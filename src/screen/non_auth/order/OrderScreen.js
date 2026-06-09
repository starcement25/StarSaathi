import React, { useEffect, useState, useCallback, useMemo, useRef } from 'react'
import { View, Text, ImageBackground, TouchableOpacity, TextInput, FlatList, StyleSheet, Platform, Animated, } from 'react-native'
import SafeView from '../../../helper/SafeView'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Icons } from '../../../assets/Icons'
import { Colors } from '../../../assets/Colors'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import Toast from 'react-native-toast-message'
import toastConfig from '../../../helper/ToastConfig'
import Loader from '../../../common/Loader'
import moment from 'moment'
import DashboardDataStorage from '../../../storage/DashboardDataStorage'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const NO_SUB = '__NO_SUB__'
const BOTTOM_BUTTON_HEIGHT = moderateScale(80)

/* ---------------- VIBRANT RED THEME ---------------- */
const THEME = {
  primary: '#FF1744',
  primaryDark: '#D50000',
  primaryLight: '#FF5252',
  secondary: '#FF4081',
  background: '#FAFAFA',
  cardBg: '#FFFFFF',
  groupBg: '#FFEBEE',
  subGroupBg: '#FFF',
  subGroupAccent: '#EF9A9A',
  border: '#FFCDD2',
  borderLight: '#FFEBEE',
  textPrimary: '#212121',
  textSecondary: '#757575',
  textLight: '#BDBDBD',
  shadow: '#000000',
  warning: '#FF6F00',
  error: '#D32F2F',
  errorBg: '#FFEBEE',
}

/* ---------------- HELPERS ---------------- */

const buildCatalogTree = (products = []) => {
  const tree = {}

  products.forEach(p => {
    const group = p.product_group
    const sub = p.product_sub_group || NO_SUB

    if (!tree[group]) {
      tree[group] = {
        expanded: false,
        subGroups: {},
      }
    }

    if (!tree[group].subGroups[sub]) {
      tree[group].subGroups[sub] = {
        expanded: false,
        products: [],
      }
    }

    tree[group].subGroups[sub].products.push({
      ...p,
      count: 0,
      hasError: false,
      errorMessage: '',
    })
  })

  const sortedTree = {}
  Object.keys(tree)
    .sort((a, b) => a.localeCompare(b))
    .forEach(group => { sortedTree[group] = tree[group] })

  return sortedTree
}

/* ---------------- MAIN SCREEN ---------------- */

const OrderScreen = (props) => {
  const regex = /^\d*\.?\d*$/;

  const [catalog, setCatalog] = useState({})
  const [expandedGroups, setExpandedGroups] = useState({})
  const [expandedSubGroups, setExpandedSubGroups] = useState({})
  const [loading, setLoading] = useState(false)
  const [date, setDate] = useState('')
  const [primaryColor, setPrimaryColor] = useState(THEME.primary)
  const [search, setSearch] = useState('')
  const [focused, setIsFocused] = useState('')
  const [productList, setProductList] = useState([])
  const [authChecker, setAuthChecker] = useState(false)

  const flatListRef = useRef(null)

  useEffect(() => {
    initializeScreen()
  }, [])

  /* ---------------- INIT ---------------- */

  const initializeScreen = useCallback(() => {
    if (DataStorage?.primaryColorCode) {
      setPrimaryColor(DataStorage.primaryColorCode)
    }
    if (DataStorage.typeOfUse == 2) {
      const empCode = UrlStorage.ParameterList.BasicData.user_type === 'broker' ? UrlStorage.ParameterList.BasicData.selectedCustomerCode : UrlStorage.ParameterList.BasicData.emp_code
      requestForCementProductList(empCode)
    } else {
      requestSbsProductList(UrlStorage.ParameterList.BasicData.selectedCustomerCode)
    }

    formatBalanceDate()
  }, [])

  /* ---------------- DATE ---------------- */

  const formatBalanceDate = () => {
    try {
      const inputDate = UrlStorage?.ParameterList?.BasicData?.ledger_balance_data?.date

      if (inputDate) {
        const [month, day, year] = inputDate.split('/')
        const d = new Date(`${year}-${month}-${day}`)
        setDate(
          d.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
          })
        )
      } else {
        setDate(moment(new Date()).format('DD-MM-YYYY'))
      }
    } catch {
      setDate(moment(new Date()).format('DD-MM-YYYY'))
    }
  }
  const checkDecimal = (value) => {
    const parts = value.split('.');

    if (parts.length > 1 && parts[1].length > 2) {
      return false; // more than 2 decimals
    }
    return true;
  };
  /* ---------------- API ---------------- */

  const requestForCementProductList = async (emp_code) => {
    setLoading(true)
    var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
    try {
      const requestOptions = {
        method: "GET",
        redirect: "follow"
      }

      var emp_code1 = UrlStorage.ParameterList.BasicData.user_type != 'broker' ? UrlStorage.ParameterList.BasicData.selectedCustomerCode : UrlStorage.ParameterList.BasicData.customerDetails.customer_code
      var user_type1 = UrlStorage.ParameterList.BasicData.user_type != 'broker' ? UrlStorage.ParameterList.BasicData.user_type : UrlStorage.ParameterList.BasicData.customerDetails.cust_type


      let url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DashboardURL.product_data_list_url
      url = url + '?emp_code=' + emp_code1 + '&user_type=' + user_type1

      const response = await fetch(url, requestOptions)
      const result = await response.json()

      if (result?.process_status === 'YES' && result?.product_date) {
        const productsWithCount = result.product_date.map((item) => ({
          ...item,
          count: 0
        }))
        setProductList(productsWithCount)
      } else {
        setProductList([])
      }
    } catch (error) {
      setProductList([])
      Toast.show({ type: 'error', text1: 'Error', text2: 'Failed to load products. Please try again.' })
    } finally {
      setLoading(false)
    }
  }

  const requestSbsProductList = async (customer_code) => {
    setLoading(true)
    var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
    try {
      const url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.order.user_product_list + '?cust_code=' + customer_code

      const res = await fetch(url)
      const json = await res.json()

      setCatalog(buildCatalogTree(Array.isArray(json) ? json : []))
    } catch {
      setCatalog({})
      Toast.show({ type: 'error', text1: 'Error', text2: 'Failed to load products. Please try again.', })
    } finally {
      setLoading(false)
    }
  }

  /* ---------------- TOGGLE ---------------- */

  const toggleGroup = useCallback((group) => {
    setExpandedGroups(prev => ({
      ...prev,
      [group]: !prev[group],
    }))
  }, [])

  const toggleSubGroup = useCallback((group, sub) => {
    const key = `${group}::${sub}`
    setExpandedSubGroups(prev => ({
      ...prev,
      [key]: !prev[key],
    }))
  }, [])

  /* ---------------- QTY WITH VALIDATION ---------------- */

  const updateQty = useCallback((group, sub, prodCode, value) => {
    setCatalog(prev => {
      const newCatalog = { ...prev }
      const products = newCatalog[group].subGroups[sub].products
      const productIndex = products.findIndex(p => p.prod_code === prodCode)

      if (productIndex !== -1) {
        const updatedProducts = [...products]
        const product = updatedProducts[productIndex]

        updatedProducts[productIndex] = {
          ...product,
          count: value,
          hasError: false,
          errorMessage: '',
        }

        newCatalog[group] = {
          ...newCatalog[group],
          subGroups: {
            ...newCatalog[group].subGroups,
            [sub]: {
              ...newCatalog[group].subGroups[sub],
              products: updatedProducts,
            },
          },
        }
      }

      return newCatalog
    })
  }, [])

  /* ---------------- SEARCH & FILTERING ---------------- */

  const filterProducts = useCallback((products, searchTerm) => {
    if (!searchTerm) return products
    return products.filter(p =>
      p.app_prod_desc?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      p.prod_desc?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      p.prod_code?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      p.dns_prod_code?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      p.product_group?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      p.product_sub_group?.toLowerCase().includes(searchTerm.toLowerCase())
    )
  }, [])

  /* ---------------- TOTAL ITEMS ---------------- */

  const totalItems = useMemo(() => {
    if (DataStorage.typeOfUse != 1) {
      return productList.filter(p => parseInt(p.count || 0) > 0).length
    }

    let count = 0
    Object.values(catalog).forEach(g =>
      Object.values(g.subGroups).forEach(s =>
        s.products.forEach(p => {
          if (p.count > 0) count += 1
        })
      )
    )
    return count
  }, [catalog, productList])

  /* ---------------- HANDLERS ---------------- */

  const incrementHandler = useCallback((index) => {
    setProductList(prevList =>
      prevList.map((item, i) =>
        i === index ? { ...item, count: parseInt(item.count || 0) + 1 } : item
      )
    )
  }, [])

  const decrementHandler = useCallback((index) => {
    setProductList(prevList =>
      prevList.map((item, i) =>
        i === index ? { ...item, count: Math.max(0, parseInt(item.count || 0) - 1) } : item
      )
    )
  }, [])

  const onChangeTextHandler = useCallback((text, index) => {
    setProductList(prevList =>
      prevList.map((item, i) =>
        i === index ? { ...item, count: text } : item
      )
    )
  }, [])

  /* ---------------- PRODUCT ROW WITH ERROR ---------------- */

  const ProductRow = React.memo(({ item, group, sub, isHighlighted }) => {
    const shakeAnim = useRef(new Animated.Value(0)).current

    useEffect(() => {
      if (item.hasError) {
        Animated.sequence([
          Animated.timing(shakeAnim, { toValue: 10, duration: 50, useNativeDriver: true }),
          Animated.timing(shakeAnim, { toValue: -10, duration: 50, useNativeDriver: true }),
          Animated.timing(shakeAnim, { toValue: 10, duration: 50, useNativeDriver: true }),
          Animated.timing(shakeAnim, { toValue: 0, duration: 50, useNativeDriver: true }),
        ]).start()
      }
    }, [item.hasError])

    return (
      <Animated.View
        style={[
          styles.productRow,
          isHighlighted && { backgroundColor: '#FFF9C4' },
          item.hasError && {
            backgroundColor: THEME.errorBg,
            borderLeftWidth: 4,
            borderLeftColor: THEME.error,
          },
          { transform: [{ translateX: shakeAnim }] }
        ]} >
        <View style={styles.productInfo}>
          <Text style={styles.productName}>{item.app_prod_desc}</Text>
          {item.min_order_qty > 0 && (
            <View style={styles.minQtyBadge}>
              <Text style={styles.minQtyText}>
                Min: {item.min_order_qty} {item.UOM1}
              </Text>
            </View>
          )}
          {item.hasError && (
            <View style={styles.errorBadge}>
              <Text style={styles.errorText}>
                <Text style={{ color: 'red', fontWeight: 'bold' }}>⚠ </Text>
                {item.errorMessage}
              </Text>
            </View>
          )}
        </View>

        <View style={styles.qtyContainer}>
          <View style={[styles.qtyBox, { borderColor: item.hasError ? THEME.error : primaryColor + '50' }]}>
            <TouchableOpacity style={styles.qtyButton} onPress={() => updateQty(group, sub, item.prod_code, Math.max(0, item.count - 1))} >
              <Text style={[styles.qtyBtn, { color: primaryColor }]}>−</Text>
            </TouchableOpacity>

            <TextInput
              style={[styles.qtyInput, item.hasError && { color: THEME.error }]}
              keyboardType='decimal-pad'
              value={item.count ? String(item.count) : ''}
              onChangeText={t => {
                if (regex.test(t) && checkDecimal(t)) {
                  updateQty(group, sub, item.prod_code, t)
                }
              }}
              placeholder="0"
              placeholderTextColor={THEME.textLight}
            />

            <TouchableOpacity style={styles.qtyButton} onPress={() => updateQty(group, sub, item.prod_code, item.count + 1)} >
              <Text style={[styles.qtyBtn, { color: primaryColor }]}>+</Text>
            </TouchableOpacity>
          </View>
          <Text style={styles.unitText}>{item.UOM1}</Text>
        </View>
      </Animated.View>
    )
  })

  const renderProductItem = useCallback(({ item, index }) => {
    const displayCount = item.count === 0 || item.count === '0' ? '' : item.count.toString()

    return (
      <View style={styles.productItemWrapper}>
        <View style={styles.productItemContainer}>
          <View style={styles.productInfoSection}>
            <Text style={styles.productName} numberOfLines={2}>
              {item.prod_desc || 'Product'}
            </Text>
            {item.min_order_qty && parseInt(item.min_order_qty) > 0 && (
              <View style={styles.minQtyBadge}>
                <Text style={styles.minQtyText}>
                  Min Qty - {item.min_order_qty}
                </Text>
              </View>
            )}
          </View>

          <View style={styles.quantitySection}>
            <TouchableOpacity activeOpacity={0.7} onPress={() => decrementHandler(index)} style={styles.quantityButton} >
              <Text style={[styles.quantityButtonText, { color: primaryColor }]}>
                -
              </Text>
            </TouchableOpacity>

            <TextInput
              value={displayCount}
              onChangeText={(text) => {
                if (regex.test(text) && checkDecimal(text)) {
                  onChangeTextHandler(text, index)
                }
              }}
              keyboardType='decimal-pad'
              placeholder="QTY"
              placeholderTextColor="#999"
              style={styles.quantityInput}
              maxLength={6}
            />

            <TouchableOpacity activeOpacity={0.7} onPress={() => incrementHandler(index)} style={styles.quantityButton} >
              <Text style={[styles.quantityButtonText, { color: primaryColor }]}>
                +
              </Text>
            </TouchableOpacity>
          </View>

          <Text style={styles.unitText}>
            {item.UOM1 || "MT"}
          </Text>
        </View>
        <View style={styles.divider} />
      </View>
    )
  }, [primaryColor, incrementHandler, decrementHandler, onChangeTextHandler])

  /* ---------------- CONTINUE WITH VALIDATION ---------------- */

  const scrollToFirstError = (firstErrorKey) => {
    // Only for SBS (typeOfUse == 1)
    const catalogArray = Object.keys(catalog)
    let foundIndex = 0

    for (let i = 0; i < catalogArray.length; i++) {
      const group = catalogArray[i]
      const subGroups = catalog[group].subGroups

      for (const sub in subGroups) {
        const products = subGroups[sub].products
        const errorProduct = products.find(p => p.hasError)

        if (errorProduct) {
          setExpandedGroups(prev => ({ ...prev, [group]: true }))
          if (sub !== NO_SUB) {
            setExpandedSubGroups(prev => ({ ...prev, [`${group}::${sub}`]: true }))
          }

          setTimeout(() => {
            flatListRef.current?.scrollToIndex({
              index: foundIndex,
              animated: true,
              viewPosition: 0.2,
            })
          }, 300)
          return
        }
      }
      foundIndex++
    }
  }

  const onContinue = () => {
    let selected = []

    if (DataStorage.typeOfUse != 1) {
      // Cement products - no validation
      selected = productList.filter(p => parseInt(p.count || 0) > 0)
    } else {
      // SBS products - validate min qty with inline errors
      const updatedCatalog = { ...catalog }
      let hasError = false
      let firstErrorKey = null
      const groupsToExpand = {}
      const subGroupsToExpand = {}

      Object.keys(updatedCatalog).forEach(group => {
        Object.keys(updatedCatalog[group].subGroups).forEach(sub => {
          const products = updatedCatalog[group].subGroups[sub].products
          let hasErrorInSubGroup = false

          products.forEach((p, idx) => {
            if (p.count > 0) {
              const minQty = p.min_order_qty || 0
              if (p.count < minQty) {
                hasError = true
                hasErrorInSubGroup = true
                products[idx] = {
                  ...p,
                  hasError: true,
                  errorMessage: `Minimum quantity is ${minQty} ${p.UOM1}`,
                }
                if (!firstErrorKey) {
                  firstErrorKey = p.prod_code
                }
                // Mark this group and subgroup to be expanded
                groupsToExpand[group] = true
                if (sub !== NO_SUB) {
                  subGroupsToExpand[`${group}::${sub}`] = true
                }
              } else {
                selected.push(p)
                products[idx] = {
                  ...p,
                  hasError: false,
                  errorMessage: '',
                }
              }
            }
          })
        })
      })

      if (hasError) {
        // Collapse all groups and subgroups first
        setExpandedGroups({})
        setExpandedSubGroups({})

        // Update catalog with errors
        setCatalog(updatedCatalog)

        // Then expand only the groups/subgroups with errors after a small delay
        setTimeout(() => {
          setExpandedGroups(groupsToExpand)
          setExpandedSubGroups(subGroupsToExpand)

          // Scroll to first error after expansion
          setTimeout(() => {
            scrollToFirstError(firstErrorKey)
          }, 300)
        }, 100)

        return
      }
    }

    if (!selected.length) {
      Toast.show({ type: 'error', text1: 'Warning', text2: 'Please add quantity first.', })
      return
    }

    DataStorage.productQtyAddedList = selected
    props.navigation.navigate('OrderDetailsScreen', {
      selectedProducts: selected,
    })
  }

  /* ---------------- RENDER CATALOG ---------------- */

  const catalogData = useMemo(() => {
    const hasSearch = search.trim().length > 0
    const result = []

    Object.keys(catalog).forEach(group => {
      const g = catalog[group]

      const isGroupExpanded = hasSearch ? true : expandedGroups[group]
      let hasMatchingInGroup = false
      const subGroupsData = []

      Object.keys(g.subGroups).forEach(sub => {
        const s = g.subGroups[sub]
        const filtered = filterProducts(s.products, search)

        if (filtered.length > 0) {
          hasMatchingInGroup = true
          const subKey = `${group}::${sub}`
          const isSubExpanded = hasSearch ? true : expandedSubGroups[subKey]

          subGroupsData.push({
            key: sub,
            sub,
            filtered,
            isSubExpanded,
          })
        }
      })

      if (hasSearch && !hasMatchingInGroup) return

      result.push({
        key: group,
        group,
        isGroupExpanded,
        subGroupsData,
        hasSearch,
      })
    })

    return result
  }, [catalog, search, expandedGroups, expandedSubGroups, filterProducts])

  const renderCatalogItem = useCallback(({ item: groupData }) => {
    const { group, isGroupExpanded, subGroupsData, hasSearch } = groupData

    return (
      <View style={styles.groupCard}>
        <TouchableOpacity onPress={() => toggleGroup(group)} style={[styles.groupHeader, { backgroundColor: isGroupExpanded ? THEME.groupBg : THEME.cardBg }]} activeOpacity={0.7} >
          <Text style={styles.groupTitle}>{group}</Text>
          <Text style={[styles.arrow, { color: primaryColor }]}>
            {isGroupExpanded ? '▼' : '▶'}
          </Text>
        </TouchableOpacity>

        {isGroupExpanded &&
          subGroupsData.map(({ key, sub, filtered, isSubExpanded }) => {
            if (sub === NO_SUB) {
              return filtered.map((p) => (
                <ProductRow
                  key={p.prod_code}
                  item={p}
                  group={group}
                  sub={sub}
                  isHighlighted={hasSearch}
                />
              ))
            }

            return (
              <View key={key}>
                <TouchableOpacity onPress={() => toggleSubGroup(group, sub)} style={[styles.subHeader, { backgroundColor: isSubExpanded ? THEME.subGroupBg : THEME.subGroupBg, borderLeftWidth: 0, borderLeftColor: primaryColor, }]} activeOpacity={0.7} >
                  <View style={styles.subTitleContainer}>
                    <View style={[styles.subDot, { backgroundColor: primaryColor }]} />
                    <Text style={[styles.subTitle, { color: isSubExpanded ? primaryColor : "#000" }]}>{sub}</Text>
                  </View>
                  <Text style={[styles.arrow, { color: primaryColor }]}>
                    {isSubExpanded ? '▼' : '▶'}
                  </Text>
                </TouchableOpacity>

                {isSubExpanded &&
                  filtered.map((p) => (
                    <ProductRow
                      key={p.prod_code}
                      item={p}
                      group={group}
                      sub={sub}
                      isHighlighted={hasSearch}
                    />
                  ))}
              </View>
            )
          })}
      </View>
    )
  }, [toggleGroup, toggleSubGroup, primaryColor])

  return (
    <SafeView backgroundColor={THEME.background} statusbarColor={Colors.main} avoidKeyboard={false}>
      <SBSCommonHeaderView title="Select Products" backPath=" " />

      <View style={styles.headerContainer}>
        <ImageBackground source={Icons.BlurBg} style={styles.headerBackground} >
          <View style={[styles.balanceCard, { borderColor: primaryColor },]} >
            <Text style={styles.balanceLabel}>Outstanding Balance</Text>
            <Text style={styles.balanceAmount}>₹ {DataStorage.typeOfUse == 1 ? Number(DashboardDataStorage.requestLedgerForSBS?.credit_expose).toFixed(2) : UrlStorage.ParameterList.BasicData.credit_details?.credit_expose}</Text>
            <Text style={styles.balanceDate}>as on {date}</Text>
          </View>
        </ImageBackground>
      </View>

      {DataStorage.typeOfUse == 1 && (catalog) && <View>
        <View style={[styles.searchContainer]}>
          <View style={[styles.searchBox, { borderColor: focused ? 'red' : THEME.borderLight }]}>
            <Text style={styles.searchIcon}>🔍</Text>
            <TextInput
              placeholder="Search products..."
              placeholderTextColor={THEME.textLight}
              value={search}
              onFocus={() => setIsFocused(true)}
              onBlur={() => setIsFocused(false)}
              onChangeText={setSearch}
              style={styles.searchInput}
            />
            {search.length > 0 && (
              <TouchableOpacity onPress={() => setSearch('')}>
                <Text style={styles.clearIcon}>✕</Text>
              </TouchableOpacity>
            )}
          </View>
        </View>

        {totalItems > 0 && (
          <View style={[styles.itemsBadge, { backgroundColor: primaryColor }]}>
            <Text style={styles.itemsBadgeText}>
              {totalItems} {totalItems === 1 ? 'item' : 'items'} selected
            </Text>
          </View>
        )}</View>}

      {catalog ? <FlatList
        ref={flatListRef}
        data={DataStorage.typeOfUse == 1 ? catalogData : productList}
        renderItem={DataStorage.typeOfUse == 1 ? renderCatalogItem : renderProductItem}
        keyExtractor={(item, index) => DataStorage.typeOfUse != 1 ? `${item.prod_code || index}` : item.key}
        keyboardShouldPersistTaps="handled"
        automaticallyAdjustKeyboardInsets={false}
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingBottom: BOTTOM_BUTTON_HEIGHT + 20, }}
        onScrollToIndexFailed={(info) => {
          const wait = new Promise(resolve => setTimeout(resolve, 500))
          wait.then(() => {
            flatListRef.current?.scrollToIndex({
              index: info.index,
              animated: true,
              viewPosition: 0.2,
            })
          })
        }}
      /> : !loading && <View style={{ flex: 0.8, alignItems: 'center', justifyContent: 'center' }}>
        <Text style={styles.productName} numberOfLines={2}>
          {'No Product Available'}
        </Text>
      </View>}

      {(catalog || productList.length > 0) && (
        <View style={styles.footer}>
          <TouchableOpacity onPress={onContinue} style={[styles.cta, { backgroundColor: totalItems > 0 ? primaryColor : THEME.textLight, elevation: totalItems > 0 ? 4 : 0, shadowOpacity: totalItems > 0 ? 0.3 : 0, },]} activeOpacity={0.8} >
            <Text style={styles.ctaText}>
              Continue {totalItems > 0 ? `(${totalItems})` : ''}
            </Text>
          </TouchableOpacity>
        </View>
      )}

      {loading && <Loader />}
      <Toast config={toastConfig} />
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  )
}

export default OrderScreen

/* ---------------- STYLES ---------------- */

const styles = StyleSheet.create({
  headerContainer: { height: moderateScale(160), marginBottom: 12, },
  headerBackground: { flex: 1, alignItems: 'center', justifyContent: 'center', },
  balanceCard: { paddingVertical: 24, paddingHorizontal: 32, backgroundColor: 'rgba(0, 0, 0, 0.5)', borderRadius: 16, borderWidth: 2, alignItems: 'center', minWidth: '80%', },
  balanceLabel: { color: '#FFF', fontSize: 12, fontWeight: '500', textTransform: 'uppercase', letterSpacing: 1, marginBottom: 8, opacity: 0.9, },
  balanceAmount: { color: '#FFF', fontSize: 32, fontWeight: '800', marginBottom: 4, },
  balanceDate: { color: '#FFF', fontSize: 13, opacity: 0.85, },
  searchContainer: { paddingHorizontal: 16, marginBottom: 12, },
  searchBox: { backgroundColor: THEME.cardBg, borderRadius: 12, height: 50, paddingHorizontal: 16, flexDirection: 'row', alignItems: 'center', shadowColor: THEME.shadow, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4, elevation: 2, borderWidth: 1.5, borderColor: THEME.borderLight, },
  searchIcon: { fontSize: 18, marginRight: 10, },
  searchInput: { flex: 1, fontSize: 15, color: THEME.textPrimary, },
  clearIcon: { fontSize: 20, color: THEME.textLight, paddingHorizontal: 8, },
  itemsBadge: { marginHorizontal: 16, marginBottom: 8, paddingVertical: 8, paddingHorizontal: 16, borderRadius: 20, alignSelf: 'flex-start', },
  itemsBadgeText: { color: '#FFF', fontSize: 13, fontWeight: '600', },
  groupCard: { backgroundColor: THEME.cardBg, borderRadius: 16, marginHorizontal: 16, marginBottom: 12, overflow: 'hidden', shadowColor: THEME.shadow, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.08, shadowRadius: 8, elevation: 3, borderWidth: 1, borderColor: THEME.borderLight, },
  groupHeader: { padding: 18, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', },
  groupTitle: { fontSize: 17, fontWeight: '700', color: THEME.textPrimary, flex: 1, },
  arrow: { fontSize: 18, fontWeight: '700', },
  subHeader: { paddingVertical: 14, paddingHorizontal: 18, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', borderTopWidth: 1, borderTopColor: THEME.border, },
  subTitleContainer: { flexDirection: 'row', alignItems: 'center', flex: 1, },
  subDot: { width: 8, height: 8, borderRadius: 4, marginRight: 10, },
  subTitle: { fontSize: 15, fontWeight: '700', color: THEME.textPrimary, },
  productRow: { flexDirection: 'row', padding: 16, borderTopWidth: 1, borderTopColor: THEME.border, alignItems: 'center', backgroundColor: THEME.cardBg, },
  productInfo: { flex: 1, marginRight: 12, },
  productName: { fontSize: 15, fontWeight: '600', color: THEME.textPrimary, marginBottom: 4, },
  minQtyBadge: { backgroundColor: '#FFF4E5', paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6, alignSelf: 'flex-start', marginTop: 4, },
  minQtyText: { fontSize: 11, color: '#F5A623', fontWeight: '600', },
  qtyContainer: { alignItems: 'center', },
  qtyBox: { flexDirection: 'row', borderWidth: 2, borderRadius: 10, alignItems: 'center', backgroundColor: '#FAFBFC', marginBottom: 4, },
  qtyButton: { paddingHorizontal: 12, paddingVertical: 8, },
  qtyBtn: { fontSize: 24, fontWeight: '700', },
  qtyInput: { width: 50, textAlign: 'center', fontSize: 16, fontWeight: '600', color: THEME.textPrimary, },
  unitText: { fontSize: 11, fontWeight: '700', color: THEME.textSecondary, textTransform: 'uppercase', letterSpacing: 0.5, },
  footer: { position: 'absolute', bottom: Platform.OS === 'ios' ? 20 : 10, left: 0, right: 0, paddingHorizontal: 20, paddingVertical: 12, backgroundColor: THEME.background, },
  cta: { height: 54, borderRadius: 14, alignItems: 'center', justifyContent: 'center', shadowColor: THEME.shadow, shadowOffset: { width: 0, height: 4 }, shadowRadius: 8, elevation: 4, },
  ctaText: { color: '#FFF', fontSize: 17, fontWeight: '700', letterSpacing: 0.5, },
  container: { flex: 1, backgroundColor: Colors.white, },
  contentWrapper: { flex: 1, },
  flatListContent: { paddingBottom: moderateScale(120), paddingHorizontal: moderateScale(0), },
  headerContainer: { width: "100%", height: moderateScale(140), marginBottom: moderateScale(20), },
  headerBackground: { width: "100%", height: "100%", alignItems: "center", justifyContent: "center", },
  headerImage: { resizeMode: "cover", },
  balanceCard: { padding: moderateScale(20), backgroundColor: "#00000042", borderRadius: moderateScale(10), borderWidth: moderateScale(1), gap: moderateScale(8), alignItems: "center", justifyContent: "center", minWidth: moderateScale(200), },
  balanceAmount: { color: "#FFFFFF", fontSize: moderateScale(24), fontWeight: "700", },
  balanceDate: { color: "#FFFFFF", fontSize: moderateScale(13), textAlign: "center", },
  productItemWrapper: { width: "100%", paddingHorizontal: moderateScale(15), },
  productItemContainer: { width: "100%", paddingVertical: moderateScale(15), flexDirection: "row", alignItems: "center", justifyContent: "space-between", },
  productInfoSection: { flex: 1, flexDirection: "column", gap: moderateScale(6), paddingRight: moderateScale(10), },
  productName: { color: Colors.text, fontSize: moderateScale(15), fontWeight: "600", },
  minQtyBadge: { backgroundColor: "#F0F0F0", alignSelf: "flex-start", paddingHorizontal: moderateScale(8), paddingVertical: moderateScale(4), borderRadius: moderateScale(4), },
  minQtyText: { fontSize: moderateScale(10), color: "#666", fontWeight: "500", },
  quantitySection: { paddingVertical: moderateScale(6), paddingHorizontal: moderateScale(12), borderRadius: moderateScale(8), borderWidth: moderateScale(1), borderColor: "#DCDDDF", flexDirection: "row", alignItems: "center", gap: moderateScale(12), minWidth: moderateScale(130), },
  quantityButton: { padding: moderateScale(4), minWidth: moderateScale(24), alignItems: "center", justifyContent: "center", },
  quantityButtonText: { fontSize: moderateScale(24), fontWeight: "600", },
  quantityInput: { color: "#333", minWidth: moderateScale(40), fontSize: moderateScale(14), textAlign: "center", padding: 0, height: moderateScale(30), },
  unitText: { color: Colors.grey, fontSize: moderateScale(14), fontWeight: "700", minWidth: moderateScale(40), textAlign: "center", marginLeft: moderateScale(8), },
  divider: { width: "100%", height: moderateScale(1), backgroundColor: "#F5F8EF", marginTop: moderateScale(10), },
  errorBadge: { marginTop: 6, },
  errorText: { fontSize: 11, color: THEME.error, fontWeight: '600', },
  /* ---------- PRODUCT ROW (animated highlight case) ---------- */
  productRowHighlighted: { backgroundColor: '#FFF9C4', },
  /* ---------- QTY CONTAINER ALIGNMENT (SBS flow) ---------- */
  qtyContainer: { alignItems: 'flex-end', },
  /* ---------- UNIT TEXT (used twice with different layouts) ---------- */
  unitTextRight: { fontSize: 11, color: THEME.textSecondary, marginTop: 4, textAlign: 'right', },
})