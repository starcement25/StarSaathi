package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.twoDigitRoundOff;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.text.InputFilter;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RadioButton;
import android.widget.TextView;
import android.widget.Toast;

import org.forcepower.starcement.R;
import org.forcepower.starcement.Utils_;
import org.forcepower.starcement.bean.DigitsInputFilter;
import org.forcepower.starcement.util.HTTPUtils;

public final class PayAmountActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private String initialBalance = "00.00", coming_from = "";
	private EditText et_paynow_amount;
	private RadioButton rb_credit_card, rb_net_banking, rb_upi, rb_debit_card;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_pay_amount);
		try
		{
			mContext = this;
			if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				selected_customr_code = get_selected_customer_code(mContext);
			}
			else
			{
				selected_customr_code = get_emp_or_customer_code(mContext);
			}
			TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("Payment Amount");
			ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});
			//
			if(getIntent().hasExtra("initialBalance"))
				initialBalance = getIntent().getStringExtra("initialBalance");

			if(getIntent().hasExtra("coming_from"))
				coming_from = getIntent().getStringExtra("coming_from");
			rb_credit_card = findViewById(R.id.rb_credit_card);
			rb_net_banking = findViewById(R.id.rb_net_banking);
			rb_upi = findViewById(R.id.rb_upi);
			rb_debit_card = findViewById(R.id.rb_debit_card);


			et_paynow_amount =  (EditText) findViewById(R.id.et_paynow_amount);
			et_paynow_amount.setText(initialBalance+"");
			et_paynow_amount.setSelection(et_paynow_amount.getText().toString().length());
			et_paynow_amount.setFilters(new InputFilter[]{new DigitsInputFilter(9, 2, 999999999.00)});
//			if(BuildConfig.DEBUG)
//			{
//				et_paynow_amount.setText("1.00");
//			}

			if(coming_from.equalsIgnoreCase("pop_order"))
			{
				et_paynow_amount.setEnabled(false);
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void payment_summary(View view) {
		try
		{
			if(!et_paynow_amount.getText().toString().trim().matches(""))
			{
				double val = Double.parseDouble(et_paynow_amount.getText().toString().trim());
				if(val >= 1)
				{
					String amount = et_paynow_amount.getText().toString();

					if (HTTPUtils.isConnectionPossible(mContext))
					{
						amount = twoDigitRoundOff(amount);
						String payment_environment = "netbanking";

						if(rb_credit_card.isChecked())
						{
							payment_environment = "creditcard";
						}
						else if(rb_net_banking.isChecked())
						{
							payment_environment = "netbanking";
						}
						else if(rb_upi.isChecked())
						{
							payment_environment = "upi";
						}
						else if(rb_debit_card.isChecked())
						{
							payment_environment = "debitcard";
						}
						if (HTTPUtils.isConnectionPossible(mContext))
						{
							Intent intent = new Intent(mContext, PaymentSummaryActivity.class);
							intent.putExtra("payment_amount", val);
							intent.putExtra("payment_environment", payment_environment);
							intent.putExtra("coming_from", coming_from);
							startActivity(intent);
						}
						else
						{
							Utils_.closeApp(mContext,check_internet_connection);
						}
					}
					else
					{
						Utils_.closeApp(mContext,check_internet_connection);
					}
				}
				else
				{
					Toast.makeText(mContext, "Minimum amount Rs 1/-", Toast.LENGTH_SHORT).show();
				}
			}
			else
			{
				Toast.makeText(mContext, "Please enter a valid amount", Toast.LENGTH_SHORT).show();
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void show_info(View view) {
		try
		{
			final Dialog dialog = new Dialog(this, R.style.MyDialog);
			dialog.setContentView(R.layout.dialog_make_payment);


			dialog.setCancelable(true);
			dialog.setCanceledOnTouchOutside(true);
			dialog.show();

			Window window = dialog.getWindow();
			window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.WRAP_CONTENT);
			
			ImageView iv_paynow_close =  (ImageView) dialog.findViewById(R.id.iv_paynow_close);
			iv_paynow_close.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View view) {
					dialog.dismiss();
				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	@Override
	public void onBackPressed() {
		try
		{
			AlertDialog.Builder issueBuilder = new AlertDialog.Builder(mContext, R.style.MyDialog);

			TextView tvCPopup = new TextView(mContext);
			tvCPopup.setText(mContext.getResources().getString(R.string.app_name));
			tvCPopup.setGravity(Gravity.CENTER);
			tvCPopup.setTextColor(mContext.getResources().getColor(R.color.white));
			tvCPopup.setTextSize(14);
			tvCPopup.setBackgroundColor(mContext.getResources().getColor(R.color.red));
			int margin = 15;
			tvCPopup.setPadding(0, margin*2, 0, margin*2);
			issueBuilder.setCustomTitle(tvCPopup);
			issueBuilder.setMessage("\nDo you really want to exit?");

			issueBuilder.setPositiveButton("Yes", new DialogInterface.OnClickListener() {
				@Override
				public void onClick(DialogInterface dialog, int which) {
					dialog.dismiss();
					Intent intent = new Intent(mContext, MenuActivity.class);
					intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
					startActivity(intent);
					finish();

				}
			});
			issueBuilder.setNegativeButton("Continue", new DialogInterface.OnClickListener() {
				@Override
				public void onClick(DialogInterface dialog, int which) {
					dialog.dismiss();
					payment_summary(null);
				}
			});

			issueBuilder.setCancelable(true);
			issueBuilder.show();
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}
