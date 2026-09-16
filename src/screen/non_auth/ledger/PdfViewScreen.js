import React, { useEffect, useState, useRef } from 'react'
import { View, ActivityIndicator, TouchableOpacity, Text, Alert, Platform } from 'react-native'
import Pdf from 'react-native-pdf'
import RNBlobUtil from 'react-native-blob-util'
import RNFetchBlob from 'react-native-blob-util'
import SafeView from '../../../helper/SafeView'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import moment from 'moment'

const PdfViewScreen = ({ route, navigation }) => {
    const [pdfPath, setPdfPath] = useState(null)
    const [loading, setLoading] = useState(true)
    const [loadingText, setLoadingText] = useState('Preparing PDF...')
    const [error, setError] = useState(false)
    const isMounted = useRef(true)
    const downloadTimeoutRef = useRef(null)

    const { pdfUrl, page_title, type, pdfLink } = route.params

    useEffect(() => {
        return () => {
            isMounted.current = false
            if (downloadTimeoutRef.current)
                clearTimeout(downloadTimeoutRef.current)
            if (pdfPath && type === 'url') {
                setTimeout(() => {
                    RNFetchBlob.fs.unlink(pdfPath).catch(() => { })
                }, 100)
            }
        }
    }, [pdfPath, type])

    useEffect(() => {
        const unsubscribe = navigation.addListener('beforeRemove', (e) => {
            if (pdfPath && type === 'url') {
                setTimeout(() => {
                    RNFetchBlob.fs.unlink(pdfPath).catch(() => { })
                }, 100)
            }
        })
        return unsubscribe
    }, [navigation, pdfPath, type])

    const download = async () => {
        try {
            if (!isMounted.current) return
            setLoadingText('Downloading PDF...')
            setError(false)
            const timeoutPromise = new Promise((_, reject) => {
                downloadTimeoutRef.current = setTimeout(() => {
                    reject(new Error('Download timeout - Please check your connection'))
                }, 30000)
            })
            const downloadPromise = RNFetchBlob.config({
                fileCache: true,
                appendExt: 'pdf',
            }).fetch('GET', pdfUrl)
            const res = await Promise.race([downloadPromise, timeoutPromise])
            if (downloadTimeoutRef.current)
                clearTimeout(downloadTimeoutRef.current)
            if (isMounted.current && res && res.path()) {
                setLoadingText('Loading PDF...')
                setPdfPath(res.path())
            }
        } catch (e) {
            if (isMounted.current) {
                setLoadingText('Error loading PDF')
                setError(true)
                setLoading(false)
                Alert.alert('Error', e.message || 'Failed to load PDF. Please check your internet connection and try again.',
                    [
                        { text: 'Go Back', onPress: () => navigation.goBack() },
                        {
                            text: 'Retry', onPress: () => {
                                setError(false)
                                setLoading(true)
                                download()
                            }
                        }
                    ]
                )
            }
        }
    }

    useEffect(() => {
        if (type == 'url')
            download()
        else if (type == 'local') {
            RNFetchBlob.fs.exists(pdfUrl)
                .then((exists) => {
                    if (exists) {
                        setPdfPath(pdfUrl)
                        setLoadingText('Loading PDF...')
                    } else {
                        setError(true)
                        setLoading(false)
                        Alert.alert('Error', 'PDF file not found on device')
                    }
                })
                .catch((err) => {
                    setError(true)
                    setLoading(false)
                })
        } else
            setLoadingText('Loading PDF...')
    }, [])

    useEffect(() => {
        if (pdfPath) {
            const timer = setTimeout(() => {
                setLoading(false)
            }, 1000)
            return () => clearTimeout(timer)
        }
    }, [pdfPath])

    const getPdfSource = () => {
        if (type === 'url' || type === 'local') {
            return pdfPath ? { uri: `file://${pdfPath}`, cache: false } : null
        }
        return { uri: pdfUrl, cache: true }
    }

    const downloadPdf = async () => {
        const dateTime = moment(new Date()).format('YYYYMMDDHHmmss')
        const { config, fs } = RNBlobUtil
        const isIOS = Platform.OS === 'ios'
        const dirToSave = isIOS ? fs.dirs.DocumentDir : fs.dirs.DownloadDir
        const filePath = `${dirToSave}/${page_title}_${dateTime}.pdf`
        const fileName = `${page_title}_${dateTime}.pdf`

        if (type === 'local') {
            try {
                await RNBlobUtil.fs.cp(pdfUrl, filePath)
                if (Platform.OS === 'android')
                    await RNBlobUtil.fs.scanFile([{ path: filePath, mime: 'application/pdf' }])
                Alert.alert('Download Successful', 'PDF has been saved to your device.',
                    [
                        { text: 'OK', style: 'cancel' },
                        {
                            text: 'Open', onPress: () => {
                                if (Platform.OS === 'ios')
                                    RNBlobUtil.ios.previewDocument(filePath)
                                else
                                    RNBlobUtil.android.actionViewIntent(filePath, 'application/pdf')
                            }
                        }
                    ]
                )
            } catch (error) {
                Alert.alert('Error', 'Failed to download file')
            }
            return
        }

        config({
            fileCache: true,
            appendExt: 'pdf',
            path: filePath,
            addAndroidDownloads: {
                useDownloadManager: true,
                notification: true,
                title: fileName,
                description: 'Downloading PDF',
                path: filePath,
                mime: 'application/pdf',
                mediaScannable: true,
            },
        })
            .fetch('GET', pdfLink || pdfUrl)
            .then(res => {
                Alert.alert(
                    'Download Successful',
                    'PDF has been saved to your device.',
                    [
                        { text: 'OK', style: 'cancel' },
                        {
                            text: 'Open', onPress: () => {
                                if (Platform.OS === 'ios')
                                    RNBlobUtil.ios.previewDocument(filePath)
                                else
                                    RNBlobUtil.android.actionViewIntent(filePath, 'application/pdf')
                            }
                        }
                    ]
                )
            })
            .catch(error => {
                Alert.alert('Error', 'Failed to download file. Please try again.')
            })
    }

    const downloadFromUrl = async () => {
        const dateTime = moment(new Date()).format('YYYYMMDDHHmmss')
        const { fs } = RNBlobUtil
        const isIOS = Platform.OS === 'ios'
        const dir = isIOS ? fs.dirs.DocumentDir : fs.dirs.DownloadDir
        const fileName = `${page_title}_${dateTime}.pdf`
        const path = `${dir}/${fileName}`
        try {
            const res = await RNBlobUtil.config({
                path: path,
                fileCache: true,
                addAndroidDownloads: {
                    useDownloadManager: true,
                    notification: true,
                    mime: "application/pdf",
                    description: "Downloading PDF",
                    mediaScannable: true,
                    title: fileName,
                    path: path,
                },
            }).fetch("GET", pdfUrl)
            Alert.alert(
                'Download Successful',
                'PDF has been saved to your device.',
                [
                    { text: 'OK', style: 'cancel' },
                    {
                        text: 'Open', onPress: () => {
                            if (Platform.OS === 'ios')
                                RNBlobUtil.ios.previewDocument(path)
                            else
                                RNBlobUtil.android.actionViewIntent(path, 'application/pdf')
                        }
                    }
                ]
            )
        } catch (err) {
            Alert.alert('Error', 'Failed to download file. Please try again.')
        }
    }

    const LoadingOverlay = () => {
        return (
            <View style={styles.loadingContainer}>
                <View style={styles.loadingContent}>
                    <ActivityIndicator size="large" color={Colors.main} style={styles.spinner} />
                    <Text style={styles.loadingText}>{loadingText}</Text>
                    <View style={styles.loadingDots}>
                        <View style={[styles.dot, styles.dot1]} />
                        <View style={[styles.dot, styles.dot2]} />
                        <View style={[styles.dot, styles.dot3]} />
                    </View>
                </View>
            </View>
        )
    }

    const ErrorView = () => (
        <View style={styles.errorContainer}>
            <Text style={styles.errorText}>Failed to load PDF</Text>
            <Text style={styles.errorSubText}> The PDF file could not be loaded. Please check your connection and try again. </Text>
            <View style={styles.errorButtonContainer}>
                <TouchableOpacity
                    style={styles.retryButton}
                    onPress={() => {
                        setError(false)
                        setLoading(true)
                        if (type === 'url')
                            download()
                        else
                            setPdfPath(pdfUrl)
                    }} >
                    <Text style={styles.retryButtonText}>Retry</Text>
                </TouchableOpacity>
                <TouchableOpacity style={styles.backButton} onPress={() => navigation.goBack()} >
                    <Text style={styles.backButtonText}>Go Back</Text>
                </TouchableOpacity>
            </View>
        </View>
    )

    const pdfSource = getPdfSource()

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main} disableKeyboardDismiss={true}>
            <SBSCommonHeaderView title={page_title} backPath="back" Information={false} gotoLink={null} navigation={navigation} />
            <View style={{ flex: 1 }}>
                {error ? <ErrorView /> :
                    <>
                        {pdfSource && <Pdf
                            source={pdfSource}
                            trustAllCerts={false}
                            enablePaging={true}
                            onLoadComplete={(numberOfPages, filePath) => { setLoading(false) }}
                            onPageChanged={(page, numberOfPages) => { setLoading(false) }}
                            onError={(error) => {
                                setError(true)
                                setLoading(false)
                            }}
                            onPressLink={(uri) => { }}
                            style={{ flex: 1, backgroundColor: Colors.white }}
                            spacing={0}
                            enableAnnotationRendering={false}
                            maxScale={3}
                            minScale={0.5}
                            scale={1}
                        />}
                        {!loading && !error && pdfSource && <TouchableOpacity
                            onPress={() => {
                                if (type === 'local')
                                    downloadPdf()
                                else if (type === 'url')
                                    downloadFromUrl()
                                else
                                    downloadPdf()
                            }}
                            style={styles.downloadButton}
                            activeOpacity={0.8}
                        >
                            <Text style={styles.downloadButtonText}>Download PDF</Text>
                        </TouchableOpacity>}
                    </>
                }
            </View>
            {loading && <LoadingOverlay />}
        </SafeView>
    )
}

const styles = {
    loadingContainer: { position: 'absolute', top: 0, bottom: 0, left: 0, right: 0, backgroundColor: Colors.white, justifyContent: 'center', alignItems: 'center', zIndex: 1000, },
    loadingContent: { alignItems: 'center', backgroundColor: 'rgba(255, 255, 255, 0.95)', padding: moderateScale(30), borderRadius: moderateScale(15), shadowColor: '#000', shadowOffset: { width: 0, height: 2, }, shadowOpacity: 0.1, shadowRadius: 8, elevation: 5, minWidth: moderateScale(200), },
    spinner: { marginBottom: moderateScale(15), },
    loadingText: { fontSize: moderateScale(16), color: Colors.main || '#333', fontWeight: '500', textAlign: 'center', marginBottom: moderateScale(20), },
    loadingDots: { flexDirection: 'row', alignItems: 'center', },
    dot: { width: moderateScale(8), height: moderateScale(8), borderRadius: moderateScale(4), backgroundColor: Colors.main || '#007AFF', marginHorizontal: moderateScale(3), },
    dot1: { animationName: 'bounce', animationDuration: '1.4s', animationIterationCount: 'infinite', animationDelay: '0s', },
    dot2: { animationName: 'bounce', animationDuration: '1.4s', animationIterationCount: 'infinite', animationDelay: '0.2s', },
    dot3: { animationName: 'bounce', animationDuration: '1.4s', animationIterationCount: 'infinite', animationDelay: '0.4s', },
    errorContainer: { flex: 1, justifyContent: 'center', alignItems: 'center', padding: moderateScale(30), backgroundColor: Colors.white, },
    errorText: { fontSize: moderateScale(18), fontWeight: '700', color: '#1A1A1A', marginBottom: moderateScale(10), textAlign: 'center', },
    errorSubText: { fontSize: moderateScale(14), color: '#666', marginBottom: moderateScale(30), textAlign: 'center', lineHeight: moderateScale(20), },
    errorButtonContainer: { flexDirection: 'row', justifyContent: 'center', alignItems: 'center', gap: moderateScale(15), },
    retryButton: { backgroundColor: Colors.main, paddingHorizontal: moderateScale(30), paddingVertical: moderateScale(12), borderRadius: moderateScale(8), minWidth: moderateScale(100), },
    retryButtonText: { color: '#fff', fontSize: moderateScale(14), fontWeight: '600', textAlign: 'center', },
    backButton: { backgroundColor: '#F0F0F0', paddingHorizontal: moderateScale(30), paddingVertical: moderateScale(12), borderRadius: moderateScale(8), minWidth: moderateScale(100), },
    backButtonText: { color: '#333', fontSize: moderateScale(14), fontWeight: '600', textAlign: 'center', },
    downloadButton: { position: 'absolute', bottom: moderateScale(30), left: moderateScale(100), right: moderateScale(100), height: moderateScale(50), backgroundColor: Colors.main, borderRadius: moderateScale(10), elevation: 10, shadowColor: '#000', shadowOffset: { width: 0, height: 4, }, shadowOpacity: 0.3, shadowRadius: 6, justifyContent: 'center', alignItems: 'center', },
    downloadButtonText: { color: '#fff', fontSize: moderateScale(15), fontWeight: '600', }
}

export default PdfViewScreen