import AsyncStorage from "@react-native-async-storage/async-storage"
import UrlStorage from "../storage/UrlStorage"

export const AuthCheckingApi = async () => {
    try {
        const empCode = UrlStorage?.ParameterList?.BasicData?.emp_id
        const token = await AsyncStorage.getItem("auth_token")
        if (!token || !empCode)
            return false
        const url = `https://starsaathi.com/SAP${UrlStorage.AuthURL.check_auth}`
        const formData = new FormData()
        formData.append("emp_code", empCode)
        formData.append("auth_token", token)
        const response = await fetch(url, { method: "POST", body: formData, })
        const result = await response.json()
        console.log(result, token);

        return result?.process_status === "YES"
    } catch (error) {
        return false
    }
}
