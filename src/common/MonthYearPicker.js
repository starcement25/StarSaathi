import React, { useRef, useState } from 'react'
import { View, Text, FlatList, TouchableOpacity, StyleSheet, Modal } from 'react-native'

const ITEM_HEIGHT = 48
const VISIBLE_ITEMS = 5

const MONTHS = [
  'January', 'February', 'March', 'April', 'May',
  'June', 'July', 'August', 'September', 'October', 'November', 'December'
]
const YEARS = Array.from({ length: 30 }, (_, i) => 2015 + i)

function Drum({ data, selectedIndex, onChange }) {
  const flatRef = useRef(null)
  const paddedData = ['', '', ...data, '', '']

  const onMomentumEnd = (e) => {
    const index = Math.round(e.nativeEvent.contentOffset.y / ITEM_HEIGHT)
    onChange(index)
    flatRef.current?.scrollToOffset({ offset: index * ITEM_HEIGHT, animated: true })
  }

  return (
    <FlatList
      ref={flatRef}
      data={paddedData}
      keyExtractor={(_, i) => String(i)}
      showsVerticalScrollIndicator={false}
      snapToInterval={ITEM_HEIGHT}
      decelerationRate="fast"
      onMomentumScrollEnd={onMomentumEnd}
      initialScrollIndex={selectedIndex}
      getItemLayout={(_, index) => ({
        length: ITEM_HEIGHT, offset: ITEM_HEIGHT * index, index,
      })}
      renderItem={({ item, index }) => {
        const isSelected = index === selectedIndex + 2
        return (
          <View style={styles.drumItem}>
            <Text style={[styles.drumText, isSelected && styles.selectedText]}> {item} </Text>
          </View>
        )
      }}
      style={{ height: ITEM_HEIGHT * VISIBLE_ITEMS }}
    />
  )
}

export default function MonthYearPicker({ visible, onConfirm, onCancel }) {
  const now = new Date()
  const [monthIndex, setMonthIndex] = useState(now.getMonth())
  const [yearIndex, setYearIndex] = useState(
    YEARS.indexOf(now.getFullYear()) !== -1 ? YEARS.indexOf(now.getFullYear()) : 0
  )

  const handleConfirm = () => {
    const date = new Date(YEARS[yearIndex], monthIndex, 1)
    onConfirm(date)
  }

  return (
    <Modal visible={visible} transparent animationType="slide">
      <View style={styles.overlay}>
        <View style={styles.sheet}>
          <View style={styles.header}>
            <TouchableOpacity onPress={onCancel}>
              <Text style={styles.cancelBtn}>Cancel</Text>
            </TouchableOpacity>
            <Text style={styles.title}>Month & Year</Text>
            <TouchableOpacity onPress={handleConfirm}>
              <Text style={styles.doneBtn}>Done</Text>
            </TouchableOpacity>
          </View>
          <View style={styles.drumsContainer}>
            <View style={styles.selectionBar} pointerEvents="none" />
            <Drum data={MONTHS} selectedIndex={monthIndex} onChange={setMonthIndex} />
            <Drum data={YEARS.map(String)} selectedIndex={yearIndex} onChange={setYearIndex} />
          </View>
        </View>
      </View>
    </Modal>
  )
}

const styles = StyleSheet.create({
  overlay: { flex: 1, justifyContent: 'flex-end', backgroundColor: 'rgba(0,0,0,0.4)', },
  sheet: { backgroundColor: '#fff', borderTopLeftRadius: 16, borderTopRightRadius: 16, paddingBottom: 32, },
  header: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 20, paddingVertical: 14, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: '#e0e0e0', },
  title: { fontSize: 16, fontWeight: '500', color: '#111', },
  cancelBtn: { fontSize: 15, color: '#888', },
  doneBtn: { fontSize: 15, fontWeight: '600', color: '#1a73e8', },
  drumsContainer: { flexDirection: 'row', position: 'relative', paddingHorizontal: 16, marginTop: 8, },
  selectionBar: { position: 'absolute', left: 16, right: 16, top: ITEM_HEIGHT * 2, height: ITEM_HEIGHT, borderTopWidth: StyleSheet.hairlineWidth, borderBottomWidth: StyleSheet.hairlineWidth, borderColor: '#ccc', backgroundColor: '#f5f5f5', borderRadius: 8, },
  drumItem: { height: ITEM_HEIGHT, justifyContent: 'center', alignItems: 'center', },
  drumText: { fontSize: 17, color: '#aaa', },
  selectedText: { fontSize: 18, fontWeight: '500', color: '#111', },
})