import React, { useState } from 'react';
import { View } from 'react-native';
import { Dropdown } from 'react-native-element-dropdown';
import DataStorage from '../storage/DataStorage';

const DropdownComponent = ({ data = [], label = "" }) => {
  const [value, setValue] = useState(null);
  const [isFocus, setIsFocus] = useState(false);

  return (
    <View style={{ backgroundColor: 'white', width: "100%" }}>
      <Dropdown
        style={{ width: "100%", height: 50, borderColor: isFocus ? '#000' : '#DCDDDF', borderWidth: 1, borderRadius: 10, paddingHorizontal: 10, }}
        iconStyle={{ width: 30, height: 30, tintColor: DataStorage.primaryColorCode }}
        inputSearchStyle={{ height: 30, fontSize: 12 }}
        selectedTextStyle={{ fontSize: 14 }}
        placeholderStyle={{ fontSize: 14 }}
        data={data}
        search
        maxHeight={300}
        labelField="label"
        valueField="value"
        placeholder={!isFocus ? label : '...'}
        searchPlaceholder="Search..."
        value={value}
        onFocus={() => setIsFocus(true)}
        onBlur={() => setIsFocus(false)}
        onChange={item => {
          setValue(item.value);
          setIsFocus(false);
        }}
      />
    </View>
  );
};

export default DropdownComponent;
