import React, { useEffect, useState } from 'react'
import { FlatList, Image, ImageBackground, Text, TouchableOpacity, TouchableWithoutFeedback, View } from 'react-native'
import Modal from 'react-native-modal'
import DateTimePicker from '@react-native-community/datetimepicker'
import SafeView from '../../../helper/SafeView'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Icons } from '../../../assets/Icons'
import { Colors } from '../../../assets/Colors'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import Toast from 'react-native-toast-message'
import toastConfig from '../../../helper/ToastConfig'
import moment from 'moment'
import Loader from '../../../common/Loader'
import RNFetchBlob from 'react-native-blob-util'
import DashboardDataStorage from '../../../storage/DashboardDataStorage'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const LedgerScreen = (props) => {
  const [ledgerList, setLedgerList] = useState([])
  const [ledgerBalanceData, setLedgerBalanceData] = useState({})

  const [isVisiblePopup, setIsVisiblePopup] = useState(false)
  const [loading, setLoading] = useState(true)
  const [showDetailsItem, setShowDetailsItem] = useState({})

  const [showDateSelector, setShowDateSelector] = useState(false)
  const [authChecker, setAuthChecker] = useState(false)
  const [startDate, setStartDate] = useState(() => {
    const date = new Date()
    date.setMonth(date.getMonth() - 1)
    return date
  })
  const [endDate, setEndDate] = useState(() => new Date())
  const [showStartDatePicker, setShowStartDatePicker] = useState(false)
  const [showEndDatePicker, setShowEndDatePicker] = useState(false)

  useEffect(() => {
    DataStorage.typeOfUse === 1 ? requestForLedgerListSBS() : requestForLedgerList()
  }, [])


  const gotoLink = () => {
    DataStorage.web_page_title = "Details"
    DataStorage.web_link = 'https://starsaathi.com/SAP/dashboard/'
    props.navigation.navigate('WebLinkScreen')
  }

  const requestForLedgerList = async () => {
    //setLoading(true)
    var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
    const requestOptions = {
      method: "GET",
      redirect: "follow"
    };
    var url = ""

    if (UrlStorage.ParameterList.BasicData.user_type == "broker") {
      url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.LedgerURL.other_ledger_list_url + "?the_id=" + UrlStorage.ParameterList.BasicData.customerDetails.customer_code
    } else if (UrlStorage.ParameterList.BasicData.user_type.toLowerCase() == "dealer") {
      url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.LedgerURL.other_ledger_list_url + "?the_id=" + UrlStorage.ParameterList.BasicData.emp_code
    } else {
      url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.LedgerURL.dealer_ledger_list_url + "?the_id=" + UrlStorage.ParameterList.BasicData.emp_code + "&user_type=rssd"
    }

    try {
      const response = await fetch(url, requestOptions)

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const rawText = await response.text()

      const result = JSON.parse(rawText)

      if (result.process_status == 'YES') {

        if (UrlStorage.ParameterList.BasicData.user_type == "broker" || UrlStorage.ParameterList.BasicData.user_type.toLowerCase() == "dealer") {
          setLedgerList(result.ledger_data)
          setLedgerBalanceData(result.ledger_balance_data)
        } else {
          const sorted = result.ledger_data.sort((a, b) => {
            const [monthA, dayA, yearA] = a.voucher_date.split("/").map(Number);
            const [monthB, dayB, yearB] = b.voucher_date.split("/").map(Number);

            const dateA = new Date(yearA, monthA - 1, dayA);
            const dateB = new Date(yearB, monthB - 1, dayB);

            return dateB - dateA; // newest first
          });

          setLedgerList(sorted)
          setLedgerBalanceData(result.ledger_balance_data)
        }
      } else {
        Toast.show({ type: 'error', text1: 'Sorry', text2: result.process_message })
      }
      setLoading(false)

    } catch (error) {

      // Check if it's a JSON parsing error
      if (error.name === 'SyntaxError') {
        Toast.show({ type: 'error', text1: 'Error', text2: 'Invalid response format from server' })
      } else {
        Toast.show({ type: 'error', text1: 'Error', text2: 'Failed to fetch ledger data' })
      }
      setLoading(false)
    }
  }

  //=======SBS Ledger ======
  const requestForLedgerListSBS = async () => {
    var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
    const requestOptions = {
      method: "GET",
      redirect: "follow",
      headers: { "Authorization": UrlStorage.ParameterList.BasicData.user_type == 'broker' ? `SAP_SP` : `SAP_DEALER` + `${UrlStorage.ParameterList.BasicData.emp_id}`, "Content-Type": "application/json", },
    };
    var url = ""

    if (UrlStorage.ParameterList.BasicData.user_type == "broker") {
      url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.Ledger.ledger_list + `?KUNNR=${UrlStorage.ParameterList.BasicData.selectedCustomerCode}`
    } else {
      url = url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.Ledger.ledger_list + `?KUNNR=${UrlStorage.ParameterList.BasicData.emp_id}`
    }

    try {
      const response = await fetch(url, requestOptions,)

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const result = await response.json()

      if (result.length > 0) {
        setLedgerList(result.reverse())
        //setLedgerBalanceData(result.ledger_balance_data)
      } else {

      }
      setLoading(false)

    } catch (error) {
      if (error.name === 'SyntaxError') {
        Toast.show({ type: 'error', text1: 'Sorry', text2: "Something went wrong" })
      } else {
        Toast.show({ type: 'error', text1: 'Error', text2: 'Failed to fetch ledger data' })
      }
      setLoading(false)
    }
  }

  const downloadLedgerPdf = async () => {
    if (!startDate || !endDate) {
      Toast.show({ type: 'error', text1: 'Error', text2: 'Please select both start and end dates' })
      return
    }

    if (startDate > endDate) {
      Toast.show({ type: 'error', text1: 'Error', text2: 'Start date cannot be later than end date' })
      return
    }

    try {
      const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.LedgerURL.other_statement_details_PDF_download_API +
        `?the_customer_code=${UrlStorage.ParameterList.BasicData.user_type == 'broker' ? UrlStorage.ParameterList.BasicData.customerDetails.customer_code : UrlStorage.ParameterList.BasicData.emp_code}` +
        `&user_type=${UrlStorage.ParameterList.BasicData.user_type}` +
        `&the_start_date=${moment(startDate).format('YYYY-MM-DD')}` +
        `&the_end_date=${moment(endDate).format('YYYY-MM-DD')}`


      const res = await RNFetchBlob.config({
        fileCache: true,
        appendExt: 'pdf',

      }).fetch('GET', url)

      const localPath = res.path()

      props.navigation.navigate('PdfViewScreen', {
        pdfUrl: localPath, // Remove file:// prefix
        pdfLink: url,
        page_title: 'Ledger PDF',
        type: 'local' // Change to 'local' to make it explicit
      })

    } catch (error) {
      Toast.show({ type: 'error', text1: 'Error', text2: 'Failed to download PDF. Please try again.' })
    }
  }

  const downloadLedgerPdfSBS = async () => {
    if (!startDate || !endDate) {
      Toast.show({ type: 'error', text1: 'Error', text2: 'Please select both start and end dates', });
      return;
    }

    if (startDate > endDate) {
      Toast.show({ type: 'error', text1: 'Error', text2: 'Start date cannot be later than end date', });
      return;
    }

    Toast.show({ type: 'info', text1: 'Downloading', text2: 'Preparing your PDF...', });

    try {
      const url =
        `${UrlStorage.BaseUrlList.SBS.base_url_sbs}api/dealer/detailed_txn_ledger_pdf` +
        `?KUNNR=${UrlStorage.ParameterList.BasicData.user_type == 'broker' ? UrlStorage.ParameterList.BasicData.selectedCustomerCode : UrlStorage.ParameterList.BasicData.emp_id}` +
        `&format_type=pdf` +
        `&the_start_date=${moment(startDate).format('MM/DD/YYYY')}` +
        `&the_end_date=${moment(endDate).format('MM/DD/YYYY')}`;


      const res = await RNFetchBlob.config({
        fileCache: true,
        appendExt: 'pdf',
      }).fetch('GET', url, {
        Authorization: UrlStorage.ParameterList.BasicData.user_type == 'broker' ? `SAP_SP${UrlStorage.ParameterList.BasicData.emp_id}` : `SAP_DEALER${UrlStorage.ParameterList.BasicData.emp_id}`,
      });

      const pdfPath = res.path();
      props?.navigation.navigate('PdfViewScreen', {
        pdfUrl: pdfPath,
        page_title: 'Transaction Ledger',
        type: 'local',
        pdfLink: url,
      });

    } catch (error) {
      Toast.show({ type: 'error', text1: 'Error', text2: 'Failed to download PDF', });
    }
  };

  const handleDownloadButtonPress = () => {
    if (showDateSelector) {
      DataStorage.typeOfUse === 1 ? downloadLedgerPdfSBS() : downloadLedgerPdf()
    } else {
      setShowDateSelector(true)
    }
  }

  const formatDateDisplay = (date) => {
    return moment(date).format('MMM DD, YYYY')
  }

  const handleStartDateConfirm = (event, selectedDate) => {
    setShowStartDatePicker(false)

    if (event.type === 'set' && selectedDate) {
      setStartDate(selectedDate)
      if (selectedDate > endDate) {
        setEndDate(selectedDate)
      }
    }
  }

  const handleEndDateConfirm = (event, selectedDate) => {
    setShowEndDatePicker(false)

    if (event.type === 'set' && selectedDate) {
      setEndDate(selectedDate)
    }
  }

  const renderItemDealer = (item) => {
    return (
      <View style={{ width: "100%", overflow: "hidden", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), backgroundColor: "#FFFFFF", elevation: 10, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: (0), y: (4) } }}>
        <View style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(4), alignItems: "flex-start" }}>
          <View style={{ width: "100%", flexDirection: "row", alignItems: "flex-start" }}>
            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Voucher Date:</Text>
            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500", flex: 1 }}>{DataStorage.typeOfUse === 1 ? item?.entry_date : moment(item.voucher_date, 'MM/DD/YYYY').format('MMM DD, YYYY')}</Text>
            {/* <TouchableOpacity onPress={() => {
              setShowDetailsItem(item)
              setIsVisiblePopup(true)
            }} style={{ backgroundColor: DataStorage.primaryColorCode, paddingVertical: moderateScale(5), paddingHorizontal: moderateScale(10), borderRadius: moderateScale(5), elevation: 10 }}>
              <Text style={{ color: "#FFF", fontSize: moderateScale(13), fontWeight: 'bold' }}>Narration</Text>
            </TouchableOpacity> */}
          </View>
          <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Voucher NO:</Text>
            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{DataStorage.typeOfUse === 1 ? item?.voucher_no : item.voucher_no}</Text>
          </View>
          <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Quantity:</Text>
            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{item.quantity + " " + (DataStorage.typeOfUse == 1 ? item?.uom : '')}</Text>
          </View>
          <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Amount DR:</Text>
            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>₹{DataStorage.typeOfUse === 1 ? item?.amount_dr : item.amount_dr}</Text>
          </View>
          <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Amount CR:</Text>
            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>₹{DataStorage.typeOfUse === 1 ? item?.amount_cr : item.amount_cr}</Text>
          </View>
          <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Narration:</Text>
            <Text style={{ flex: 1, textTransform: 'uppercase', fontSize: moderateScale(13), fontWeight: '500', color: Colors.black }}>{item?.narration}</Text>
          </View>
        </View>
      </View>
    )
  }

  const renderItemSubDealer = (item) => {
    return (
      <View style={{ width: "100%", overflow: "hidden", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), backgroundColor: "#FFFFFF", elevation: 10, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: (0), y: (4) } }}>
        <View style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(4), alignItems: "flex-start" }}>
          <View style={{ width: "100%", flexDirection: "row", alignItems: "flex-start" }}>
            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Voucher Date:</Text>
            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500", flex: 1 }}>{moment(item.voucher_date, 'MM/DD/YYYY').format('MMM DD, YYYY')}</Text>
            <Image source={Icons.Option} style={{ width: moderateScale(16), height: moderateScale(16) }} />
          </View>
          <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Voucher NO:</Text>
            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{item.voucher_no}</Text>
          </View>
          <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Entry Date:</Text>
            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{moment(item.entry_date, 'MM/DD/YYYY').format('MMM DD, YYYY')}</Text>
          </View>
          <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Amount:</Text>
            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>₹{item.amount}</Text>
          </View>
        </View>
      </View>
    )
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
        <SBSCommonHeaderView title={DataStorage.typeOfUse === 1 ? "Last 50 Transaction" : "Ledger Balance"} backPath=" " Information={true} gotoLink={gotoLink} />
        <View style={{ width: "100%", flex: 1 }}>
          <View style={{ width: "100%", height: UrlStorage.ParameterList.BasicData.user_type != "broker" && UrlStorage.ParameterList.BasicData.user_type != "Dealer" ? moderateScale(130) : moderateScale(220), marginBottom: moderateScale(20) }}>
            <ImageBackground source={Icons.BlurBg} style={{ width: "100%", alignItems: "center", justifyContent: "center" }} imageStyle={{ resizeMode: "cover" }}>
              <View style={{ width: "100%", padding: moderateScale(20), gap: moderateScale(20), }}>
                <View style={{ padding: moderateScale(20), backgroundColor: "#00000042", borderRadius: moderateScale(10), borderWidth: moderateScale(1), borderColor: DataStorage.primaryColorCode, gap: moderateScale(8), alignItems: "center", justifyContent: "center" }}>
                  <Text style={{ color: "#FFFFFF", fontSize: moderateScale(24), fontWeight: "700" }}>₹{DataStorage.typeOfUse === 1 ? Number(DashboardDataStorage.requestLedgerForSBS?.credit_expose).toFixed(2) : ledgerBalanceData?.credit_expose}</Text>
                  <Text style={{ color: "#FFFFFF", fontSize: moderateScale(13) }}>Outstanding Balance</Text>
                  <Text style={{ color: "#FFFFFF", fontSize: moderateScale(13) }}>*Last 50 Transaction as on {moment(new Date(), 'MM/DD/YYYY').format('MMM DD, YYYY')}</Text>
                </View>

                {UrlStorage.ParameterList.BasicData.user_type != "broker" && UrlStorage.ParameterList.BasicData.user_type != "Dealer" ? null : <View style={{ width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(20) }}>
                  <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }}>
                    <View style={{ width: "100%", backgroundColor: "#8FC031", borderRadius: moderateScale(10), padding: moderateScale(10), alignItems: "center", justifyContent: "center" }}>
                      <Text style={{ color: Colors.white, fontSize: moderateScale(14), fontWeight: "500" }}>Confirm Balance</Text>
                    </View>
                  </TouchableOpacity>
                  <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }}>
                    <View style={{ width: "100%", backgroundColor: Colors.main, borderRadius: moderateScale(10), padding: moderateScale(10), alignItems: "center", justifyContent: "center" }}>
                      <Text style={{ color: Colors.white, fontSize: moderateScale(14), fontWeight: "500" }}>Make Payment</Text>
                    </View>
                  </TouchableOpacity>
                </View>}
              </View>
            </ImageBackground>
          </View>
          <View style={{ width: "100%", flex: 1 }}>
            {ledgerList?.length > 0 ? <FlatList
              data={ledgerList}
              keyExtractor={(item) => item.id}
              showsVerticalScrollIndicator={false}
              decelerationRate="fast"
              renderItem={({ item }) => (
                <TouchableOpacity onPress={() => {

                }} activeOpacity={0.95} style={{ paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(7) }}>
                  {DataStorage.typeOfUse === 1 ? renderItemDealer(item) :
                    UrlStorage.ParameterList.BasicData.user_type == "broker" || UrlStorage.ParameterList.BasicData.user_type.toLowerCase() == "dealer" ? renderItemDealer(item) : renderItemSubDealer(item)
                  }
                </TouchableOpacity>
              )}
            /> : !loading && <View style={{ flex: 0.8, alignItems: 'center', justifyContent: 'center' }}>
              <Text style={{ fontSize: 15, fontWeight: '600', color: "#000", marginBottom: 4, }} numberOfLines={2}> {'No Data Available'} </Text>
            </View>}
          </View>
        </View>
        {showDateSelector && (
          <View style={{ flexDirection: 'row', width: '100%', height: moderateScale(80), backgroundColor: Colors.white, paddingHorizontal: moderateScale(15), paddingVertical: moderateScale(10), borderTopWidth: 1, borderTopColor: '#DCDDDF', gap: moderateScale(10) }}>
            <TouchableOpacity onPress={() => setShowStartDatePicker(true)} style={{ flex: 1, backgroundColor: '#F5F5F5', borderRadius: moderateScale(8), padding: moderateScale(10), borderWidth: 1, borderColor: startDate ? DataStorage.primaryColorCode : '#DCDDDF' }} >
              <Text style={{ color: '#7D7D7D', fontSize: moderateScale(12), fontWeight: '500' }}>Start Date</Text>
              <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: '600', marginTop: moderateScale(2) }}>{startDate ? formatDateDisplay(startDate) : 'Select Date'}</Text>
            </TouchableOpacity>

            <TouchableOpacity onPress={() => setShowEndDatePicker(true)} style={{ flex: 1, backgroundColor: '#F5F5F5', borderRadius: moderateScale(8), padding: moderateScale(10), borderWidth: 1, borderColor: endDate ? DataStorage.primaryColorCode : '#DCDDDF' }} >
              <Text style={{ color: '#7D7D7D', fontSize: moderateScale(12), fontWeight: '500' }}>End Date</Text>
              <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: '600', marginTop: moderateScale(2) }}>{endDate ? formatDateDisplay(endDate) : 'Select Date'}</Text>
            </TouchableOpacity>
          </View>
        )}

        {UrlStorage.ParameterList.BasicData.user_type != "broker" && UrlStorage.ParameterList.BasicData.user_type.toLocaleLowerCase() != "dealer" ? null : ledgerList?.length > 0 && <TouchableOpacity activeOpacity={0.95} onPress={handleDownloadButtonPress}>
          <View style={{ width: "100%", paddingHorizontal: moderateScale(15) }}>
            <View style={{ width: "100%", height: moderateScale(40), flexDirection: "row", gap: moderateScale(8), backgroundColor: DataStorage.primaryColorCode, borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center" }}>
              <Image source={Icons.Download} style={{ width: moderateScale(18), height: moderateScale(21) }} />
              <Text style={{ color: Colors.white, fontSize: moderateScale(16), fontWeight: 500 }}> {showDateSelector ? 'Download Statement' : 'Download Detailed Statement'} </Text>
            </View>
          </View>
        </TouchableOpacity>}
        <View style={{ height: moderateScale(20) }} />
      </View>

      <Modal
        isVisible={isVisiblePopup}
        style={{ margin: 0, backgroundColor: '#0005' }}
        customBackdrop={
          <TouchableWithoutFeedback onPress={() => { setIsVisiblePopup(false) }}>
            <View style={{ flex: 1, backgroundColor: '#0000' }} />
          </TouchableWithoutFeedback>
        }
      >
        <View style={{ width: '85%', backgroundColor: '#FFF', alignSelf: 'center', borderRadius: moderateScale(10) }}>
          <View style={{ width: '100%', height: moderateScale(60), backgroundColor: Colors.main, borderTopLeftRadius: moderateScale(10), borderTopRightRadius: moderateScale(10), flexDirection: 'row', alignItems: 'center', justifyContent: 'center', paddingHorizontal: moderateScale(20) }}>
            <Text style={{ flex: 1, textTransform: 'uppercase', fontSize: moderateScale(18), fontWeight: '600', color: '#FFF' }}>narration</Text>
            <TouchableOpacity onPress={() => { setIsVisiblePopup(false) }} style={{ width: moderateScale(20), height: moderateScale(20), borderRadius: moderateScale(20), borderWidth: moderateScale(1), borderColor: Colors.white, alignItems: 'center', justifyContent: 'center' }}>
              <Text style={{ color: '#FFF', fontSize: moderateScale(12) }}>X</Text>
            </TouchableOpacity>
          </View>
          <View style={{ width: '100%', height: moderateScale(80), borderTopLeftRadius: moderateScale(10), borderTopRightRadius: moderateScale(10), flexDirection: 'row', alignItems: 'center', justifyContent: 'center', paddingHorizontal: moderateScale(20) }}>
            <Text style={{ flex: 1, textTransform: 'uppercase', fontSize: moderateScale(16), fontWeight: '400', color: Colors.black }}>{showDetailsItem?.narration}</Text>
          </View>
        </View>
      </Modal>

      {showStartDatePicker && (
        <View style={{ width: '100%', height: '100%', position: 'absolute', alignItems: 'center', justifyContent: 'center', backgroundColor: '#00000060' }}>
          <View style={{ backgroundColor: '#FFF', padding: 10, borderRadius: 10 }}>
            <DateTimePicker
              value={startDate}
              mode="date"
              display="default"
              maximumDate={new Date()}
              onChange={handleStartDateConfirm}
            />
          </View>
        </View>
      )}

      {showEndDatePicker && (
        <View style={{ width: '100%', height: '100%', position: 'absolute', alignItems: 'center', justifyContent: 'center', backgroundColor: '#00000060' }}>
          <View style={{ backgroundColor: '#FFF', padding: 10, borderRadius: 10 }}>
            <DateTimePicker
              value={endDate}
              mode="date"
              display="default"
              maximumDate={new Date()}
              minimumDate={startDate}
              onChange={handleEndDateConfirm}
            />
          </View>
        </View>
      )}

      <Toast config={toastConfig} />
      {loading && <Loader />}
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  )
}

export default LedgerScreen