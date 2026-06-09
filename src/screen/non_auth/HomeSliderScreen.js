import React, { useRef, useEffect } from "react";
import { View, StyleSheet, Image, Dimensions, Animated, } from "react-native";
import SafeView from "../../helper/SafeView";

const { width } = Dimensions.get("window");
const ITEM_WIDTH = width * 0.7;
const ITEM_HEIGHT = ITEM_WIDTH * 2;
const SPACING = 20;

const sliderData = [
    { id: "1", image: { uri: "https://picsum.photos/600/900?random=1" } },
    { id: "2", image: { uri: "https://picsum.photos/600/900?random=2" } },
    { id: "3", image: { uri: "https://picsum.photos/600/900?random=3" } },
    { id: "4", image: { uri: "https://picsum.photos/600/900?random=4" } },
];

const HomeSliderScreen = () => {
    const scrollX = useRef(new Animated.Value(0)).current;
    const flatListRef = useRef(null);

    // Duplicate data for infinite scroll illusion
    const dataLoop = [...sliderData, ...sliderData];

    useEffect(() => {
        let index = 0;
        const timer = setInterval(() => {
            index++;
            if (index >= dataLoop.length) index = 0;
            flatListRef.current?.scrollToIndex({
                index,
                animated: true,
            });
        }, 3000);
        return () => clearInterval(timer);
    }, []);

    // Animated position for dots
    const position = Animated.divide(scrollX, ITEM_WIDTH + SPACING);

    return (
        <SafeView>
            <View style={styles.container}>
                <Animated.FlatList
                    ref={flatListRef}
                    data={dataLoop}
                    keyExtractor={(item, index) => item.id + index}
                    horizontal
                    showsHorizontalScrollIndicator={false}
                    snapToInterval={ITEM_WIDTH + SPACING}
                    decelerationRate="fast"
                    bounces={false}
                    getItemLayout={(data, index) => ({
                        length: ITEM_WIDTH + SPACING,
                        offset: (ITEM_WIDTH + SPACING) * index,
                        index,
                    })}
                    contentContainerStyle={{
                        paddingHorizontal: (width - ITEM_WIDTH) / 2,
                    }}
                    onScroll={Animated.event(
                        [{ nativeEvent: { contentOffset: { x: scrollX } } }],
                        { useNativeDriver: true }
                    )}
                    scrollEventThrottle={16}
                    renderItem={({ item, index }) => {
                        const inputRange = [
                            (index - 1) * (ITEM_WIDTH + SPACING),
                            index * (ITEM_WIDTH + SPACING),
                            (index + 1) * (ITEM_WIDTH + SPACING),
                        ];

                        const scale = scrollX.interpolate({
                            inputRange,
                            outputRange: [0.9, 1, 0.9],
                            extrapolate: "clamp",
                        });

                        const opacity = scrollX.interpolate({
                            inputRange,
                            outputRange: [0.7, 1, 0.7],
                            extrapolate: "clamp",
                        });

                        return (
                            <Animated.View style={[styles.card, { transform: [{ scale }], opacity },]} >
                                <Image source={item.image} style={styles.image} resizeMode="cover" />
                            </Animated.View>
                        );
                    }}
                />

                {/* Dots */}
                <View style={styles.dotsContainer}>
                    {sliderData.map((_, i) => {
                        const opacity = position.interpolate({
                            inputRange: [i - 0.5, i, i + 0.5],
                            outputRange: [0.3, 1, 0.3],
                            extrapolate: "clamp",
                        });
                        return (<Animated.View key={i} style={[styles.dot, { opacity }]} />);
                    })}
                </View>
            </View>
        </SafeView>
    );
};

const styles = StyleSheet.create({
    container: { height: "100%", backgroundColor: "#eef2f7", justifyContent: "center", alignItems: "center", },
    card: { width: ITEM_WIDTH, height: ITEM_HEIGHT, marginHorizontal: SPACING / 2, borderRadius: 16, backgroundColor: "#fff", alignSelf: 'center', elevation: 8, shadowColor: "#000", shadowOffset: { width: 0, height: 6 }, shadowOpacity: 0.2, shadowRadius: 8, overflow: "hidden", },
    image: { width: "100%", height: "100%", },
    dotsContainer: { flexDirection: "row", justifyContent: "center", marginBottom: 20, },
    dot: { width: 12, height: 12, borderRadius: 6, backgroundColor: "#007bff", marginHorizontal: 5, },
});

export default HomeSliderScreen;
