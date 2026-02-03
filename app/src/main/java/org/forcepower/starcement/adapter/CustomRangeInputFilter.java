package org.forcepower.starcement.adapter;

import android.text.InputFilter;
import android.text.Spanned;

public final class CustomRangeInputFilter implements InputFilter {
    private double minValue;
    private double maxValue;

    public CustomRangeInputFilter(double minVal, double maxVal) {
        this.minValue = minVal;
        this.maxValue = maxVal;
    }

    @Override
    public CharSequence filter(CharSequence source, int start, int end, Spanned dest, int dStart, int dEnd) {
        try {
            // Remove the string out of destination that is to be replaced
            String newVal = dest.toString().substring(0, dStart) + dest.toString().substring(dEnd, dest.toString().length());
            newVal = newVal.substring(0, dStart) + source.toString() + newVal.substring(dStart, newVal.length());
            double input = Double.parseDouble(newVal);

            try {
                String check = newVal + "";
                if(check.contains("."))
                {
                    String[] array = check.split("\\.");
                    if(array[1].length()>2)
                    {
                        return "";
                    }
                }
            }
            catch (Exception e)
            {
                e.printStackTrace();
            }

            if (isInRange(minValue, maxValue, input)) {
                return null;
            }
        } catch (NumberFormatException e) {
            e.printStackTrace();
        }
        return "";
    }

    private boolean isInRange(double a, double b, double c) {
        return b > a ? c >= a && c <= b : c >= b && c <= a;
    }
}
