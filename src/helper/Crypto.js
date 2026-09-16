import CryptoJS from 'crypto-js'

const IV_STRING = 'fedcba9876543210'
const KEY_STRING = '0123456789abcdef'

const IV = CryptoJS.enc.Utf8.parse(IV_STRING)
const KEY = CryptoJS.enc.Utf8.parse(KEY_STRING)

const padString = (text) => {
    const blockSize = 16
    const padLength = blockSize - (text.length % blockSize)
    return text + ' '.repeat(padLength)
}

export const encryptToHex = (plainText) => {
    if (!plainText) throw new Error('Empty string')

    const padded = padString(plainText)

    const encrypted = CryptoJS.AES.encrypt(
        CryptoJS.enc.Utf8.parse(padded),
        KEY,
        {
            iv: IV,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.NoPadding,
        }
    )

    return encrypted.ciphertext.toString(CryptoJS.enc.Hex)
}

export const decryptFromHex = (hexCipher) => {
    if (!hexCipher) throw new Error('Empty string')

    const cipherParams = CryptoJS.lib.CipherParams.create({
        ciphertext: CryptoJS.enc.Hex.parse(hexCipher),
    })

    const decrypted = CryptoJS.AES.decrypt(
        cipherParams,
        KEY,
        {
            iv: IV,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.NoPadding,
        }
    )

    return decrypted.toString(CryptoJS.enc.Utf8).trim()
}
