import axios from 'axios';

export async function getApiWithoutToken(url) {
    return axios.get(url, {
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
    });
}

export async function postApiWithoutToken(url, payload) {
    return axios.post(url, payload, {
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        timeout: 20000,
    });
}

export async function putApiWithoutToken(url, payload) {
    return axios.put(url, payload, {
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
    });
}

export async function deleteApiWithoutToken(url) {
    return axios.delete(url, {
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
    });
}


