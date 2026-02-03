package org.forcepower.starcement.util;

import android.app.DatePickerDialog;
import android.app.Dialog;
import android.content.DialogInterface;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.NumberPicker;

import androidx.annotation.Nullable;
import androidx.appcompat.app.AlertDialog;
import androidx.fragment.app.DialogFragment;

import org.forcepower.starcement.R;

import java.util.Calendar;

public final class MonthYearPickerDialog extends DialogFragment {

    private DatePickerDialog.OnDateSetListener listener;
    private NumberPicker monthPicker;
    private NumberPicker yearPicker;
    private final Calendar cal = Calendar.getInstance();

    public static final String MONTH_KEY = "monthValue";
    public static final String YEAR_KEY = "yearValue";

    private int monthVal = -1 , yearVal =-1 ;

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        final Bundle extras = getArguments();
        if(extras != null){
            monthVal = extras.getInt(MONTH_KEY , -1);
            yearVal = extras.getInt(YEAR_KEY , -1);
        }
    }

    public static MonthYearPickerDialog newInstance(int monthIndex , int daysIndex , int yearIndex) {
        final MonthYearPickerDialog f = new MonthYearPickerDialog();

        // Supply num input as an argument.
        final Bundle args = new Bundle();
        args.putInt(MONTH_KEY, monthIndex);
        args.putInt(YEAR_KEY, yearIndex);
        f.setArguments(args);

        return f;
    }

    public void setListener(final DatePickerDialog.OnDateSetListener listener) {
        this.listener = listener;
    }

    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {

        //getDialog().setTitle("Add Birthday");

        final AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        // Get the layout inflater
        final LayoutInflater inflater = getActivity().getLayoutInflater();

        final View dialog = inflater.inflate(R.layout.date_picker_dialog, null);
        monthPicker = (NumberPicker) dialog.findViewById(R.id.picker_month);
        yearPicker = (NumberPicker) dialog.findViewById(R.id.picker_year);

        monthPicker.setMinValue(1);
        monthPicker.setMaxValue(12);



        monthPicker.setValue(cal.get(Calendar.MONTH) + 1);

        monthPicker.setDisplayedValues(new String[]{"Jan","Feb","Mar","Apr","May","June","July",
                "Aug","Sep","Oct","Nov","Dec"});

        monthPicker.setOnValueChangedListener(new NumberPicker.OnValueChangeListener() {
            @Override
            public void onValueChange(NumberPicker picker, int oldVal, int newVal) {
                switch (newVal){
                    case 1:case 3:case 5:
                    case 7:case 8:case 10:
                    case 12:
                        break;
                    case 2:
                        break;

                    case 4:case 6:
                    case 9:case 11:
                        break;
                }

            }
        });

        int maxYear = cal.get(Calendar.YEAR);//2023
        final int minYear = 2015;//;
        int arraySize = maxYear - minYear;

        String[] tempArray = new String[arraySize];
        int tempYear = minYear+1;

        for(int i=0 ; i < arraySize; i++){
            tempArray[i] = " " + tempYear + "";
            tempYear++;
        }
//        pri("", "onCreateDialog: " + tempArray.length);
        yearPicker.setMinValue(minYear+1);
        yearPicker.setMaxValue(maxYear);
        yearPicker.setDisplayedValues(tempArray);

        if(yearVal != -1)
            yearPicker.setValue(yearVal);
        else
            yearPicker.setValue(tempYear -1);

        yearPicker.setOnValueChangedListener(new NumberPicker.OnValueChangeListener() {
            @Override
            public void onValueChange(NumberPicker picker, int oldVal, int newVal) {
                try {
                    if(isLeapYear(picker.getValue())){

                    }
                }catch (Exception e){
                    e.printStackTrace();
                }
            }
        });


        builder.setView(dialog)
                // Add action buttons
                .setPositiveButton("Ok", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int id) {
                        int year = yearPicker.getValue();
                        if(year == (minYear+1)){
                            year = 1904;
                        }
                        listener.onDateSet(null, year, monthPicker.getValue(), 0);
                    }
                })
                .setNegativeButton("cancel", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        MonthYearPickerDialog.this.getDialog().cancel();
                    }
                });

        return builder.create();
    }

    public static boolean isLeapYear(int year) {
        Calendar cal = Calendar.getInstance();
        cal.set(Calendar.YEAR, year);
        return cal.getActualMaximum(Calendar.DAY_OF_YEAR) > 365;
    }
}