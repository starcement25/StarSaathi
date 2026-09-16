import React, { createContext, useState } from "react"
const userContext = createContext()
const ContextWrapper = ({ children }) => {
  const [token, setToken] = useState(false)
  const [type, settype] = useState('')
  const [paymentStatus, setpaymentStatus] = useState(false)
  const [notificartion, setnotificartion] = useState(0)
  return (
    <userContext.Provider value={{ token, setToken, type, settype, paymentStatus, setpaymentStatus, notificartion, setnotificartion }}>
      {children}
    </userContext.Provider>
  )
}
export default userContext
export { ContextWrapper }