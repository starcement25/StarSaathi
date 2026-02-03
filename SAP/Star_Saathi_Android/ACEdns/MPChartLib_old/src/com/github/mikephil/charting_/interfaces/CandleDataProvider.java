package com.github.mikephil.charting_.interfaces;

import com.github.mikephil.charting_.data.CandleData;

public interface CandleDataProvider extends BarLineScatterCandleBubbleDataProvider {

    CandleData getCandleData();
}
