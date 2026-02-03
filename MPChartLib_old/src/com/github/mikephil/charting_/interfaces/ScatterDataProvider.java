package com.github.mikephil.charting_.interfaces;

import com.github.mikephil.charting_.data.ScatterData;

public interface ScatterDataProvider extends BarLineScatterCandleBubbleDataProvider {

    ScatterData getScatterData();
}
