import AsyncStorage from "@react-native-async-storage/async-storage";
import DataStorage from "../storage/DataStorage";
import UrlStorage from "../storage/UrlStorage";
 
export const AuthCheckingApi = async () => {
    try {
        const empCode = UrlStorage?.ParameterList?.BasicData?.emp_id;
        const token = await AsyncStorage.getItem("auth_token");
        console.log("Token:", token);
 
        if (!token || !empCode) {
            console.log("Missing token or empCode", { token, empCode });
            return false;
        }
 
        const url = `${UrlStorage.BaseUrlList.Saathi.base_url_saathi}${UrlStorage.AuthURL.check_auth}`;
        const formData = new FormData();
        formData.append("emp_code", empCode);
        formData.append("auth_token", token);
 
        console.log("Token being sent:", token);
        console.log("Request URL:", url);
        console.log("FormData:", formData);
 
        const response = await fetch(url, {
            method: "POST",
            // headers: {
            //     Authorization: `Bearer ${token}`,
            //     Accept: "application/json",
            // },
            body: formData,
        });
 
        const result = await response.json();
 
        console.log("Final API result:", result);
 
        return result?.process_status === "YES";
 
    } catch (error) {
        console.error("Auth check error:", error);
        return false;
    }
};
 