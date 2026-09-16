import React, { useState } from "react"
import { ActivityIndicator, Alert, Image, Linking, ScrollView, Text, TouchableOpacity, TouchableWithoutFeedback, View, StyleSheet, } from "react-native"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { CommonActions } from "@react-navigation/native"
import Modal from "react-native-modal"
import { moderateScale } from "../helper/Window"
import { Icons } from "../assets/Icons"
import DataStorage from "../storage/DataStorage"
import UrlStorage from "../storage/UrlStorage"
import DeviceInfo from "react-native-device-info"
import { launchCamera, launchImageLibrary } from "react-native-image-picker"
import { PermissionsAndroid, Platform } from "react-native"

const BRAND = "#E41B14"
const BRAND_DARK = "#B81410"
const SURFACE = "#FFFFFF"
const SURFACE_2 = "#F5F5F7"
const TEXT_PRIMARY = "#111111"
const TEXT_SECONDARY = "#666666"
const TEXT_MUTED = "#AAAAAA"
const DIVIDER = "#EEEEEE"
const GREEN = "#34C759"
const RED_BTN = "#FF3B30"
const SIDEBAR_WIDTH = "84%"

const MenuItem = ({ icon, label, onPress, accent = false }) => (
  <TouchableOpacity onPress={onPress} activeOpacity={0.65} style={styles.menuItem}>
    {icon && <View style={[styles.menuIconWrap, accent && { backgroundColor: `${BRAND}18` }]}>
      <Image source={icon} style={[styles.menuIcon, { tintColor: accent ? BRAND : TEXT_SECONDARY }]} />
    </View>}
    <Text style={[styles.menuLabel, accent && { color: BRAND, fontWeight: "700" }]}> {label} </Text>
    <Image source={Icons.ArrowRight ?? Icons.Back} style={styles.chevron} />
  </TouchableOpacity>
)

const SectionDivider = ({ label }) => (
  <View style={styles.sectionDivider}>
    <View style={styles.sectionLine} />
    {label && <Text style={styles.sectionLabel}>{label}</Text>}
    {label && <View style={styles.sectionLine} />}
  </View>
)

const SBSMenuOpenView = (props) => {
  const helpNumber = "180034534500"
  const [creditLimit, setCreditLimit] = useState("")
  const [pendingOrder, setPendingOrder] = useState("")
  const [creditBalance, setCreditBalance] = useState("")
  const [isOpenLoading, setIsOpenLoading] = useState(false)
  const [isLogoutPopup, setIsLogoutPopup] = useState(false)
  const [isOpenIssueSharePopup, setIsOpenIssueSharePopup] = useState(false)
  const [isOpenCreditLimitPopup, setIsOpenCreditLimitPopup] = useState(false)
  const [profileImageUri, setProfileImageUri] = useState(
    UrlStorage.ParameterList.BasicData.image_url || null
  )
  const [isImagePickerPopup, setIsImagePickerPopup] = useState(false)

  const handleHelpPress = async () => {
    setIsOpenIssueSharePopup(false)
    await Linking.openURL(`tel:${helpNumber}`)
  }

  const handleHelpMail = async () => {
    setIsOpenIssueSharePopup(false)
    const email = "customercare@starcement.co.in"
    const subject = "Need Help"
    const body = "Hi, I need help with..."
    await Linking.openURL(
      `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`
    )
  }

  const requestForCreditLimit = () => {
    setIsOpenLoading(true)
    const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.CreditLimitURL.balance_details_url + "?the_id=" + UrlStorage.ParameterList.BasicData.emp_code
    fetch(url, { method: "GET", redirect: "follow" })
      .then((r) => r.json())
      .then((result) => {
        setCreditLimit(result?.credit_limit)
        setPendingOrder(result?.pending_orders)
        setCreditBalance(result?.current_balance)
        setIsOpenCreditLimitPopup(true)
        setIsOpenLoading(false)
      })
      .catch(() => setIsOpenLoading(false))
  }

  const fetchHTML = async () => {
    try {
      const response = await fetch("https://starsaathiapi.myvtd.site/SAP/weblink/about")
      return await response.json()
    } catch {
      return null
    }
  }

  const isUpdateAvailable = (current = "0.0.0", latest = "0.0.0") => {
    if (!latest) return false
    const cur = current.split(".").map(Number)
    const lat = latest.split(".").map(Number)
    for (let i = 0; i < Math.max(cur.length, lat.length); i++) {
      const c = cur[i] || 0
      const l = lat[i] || 0
      if (c < l) return true
      if (c > l) return false
    }
    return false
  }

  const checkAppUpdate = () => {
    const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DashboardURL.app_version_check_url
    fetch(url, { method: "GET", redirect: "follow" })
      .then((r) => r.json())
      .then((result) => {
        if (isUpdateAvailable(DeviceInfo.getVersion(), result?.android_app_version)) {
          Alert.alert(
            "Update Available",
            "A newer version is available. Please update to continue.",
            [
              { text: "Later", style: "cancel" },
              { text: "Update Now", onPress: () => { } },
            ],
            { cancelable: false }
          )
        } else {
          props.navigation.navigate("HomeSliderScreen")
        }
      })
      .catch(e => {
      })
  }

  const logoutAccount = async () => {
    await AsyncStorage.setItem("is_login", "0")
    await AsyncStorage.setItem("user_info", "")
    await AsyncStorage.setItem("user_details_dealerId", "")
    await AsyncStorage.setItem("user_details_mobileNumber", "")
    props.handleOpenSBSMenu()
    props.navigation.dispatch(
      CommonActions.reset({
        index: 0,
        routes: [{ name: "LoginScreen" }],
      })
    )
  }

  const requestCameraPermission = async () => {
    if (Platform.OS !== "android") return true
    try {
      const granted = await PermissionsAndroid.request(
        PermissionsAndroid.PERMISSIONS.CAMERA,
        {
          title: "Camera Permission",
          message: "App needs camera access to take a profile photo.",
          buttonNeutral: "Ask Me Later",
          buttonNegative: "Cancel",
          buttonPositive: "OK",
        }
      )
      return granted === PermissionsAndroid.RESULTS.GRANTED
    } catch {
      return false
    }
  }

  const requestGalleryPermission = async () => {
    if (Platform.OS !== "android") return true
    try {
      const permission = Platform.Version >= 33 ? PermissionsAndroid.PERMISSIONS.READ_MEDIA_IMAGES : PermissionsAndroid.PERMISSIONS.READ_EXTERNAL_STORAGE
      const granted = await PermissionsAndroid.request(permission, {
        title: "Gallery Permission",
        message: "App needs gallery access to select a profile photo.",
        buttonNeutral: "Ask Me Later",
        buttonNegative: "Cancel",
        buttonPositive: "OK",
      })
      return granted === PermissionsAndroid.RESULTS.GRANTED
    } catch {
      return false
    }
  }

  const showPermissionDeniedAlert = (type) => {
    Alert.alert(
      "Permission Required",
      `${type} access is needed to update your profile photo. Enable it in App Settings.`,
      [
        { text: "Not Now", style: "cancel" },
        { text: "Open Settings", onPress: () => Linking.openSettings() },
      ]
    )
  }

  const openCamera = async () => {
    setIsImagePickerPopup(false)
    if (Platform.OS === "android") {
      const hasPermission = await requestCameraPermission()
      if (!hasPermission) return showPermissionDeniedAlert("Camera")
    }
    setTimeout(() => {
      launchCamera(
        { mediaType: "photo", cameraType: "back", quality: 0.8, saveToPhotos: false },
        (response) => {
          if (response.didCancel) return
          if (response.errorCode) {
            if (response.errorCode === "permission") showPermissionDeniedAlert("Camera")
            return
          }
          if (response.assets?.[0]) uploadProfileImage(response.assets[0])
        }
      )
    }, 400)
  }

  const openGallery = async () => {
    setIsImagePickerPopup(false)
    if (Platform.OS === "android" && Platform.Version <= 32) {
      const hasPermission = await requestGalleryPermission()
      if (!hasPermission) return showPermissionDeniedAlert("Gallery")
    }
    setTimeout(() => {
      launchImageLibrary(
        { mediaType: "photo", quality: 0.8 },
        (response) => {
          if (response.didCancel) return
          if (response.errorCode) {
            if (response.errorCode === "permission") showPermissionDeniedAlert("Gallery")
            return
          }
          if (response.assets?.[0]) uploadProfileImage(response.assets[0])
        }
      )
    }, 400)
  }

  const uploadProfileImage = async (asset) => {
    setIsOpenLoading(true)
    try {
      const formData = new FormData()
      formData.append("profile_image", {
        uri: asset.uri,
        type: asset.type || "image/jpeg",
        name: asset.fileName || "profile.jpg",
      })
      formData.append("the_id", UrlStorage.ParameterList.BasicData.emp_code)
      formData.append("user_type", UrlStorage.ParameterList.BasicData.user_type)
      const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DashboardURL.update_image
      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "multipart/form-data" },
        body: formData,
      })
      const result = await response.json()
      if (result?.process_status?.toLowerCase() === "yes") {
        setProfileImageUri(result.the_profile_image_url)
        DataStorage.image_url = result.the_profile_image_url
        Alert.alert("Success", result?.process_message || "Profile updated!")
      } else {
        Alert.alert("Failed", result?.process_message || "Upload failed.")
      }
    } catch (error) {
      Alert.alert("Error", "Something went wrong while uploading.")
    } finally {
      setIsOpenLoading(false)
    }
  }

  const isBrokerOrDealer = UrlStorage.ParameterList.BasicData.user_type === "broker" || UrlStorage.ParameterList.BasicData.user_type?.toLowerCase() === "dealer"

  const empName = UrlStorage.ParameterList.BasicData.customerDetails?.emp_name ?? ""
  const userType = UrlStorage.ParameterList.BasicData.user_type ?? ""

  return (
    <Modal
      isVisible={props.isVisible}
      style={{ margin: 0 }}
      animationIn="fadeInLeft"
      animationOut="fadeOutLeft"
      customBackdrop={
        <TouchableWithoutFeedback onPress={props.handleOpenSBSMenu}>
          <View style={{ flex: 1, backgroundColor: "rgba(0,0,0,0.5)" }} />
        </TouchableWithoutFeedback>
      }
    >
      <View style={styles.sidebar}>
        <View style={styles.header}>
          <View style={styles.headerTopBar}>
            <TouchableOpacity onPress={props.handleOpenSBSMenu} style={styles.backBtn} activeOpacity={0.8} >
              <Image source={Icons.Back} style={styles.backIcon} />
            </TouchableOpacity>
            <Text style={styles.headerTitle}>Menu</Text>
            <View style={{ width: moderateScale(36) }} />
          </View>
          <View style={styles.avatarSection}>
            <View style={styles.avatarContainer}>
              <Image source={profileImageUri ? { uri: profileImageUri } : Icons.ProfileImage} style={styles.avatarImage} />
              <TouchableOpacity onPress={() => setIsImagePickerPopup(true)} style={styles.cameraBadge} activeOpacity={0.85} >
                <Image source={Icons.Camera} style={styles.cameraIcon} />
              </TouchableOpacity>
            </View>
            <Text style={styles.userName} numberOfLines={2}>{empName}</Text>
            {userType ? <View style={styles.rolePill}>
              <Text style={styles.roleText}>
                {userType.charAt(0).toUpperCase() + userType.slice(1)}
              </Text>
            </View> : null}
          </View>
        </View>
        <ScrollView style={styles.scrollView} contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false} bounces={true} >
          <SectionDivider label="GENERAL" />
          <MenuItem
            icon={Icons.AboutUsIcon}
            label="About Us"
            onPress={async () => {
              props.handleOpenSBSMenu()
              if (DataStorage.typeOfUse != 1) {
                DataStorage.select_type_of_option = "about_us_1"
                props.navigation.navigate("OtherScreen")
              } else {
                DataStorage.web_page_title = "About Us"
                DataStorage.web_link = await fetchHTML()
                props.navigation.navigate("WebLinkScreen")
              }
            }}
          />
          <MenuItem
            icon={Icons.CallIcon}
            label="Help"
            onPress={() => {
              props.handleOpenSBSMenu()
              handleHelpPress()
            }}
          />
          <MenuItem
            icon={Icons.KYCIcon}
            label="KYC"
            onPress={() => {
              props.handleOpenSBSMenu()
              props.navigation.navigate("KycScreen")
            }}
          />
          <MenuItem
            icon={Icons.IssueIcon}
            label="Issues"
            onPress={() => setIsOpenIssueSharePopup(true)}
          />
          <SectionDivider label="LEGAL" />
          <MenuItem
            icon={Icons.TermsIcon}
            label="Terms & Conditions"
            onPress={() => {
              props.handleOpenSBSMenu()
              DataStorage.select_type_of_option = "t_and_c"
              props.navigation.navigate("OtherScreen")
            }}
          />
          <MenuItem
            icon={Icons.PrivacyIcon}
            label="Privacy Policy"
            onPress={() => {
              props.handleOpenSBSMenu()
              DataStorage.select_type_of_option = DataStorage.typeOfUse == 1 || DataStorage.typeOfUse == 2 ? "p_p_1" : "p_p"
              props.navigation.navigate("OtherScreen")
            }}
          />
          <MenuItem
            icon={Icons.RefundIcon}
            label="Refund Policy"
            onPress={() => {
              props.handleOpenSBSMenu()
              DataStorage.select_type_of_option = DataStorage.typeOfUse == 1 ? "r_p_1" : "r_p"
              props.navigation.navigate("OtherScreen")
            }}
          />

          {isBrokerOrDealer && DataStorage.typeOfUse != 1 && (
            <>
              <SectionDivider label="ACCOUNT" />

              <MenuItem
                icon={Icons.PaymentHistoryIcon}
                label="App Payment History"
                onPress={() => {
                  props.handleOpenSBSMenu()
                  if (DataStorage.typeOfUse != 1) {
                    DataStorage.web_page_title = "App Payment History"
                    DataStorage.web_link = UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/weblink/my_payment_history_weblink?the_id=" + UrlStorage.ParameterList.BasicData.emp_code
                    props.navigation.navigate("WebLinkScreen")
                  }
                }}
              />
              {UrlStorage.ParameterList.BasicData.user_type.toLowerCase() === 'dealer' && <MenuItem
                icon={Icons.DealerIcon}
                label="Exclusive Dealer Declaration"
                onPress={() => {
                  props.handleOpenSBSMenu()
                  props.navigation.navigate("DealerDeclarationScreen")
                }}
              />}
              {UrlStorage.ParameterList.BasicData.user_type.toLowerCase() === 'dealer' && <MenuItem
                icon={Icons.DealerIcon}
                label="Exclusive Declaration History"
                onPress={() => {
                  props.handleOpenSBSMenu()
                  props.navigation.navigate("DealerDeclarationHistoryScreen")
                }}
              />}
              {DataStorage.typeOfUse != 1 && (
                <MenuItem
                  icon={Icons.SchemeIcon}
                  label="Consumer Scheme"
                  onPress={() => {
                    props.handleOpenSBSMenu()
                    if (DataStorage.typeOfUse != 1) {
                      DataStorage.web_page_title = "Consumer Scheme"
                      DataStorage.web_link = UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/arc_offer/index?login_user_id=" + UrlStorage.ParameterList.BasicData.emp_id + "&user_type=" + UrlStorage.ParameterList.BasicData.user_type
                      props.navigation.navigate("WebLinkScreen")
                    }
                  }}
                />
              )}
              <MenuItem
                icon={Icons.CreditIcon}
                label="Credit Limit"
                onPress={() => {
                  if (DataStorage.typeOfUse != 1) requestForCreditLimit()
                }}
              />

              {UrlStorage.ParameterList.BasicData.user_type === "broker" &&
                UrlStorage.ParameterList.BasicData.selectedCustomerCode !== "" && (
                  <MenuItem
                    icon={Icons.KismatIcon}
                    label="Consumer Lottery Scheme"
                    onPress={() => {
                      props.handleOpenSBSMenu()
                      checkAppUpdate()
                    }}
                  />
                )}
            </>
          )}

          {DataStorage.isBoriMenuShow && DataStorage.typeOfUse != 1 && (
            <MenuItem
              icon={Icons.KismatIcon}
              label="Kismet Ki Bori"
              onPress={() => {
                props.handleOpenSBSMenu()
                props.navigation.navigate("KismatKiBoriScreen")
              }}
            />
          )}

          <View style={styles.bottomSpacing} />

          <TouchableOpacity onPress={() => setIsLogoutPopup(true)} style={styles.logoutBtn} activeOpacity={0.8} >
            <Image source={Icons.Back} style={[styles.menuIcon, { tintColor: RED_BTN }]} />
            <Text style={styles.logoutText}>Log Out</Text>
          </TouchableOpacity>

          <View style={styles.bottomSpacing} />
        </ScrollView>

        <View style={styles.footer}>
          <Text style={styles.versionText}>Version {DeviceInfo.getVersion()}</Text>

          {DataStorage.isSbsRegister && DataStorage.isCementRegister ? (
            <TouchableOpacity
              onPress={() => {
                props.handleOpenSBSMenu()
                setTimeout(() => {
                  props.navigation.dispatch(
                    CommonActions.reset({ index: 0, routes: [{ name: "HomeScreen" }] })
                  )
                }, 1000)
                if (UrlStorage.ParameterList.BasicData.user_type == "broker") {
                  UrlStorage.ParameterList.BasicData.selectedCustomerCode = ""
                  UrlStorage.ParameterList.BasicData.customerDetails = ""
                }
              }}
              style={[styles.switchBtn, { backgroundColor: DataStorage.primaryColorCode || BRAND }]}
              activeOpacity={0.85}
            >
              <Image source={Icons.SwitchBusiness} resizeMode="contain" style={styles.switchIcon} />
              <Text style={styles.switchText}>Switch Business</Text>
            </TouchableOpacity>
          ) : null}
        </View>
      </View>

      <Modal
        isVisible={isOpenIssueSharePopup}
        customBackdrop={
          <TouchableWithoutFeedback onPress={() => setIsOpenIssueSharePopup(false)}>
            <View style={{ flex: 1, backgroundColor: "rgba(0,0,0,0.4)" }} />
          </TouchableWithoutFeedback>
        }
      >
        <View style={styles.card}>
          <Text style={styles.cardTitle}>How can we help?</Text>
          <Text style={styles.cardSubtitle}>Choose a way to reach our support team</Text>
          <View style={{ height: moderateScale(20) }} />
          <View style={styles.issueRow}>
            <TouchableOpacity onPress={handleHelpPress} style={styles.issueBtn} activeOpacity={0.8}>
              <View style={[styles.issueBtnIcon, { backgroundColor: `${BRAND}15` }]}>
                <Image source={Icons.CallIcon} style={[styles.issueBtnIconImg, { tintColor: BRAND }]} />
              </View>
              <Text style={styles.issueBtnLabel}>Call Us</Text>
            </TouchableOpacity>
            <TouchableOpacity onPress={handleHelpMail} style={styles.issueBtn} activeOpacity={0.8}>
              <View style={[styles.issueBtnIcon, { backgroundColor: "#1A73E815" }]}>
                <Image source={Icons.MailIcon} style={[styles.issueBtnIconImg, { tintColor: "#1A73E8" }]} />
              </View>
              <Text style={styles.issueBtnLabel}>Mail Us</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>

      <Modal
        isVisible={isOpenCreditLimitPopup}
        customBackdrop={
          <TouchableWithoutFeedback onPress={() => setIsOpenCreditLimitPopup(false)}>
            <View style={{ flex: 1, backgroundColor: "rgba(0,0,0,0.4)" }} />
          </TouchableWithoutFeedback>
        }
      >
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Credit Limit</Text>
          <View style={{ height: moderateScale(16) }} />
          {[
            { label: "Credit Limit", value: creditLimit },
            { label: "Pending Orders", value: pendingOrder },
            { label: "Credit Balance", value: creditBalance },
          ].map((row, i) => (
            <View key={i}>
              <View style={styles.creditRow}>
                <Text style={styles.creditLabel}>{row.label}</Text>
                <Text style={styles.creditValue}>{row.value}</Text>
              </View>
              {i < 2 && <View style={styles.creditDivider} />}
            </View>
          ))}
        </View>
      </Modal>

      <Modal
        isVisible={isLogoutPopup}
        customBackdrop={
          <TouchableWithoutFeedback onPress={() => setIsLogoutPopup(false)}>
            <View style={{ flex: 1, backgroundColor: "rgba(0,0,0,0.4)" }} />
          </TouchableWithoutFeedback>
        }
      >
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Log out?</Text>
          <Text style={styles.cardSubtitle}>
            You'll need to sign in again to access your account.
          </Text>
          <View style={{ height: moderateScale(24) }} />
          <View style={styles.dialogBtns}>
            <TouchableOpacity onPress={() => setIsLogoutPopup(false)} style={[styles.dialogBtn, styles.dialogBtnCancel]} activeOpacity={0.8} >
              <Text style={styles.dialogBtnCancelText}>Cancel</Text>
            </TouchableOpacity>
            <TouchableOpacity onPress={logoutAccount} style={[styles.dialogBtn, styles.dialogBtnConfirm]} activeOpacity={0.8} >
              <Text style={styles.dialogBtnConfirmText}>Log Out</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>

      <Modal
        isVisible={isImagePickerPopup}
        style={{ justifyContent: "flex-end", margin: 0 }}
        customBackdrop={
          <TouchableWithoutFeedback onPress={() => setIsImagePickerPopup(false)}>
            <View style={{ flex: 1, backgroundColor: "rgba(0,0,0,0.4)" }} />
          </TouchableWithoutFeedback>
        } >
        <View style={styles.sheetContainer}>
          <View style={styles.sheetHandle} />
          <Text style={styles.sheetTitle}>Update Profile Photo</Text>
          <View style={{ height: moderateScale(16) }} />

          <TouchableOpacity onPress={openCamera} style={styles.sheetItem} activeOpacity={0.7}>
            <View style={[styles.sheetItemIcon, { backgroundColor: `${BRAND}12` }]}>
              <Image source={Icons.Camera} style={[styles.sheetItemIconImg, { tintColor: BRAND }]} />
            </View>
            <View>
              <Text style={styles.sheetItemLabel}>Take a Photo</Text>
              <Text style={styles.sheetItemSub}>Use your camera</Text>
            </View>
          </TouchableOpacity>

          <View style={styles.sheetDivider} />

          <TouchableOpacity onPress={openGallery} style={styles.sheetItem} activeOpacity={0.7}>
            <View style={[styles.sheetItemIcon, { backgroundColor: "#34C75912" }]}>
              <Image source={Icons.GalleryIcon} style={[styles.sheetItemIconImg, { tintColor: GREEN }]} />
            </View>
            <View>
              <Text style={styles.sheetItemLabel}>Choose from Gallery</Text>
              <Text style={styles.sheetItemSub}>Pick an existing photo</Text>
            </View>
          </TouchableOpacity>

          <View style={{ height: moderateScale(16) }} />

          <TouchableOpacity onPress={() => setIsImagePickerPopup(false)} style={styles.sheetCancelBtn} activeOpacity={0.8} >
            <Text style={styles.sheetCancelText}>Cancel</Text>
          </TouchableOpacity>
          <View style={{ height: moderateScale(8) }} />
        </View>
      </Modal>

      <Modal
        isVisible={isOpenLoading}
        customBackdrop={
          <TouchableWithoutFeedback onPress={() => { }}>
            <View style={{ flex: 1, backgroundColor: "rgba(0,0,0,0.35)" }} />
          </TouchableWithoutFeedback>
        } >
        <View style={styles.loadingBox}>
          <ActivityIndicator size="large" color={BRAND} />
          <Text style={styles.loadingText}>Please wait...</Text>
        </View>
      </Modal>
    </Modal>
  )
}

const styles = StyleSheet.create({
  sidebar: { position: "absolute", top: 0, bottom: 0, left: 0, width: SIDEBAR_WIDTH, backgroundColor: SURFACE, flexDirection: "column", shadowColor: "#000", shadowOffset: { width: 4, height: 0 }, shadowOpacity: 0.15, shadowRadius: 12, elevation: 8, },
  header: { backgroundColor: BRAND, paddingBottom: moderateScale(24), paddingTop: moderateScale(20), },
  headerTopBar: { flexDirection: "row", alignItems: "center", justifyContent: "space-between", paddingHorizontal: moderateScale(16), paddingTop: moderateScale(16), paddingBottom: moderateScale(8), },
  backBtn: { width: moderateScale(36), height: moderateScale(36), borderRadius: moderateScale(18), backgroundColor: "rgba(255,255,255,0.15)", alignItems: "center", justifyContent: "center", },
  backIcon: { width: moderateScale(12), height: moderateScale(12), tintColor: "#FFFFFF" },
  headerTitle: { color: "#FFFFFF", fontSize: moderateScale(17), fontWeight: "700", letterSpacing: 0.5, },

  avatarSection: { alignItems: "center", paddingTop: moderateScale(8) },
  avatarContainer: { position: "relative", width: moderateScale(90), height: moderateScale(90), },
  avatarImage: { width: moderateScale(90), height: moderateScale(90), borderRadius: moderateScale(45), borderWidth: moderateScale(3), borderColor: "rgba(255,255,255,0.6)", resizeMode: "cover", backgroundColor: BRAND_DARK, },
  cameraBadge: { position: "absolute", bottom: 0, right: 0, width: moderateScale(28), height: moderateScale(28), borderRadius: moderateScale(14), backgroundColor: SURFACE, alignItems: "center", justifyContent: "center", shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, elevation: 4, },
  cameraIcon: { width: moderateScale(14), height: moderateScale(14), tintColor: BRAND },
  userName: { marginTop: moderateScale(10), fontSize: moderateScale(16), fontWeight: "700", color: "#FFFFFF", textAlign: "center", maxWidth: moderateScale(200), },
  rolePill: { marginTop: moderateScale(6), backgroundColor: "rgba(255,255,255,0.2)", paddingHorizontal: moderateScale(12), paddingVertical: moderateScale(3), borderRadius: moderateScale(20), },
  roleText: { fontSize: moderateScale(11), color: "rgba(255,255,255,0.9)", fontWeight: "600", letterSpacing: 0.5, },

  scrollView: { flex: 1 },
  scrollContent: { paddingTop: moderateScale(8), paddingBottom: moderateScale(8) },
  sectionDivider: { flexDirection: "row", alignItems: "center", paddingHorizontal: moderateScale(16), marginTop: moderateScale(16), marginBottom: moderateScale(4), },
  sectionLine: { flex: 1, height: 1, backgroundColor: DIVIDER },
  sectionLabel: { fontSize: moderateScale(10), fontWeight: "700", color: TEXT_MUTED, letterSpacing: 1.2, marginHorizontal: moderateScale(8), },
  menuItem: { flexDirection: "row", alignItems: "center", paddingHorizontal: moderateScale(16), paddingVertical: moderateScale(12), },
  menuIconWrap: { width: moderateScale(36), height: moderateScale(36), borderRadius: moderateScale(10), backgroundColor: SURFACE_2, alignItems: "center", justifyContent: "center", marginRight: moderateScale(12), },
  menuIcon: { width: moderateScale(16), height: moderateScale(16), resizeMode: "contain" },
  menuLabel: { flex: 1, fontSize: moderateScale(14), fontWeight: "500", color: TEXT_PRIMARY },
  chevron: { width: moderateScale(8), height: moderateScale(8), tintColor: TEXT_MUTED, resizeMode: "contain", transform: [{ rotate: "180deg" }], },
  bottomSpacing: { height: moderateScale(16) },
  logoutBtn: { flexDirection: "row", alignItems: "center", paddingHorizontal: moderateScale(16), paddingVertical: moderateScale(12), marginHorizontal: moderateScale(12), marginBottom: moderateScale(8), backgroundColor: "#FF3B3008", borderRadius: moderateScale(10), },
  logoutText: { marginLeft: moderateScale(12), fontSize: moderateScale(14), fontWeight: "600", color: RED_BTN, },
  footer: { paddingHorizontal: moderateScale(16), paddingBottom: moderateScale(20), borderTopWidth: 1, borderTopColor: DIVIDER, paddingTop: moderateScale(12), alignItems: "center", },
  versionText: { fontSize: moderateScale(11), color: TEXT_MUTED, marginBottom: moderateScale(10), },
  switchBtn: { width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "center", paddingVertical: moderateScale(10), borderRadius: moderateScale(12), gap: moderateScale(8), },
  switchIcon: { width: moderateScale(20), height: moderateScale(20), tintColor: "#FFFFFF", },
  switchText: { color: "#FFFFFF", fontSize: moderateScale(14), fontWeight: "700", letterSpacing: 0.3, },

  card: { backgroundColor: SURFACE, marginHorizontal: moderateScale(16), borderRadius: moderateScale(16), padding: moderateScale(24), shadowColor: "#000", shadowOffset: { width: 0, height: 8 }, shadowOpacity: 0.1, shadowRadius: 20, elevation: 8, },
  cardTitle: { fontSize: moderateScale(18), fontWeight: "700", color: TEXT_PRIMARY },
  cardSubtitle: { marginTop: moderateScale(4), fontSize: moderateScale(13), color: TEXT_SECONDARY, lineHeight: moderateScale(18), },
  issueRow: { flexDirection: "row", justifyContent: "space-around" },
  issueBtn: { flex: 1, alignItems: "center", marginHorizontal: moderateScale(6), paddingVertical: moderateScale(16), borderRadius: moderateScale(12), borderWidth: 1, borderColor: DIVIDER, },
  issueBtnIcon: { width: moderateScale(48), height: moderateScale(48), borderRadius: moderateScale(24), alignItems: "center", justifyContent: "center", marginBottom: moderateScale(8), },
  issueBtnIconImg: { width: moderateScale(22), height: moderateScale(22), resizeMode: "contain" },
  issueBtnLabel: { fontSize: moderateScale(13), fontWeight: "600", color: TEXT_PRIMARY },

  creditRow: { flexDirection: "row", alignItems: "center", justifyContent: "space-between", paddingVertical: moderateScale(10), },
  creditLabel: { fontSize: moderateScale(13), color: TEXT_SECONDARY },
  creditValue: { fontSize: moderateScale(15), fontWeight: "700", color: TEXT_PRIMARY },
  creditDivider: { height: 1, backgroundColor: DIVIDER },
  dialogBtns: { flexDirection: "row", gap: moderateScale(12) },
  dialogBtn: { flex: 1, paddingVertical: moderateScale(12), borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center", },
  dialogBtnCancel: { backgroundColor: SURFACE_2 },
  dialogBtnCancelText: { fontSize: moderateScale(14), fontWeight: "600", color: TEXT_SECONDARY },
  dialogBtnConfirm: { backgroundColor: RED_BTN },
  dialogBtnConfirmText: { fontSize: moderateScale(14), fontWeight: "700", color: "#FFFFFF" },
  sheetContainer: { backgroundColor: SURFACE, borderTopLeftRadius: moderateScale(20), borderTopRightRadius: moderateScale(20), paddingHorizontal: moderateScale(20), paddingTop: moderateScale(12), shadowColor: "#000", shadowOffset: { width: 0, height: -4 }, shadowOpacity: 0.08, shadowRadius: 12, elevation: 8, },
  sheetHandle: { width: moderateScale(36), height: moderateScale(4), borderRadius: moderateScale(2), backgroundColor: DIVIDER, alignSelf: "center", marginBottom: moderateScale(16), },
  sheetTitle: { fontSize: moderateScale(16), fontWeight: "700", color: TEXT_PRIMARY },
  sheetItem: { flexDirection: "row", alignItems: "center", paddingVertical: moderateScale(14), gap: moderateScale(14), },
  sheetItemIcon: { width: moderateScale(46), height: moderateScale(46), borderRadius: moderateScale(14), alignItems: "center", justifyContent: "center", },
  sheetItemIconImg: { width: moderateScale(22), height: moderateScale(22), resizeMode: "contain" },
  sheetItemLabel: { fontSize: moderateScale(14), fontWeight: "600", color: TEXT_PRIMARY },
  sheetItemSub: { fontSize: moderateScale(12), color: TEXT_MUTED, marginTop: moderateScale(2) },
  sheetDivider: { height: 1, backgroundColor: DIVIDER },
  sheetCancelBtn: { alignItems: "center", paddingVertical: moderateScale(12), borderRadius: moderateScale(10), backgroundColor: SURFACE_2, },
  sheetCancelText: { fontSize: moderateScale(14), fontWeight: "600", color: RED_BTN },

  loadingBox: { backgroundColor: SURFACE, borderRadius: moderateScale(16), padding: moderateScale(32), alignItems: "center", alignSelf: "center", gap: moderateScale(12), shadowColor: "#000", shadowOffset: { width: 0, height: 8 }, shadowOpacity: 0.12, shadowRadius: 20, elevation: 10, },
  loadingText: { fontSize: moderateScale(13), color: TEXT_SECONDARY },
})

export default SBSMenuOpenView