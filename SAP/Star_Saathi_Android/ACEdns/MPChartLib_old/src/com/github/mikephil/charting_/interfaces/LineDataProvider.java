package com.github.mikephil.charting_.interfaces;

import com.github.mikephil.charting_.components.YAxis;
import com.github.mikephil.charting_.data.LineData;

public interface LineDataProvider extends BarLineScatterCandleBubbleDataProvider {

    LineData getLineData();

    YAxis getAxis(YAxis.AxisDependency dependency);
}
