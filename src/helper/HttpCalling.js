import axios from 'axios';
import { decryptFromHex } from './Crypto';

export const httpPostCallWithXmlResponseDecrypted = async (url, jsonPayload) => {
    try {
        const response = await axios.post(url, jsonPayload, {
            headers: {
                'Content-Type': 'application/json',
            },
            responseType: 'text',
        });
        let responseFromServer = response.data;
        responseFromServer = decryptFromHex(responseFromServer);
        return responseFromServer;
    } catch (e) {
        return 'Network Failure';
    }
};
