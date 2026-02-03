package com.github.mikephil.charting_.interfaces;

import com.github.mikephil.charting_.components.YAxis.AxisDependency;
import com.github.mikephil.charting_.data.BarLineScatterCandleBubbleData;
import com.github.mikephil.charting_.utils.Transformer;

public interface BarLineScatterCandleBubbleDataProvider extends ChartInterface {

    Transformer getTransformer(AxisDependency axis);
    int getMaxVisibleCount();
    boolean isInverted(AxisDependency axis);
    
    int getLowestVisibleXIndex();
    int getHighestVisibleXIndex();

    BarLineScatterCandleBubbleData getData();
}
