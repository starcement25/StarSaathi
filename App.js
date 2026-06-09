import React from "react";
import { LogBox } from 'react-native';
import { ContextWrapper } from "./MyContext";
import AppNavigator from "./AppNavigator";
const App = () => {
  LogBox.ignoreAllLogs(true);
  return (
    <ContextWrapper>
      <AppNavigator />
    </ContextWrapper>
  )
}
export default App;