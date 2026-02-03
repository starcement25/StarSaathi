package org.forcepower.starcement.aaa;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;

import org.forcepower.starcement.R;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;

public final class OrderSubmittedActivity extends AceDnsParentActivity
{

    Activity myActivity;
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_submitted);

        myActivity = this;
        if(get_user_type(myActivity).equalsIgnoreCase("broker"))
        {
            selected_customr_code = get_selected_customer_code(myActivity);
        }
        else
        {
            selected_customr_code = get_emp_or_customer_code(myActivity);
        }
        ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
        ivHeaderBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
            }
        });

        Button btn_Payment_Status = (Button) findViewById(R.id.btn_Payment_Status);
        btn_Payment_Status.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
             onBackPressed();
            }
        });
    }
    @Override
    public void onBackPressed()
    {
        Intent intent = new Intent(getApplicationContext(),MenuActivity.class);
        startActivity(intent);
        finish();
    }
}
