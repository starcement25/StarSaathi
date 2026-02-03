package com.github.mikephil.charting_.interfaces;

import com.github.mikephil.charting_.data.BubbleData;

public interface BubbleDataProvider extends BarLineScatterCandleBubbleDataProvider {

    BubbleData getBubbleData();
}
