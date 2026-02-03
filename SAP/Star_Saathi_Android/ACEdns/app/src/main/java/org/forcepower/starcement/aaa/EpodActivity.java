package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_server_current_date;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.save_epod_details;
import static org.forcepower.starcement.util.Utils.getFileContent;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.Manifest;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.provider.Settings;
import android.util.Base64;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.annotation.RequiresApi;
import androidx.core.app.ActivityCompat;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import org.forcepower.starcement.CategoryItem;
import org.forcepower.starcement.MarshMallowPermission;
import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.EpodAdapter_Date;
import org.forcepower.starcement.adapter.EpodInputAdapter_Date;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;
import my_crop.vola.ImagePickerActivity;

public final class EpodActivity extends AceDnsParentActivity
{

	private Activity mContext;

	private String is_delivered = "YES";

	private MarshMallowPermission mMP;

	private ArrayList<CategoryItem> invoice_list = new ArrayList<>();
	private EpodAdapter_Date mAdapter;
	private RecyclerView recycler_view;

	private ArrayList<CategoryItem> invoicelist = new ArrayList<>();
	private EpodInputAdapter_Date eAdapter;
	private RecyclerView rvPending;
	
	public Bitmap stringToBitmap(String base64String) {
		byte[] decodedBytes = Base64.decode(base64String, Base64.DEFAULT);
		return BitmapFactory.decodeByteArray(decodedBytes, 0, decodedBytes.length);
	}

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_epod);
		try
		{
//			final String sqlCode = getFileContent(getResources(), R.raw.test);
			mContext = this;
			mMP = new MarshMallowPermission(mContext);
//			show_zoom(stringToBitmap(sqlCode));
			final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("EPOD");


			final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});

			final TextView tv_challan_number =  (TextView) findViewById(R.id.tv_challan_number);
			tv_challan_number.setText("Challan Number : " + getIntent().getStringExtra("challan_no"));

			final TextView tv_challan_date =  (TextView) findViewById(R.id.tv_challan_date);
			tv_challan_date.setText("Challan Date : " + getIntent().getStringExtra("challan_date"));

			final Button tv_challan_del_yes =  (Button) findViewById(R.id.tv_challan_del_yes);
			final Button tv_challan_del_no =  (Button) findViewById(R.id.tv_challan_del_no);
			tv_challan_del_no.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					is_delivered = "NO";
					eAdapter.setFilter(invoicelist, is_delivered);
					tv_challan_del_no.setBackgroundResource(R.drawable.gray_border_solid_black_bg);
					tv_challan_del_no.setTextColor(Color.WHITE);
					tv_challan_del_yes.setBackgroundResource(R.drawable.rounded_solid);
					tv_challan_del_yes.setTextColor(Color.BLACK);
				}
			});
			tv_challan_del_yes.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					is_delivered = "YES";
					eAdapter.setFilter(invoicelist, is_delivered);
					tv_challan_del_yes.setBackgroundResource(R.drawable.gray_border_solid_black_bg);
					tv_challan_del_yes.setTextColor(Color.WHITE);
					tv_challan_del_no.setBackgroundResource(R.drawable.rounded_solid);
					tv_challan_del_no.setTextColor(Color.BLACK);
				}
			});

			final TextView tv_Ch_Submit = (TextView) findViewById(R.id.tv_Ch_Submit);
			tv_Ch_Submit.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					if(HTTPUtils.isConnectionPossible(mContext))
					{
						new Sample_AsyncTask(mContext).execute();
					}
					else
					{
						show_msg_alert(mContext, check_internet_connection, false);
					}
				}
			});

			recycler_view = (RecyclerView) findViewById(R.id.recycler_view);
			recycler_view.setHasFixedSize(true);
			recycler_view.setLayoutManager(new GridLayoutManager(mContext, 4));

			invoice_list.clear();
			for(int i=0; i<4; i++)
			{

				final CategoryItem iM = new CategoryItem();
                iM.setSelected(i == 0);
				invoice_list.add(iM);
			}
			mAdapter = new EpodAdapter_Date(mContext, invoice_list);
			recycler_view.setAdapter(mAdapter);

			//
			rvPending = (RecyclerView) findViewById(R.id.recyclerview);
			rvPending.setHasFixedSize(true);
			rvPending.setLayoutManager(new LinearLayoutManager(mContext));

			invoicelist.clear();
			for(int i=0; i<1; i++)
			{

				final CategoryItem iM = new CategoryItem();
				invoicelist.add(iM);
			}
			eAdapter = new EpodInputAdapter_Date(mContext, invoicelist, is_delivered);
			rvPending.setAdapter(eAdapter);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
	public void setValue(int count)
	{
		invoicelist.clear();
		for(int i=0; i<count; i++)
		{

			final CategoryItem iM = new CategoryItem();
			invoicelist.add(iM);
		}
		eAdapter.setFilter(invoicelist, is_delivered);
	}
	private void requestPermission(final int top_or_bottom)
	{
		ActivityCompat.requestPermissions(this, new String[]{
				Manifest.permission.CAMERA
		}, top_or_bottom);
	}

	private File outPutFile = new File("");
	public void chooseYourImage(final int top_or_bottom)
	{
		//for Runtime permission
		final CharSequence[] items = {"Camera", "Gallery", "View Photo", "Remove Photo"};

		AlertDialog.Builder builder = new AlertDialog.Builder(mContext, R.style.MyDialog);
		TextView tvCPopup = new TextView(mContext);
		tvCPopup.setText("Upload/View your picture");
		tvCPopup.setGravity(Gravity.CENTER);
		tvCPopup.setTextColor(mContext.getResources().getColor(R.color.white));
		tvCPopup.setTextSize(14);
		tvCPopup.setBackgroundColor(mContext.getResources().getColor(R.color.red));
		int margin = 15;
		tvCPopup.setPadding(0, margin*2, 0, margin*2);
		builder.setCustomTitle(tvCPopup);
		builder.setItems(items, new DialogInterface.OnClickListener() {
			@Override
			public void onClick(DialogInterface dialog, int item) {
				try
				{
					if (items[item].equals("Camera"))
					{
						try
						{
							if (!mMP.checkPermissionForCamera())
							{
								requestPermission(top_or_bottom);
							}
							else
							{
								openCamera(top_or_bottom);
							}
						}
						catch (Exception e)
						{
							e.printStackTrace();
						}
					}
					else if (items[item].equals("Gallery"))
					{
						try
						{
							Intent intent = new Intent(getApplicationContext(), ImagePickerActivity.class);
							intent.putExtra(ImagePickerActivity.INTENT_IMAGE_PICKER_OPTION, ImagePickerActivity.REQUEST_GALLERY_IMAGE);

							// setting aspect ratio
							intent.putExtra(ImagePickerActivity.INTENT_LOCK_ASPECT_RATIO, true);
							intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_X, 1); // 16x9, 1x1, 3:4, 3:2
							intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_Y, 1);
							mContext.startActivityForResult(intent, top_or_bottom);
						}
						catch (Exception e)
						{
							e.printStackTrace();
						}
					}
					else if (items[item].equals("View Photo"))
					{
						show_zoom(top_or_bottom);
					}
					else if (items[item].equals("Remove Photo"))
					{
						if(top_or_bottom == 111)
						{
//							img_1_top.setImageResource(0);
//							img_1_top.setTag("");
						}
						else if(top_or_bottom == 222)
						{
//							img_2_track.setImageResource(0);
//							img_2_track.setTag("");
						}
					}
				}
				catch (Exception e)
				{
					e.printStackTrace();
				}
				finally
				{
					dialog.dismiss();
				}
			}
		});
		builder.setNegativeButton("CANCEL", new DialogInterface.OnClickListener() {

			public void onClick(DialogInterface dialog, int which) {
				dialog.dismiss();
			}
		});
		builder.show();
	}

	public void openCamera(final int top_or_bottom)
	{
		try
		{
			Intent intent = new Intent(this, ImagePickerActivity.class);
			intent.putExtra(ImagePickerActivity.INTENT_IMAGE_PICKER_OPTION, ImagePickerActivity.REQUEST_IMAGE_CAPTURE);

			// setting aspect ratio
			intent.putExtra(ImagePickerActivity.INTENT_LOCK_ASPECT_RATIO, true);
			intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_X, 1); // 16x9, 1x1, 3:4, 3:2
			intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_Y, 1);

			// setting maximum bitmap width and height
			intent.putExtra(ImagePickerActivity.INTENT_SET_BITMAP_MAX_WIDTH_HEIGHT, true);
			intent.putExtra(ImagePickerActivity.INTENT_BITMAP_MAX_WIDTH, 1000);
			intent.putExtra(ImagePickerActivity.INTENT_BITMAP_MAX_HEIGHT, 1000);

			mContext.startActivityForResult(intent, top_or_bottom);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
	public final class Sample_AsyncTask extends AsyncTaskCoroutine<String, String>
	{

		private Activity mContext;
		private JSONObject jo = new JSONObject();
		public Sample_AsyncTask(final Activity context)
		{
			this.mContext = context;
		}

		@Override
		public void onPreExecute()
		{
			super.onPreExecute();
			Utils.changeProgressDialogMsg(mContext, "Please wait..");
		}

		@Override
		public String doInBackground(String... par)
		{
			String POST_result = "";
			try
			{
				final String url = save_epod_details;
				print_log_d("PRINT_save_epod_details_", url);
				final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(1);
				if(get_user_type(mContext).equalsIgnoreCase("broker"))
				{
					nameValuePairs.add(new BasicNameValuePair("customer_id", get_selected_dealer_sap_code(mContext)));
				}
				else
				{
					nameValuePairs.add(new BasicNameValuePair("customer_id", get_dealer_id(mContext)));
				}

				nameValuePairs.add(new BasicNameValuePair("epod_data[0][challan_no]",  getIntent().getStringExtra("challan_no")));
				nameValuePairs.add(new BasicNameValuePair("epod_data[0][date_and_time]", getIntent().getStringExtra("challan_date")));
				nameValuePairs.add(new BasicNameValuePair("epod_data[0][challan_date]", getIntent().getStringExtra("challan_date")));
				nameValuePairs.add(new BasicNameValuePair("epod_data[0][is_delivered]", is_delivered));
//				nameValuePairs.add(new BasicNameValuePair("epod_data[0][total_order]", tv_total_qty.getText().toString()));
//				nameValuePairs.add(new BasicNameValuePair("epod_data[0][dispatched_order]", tv_ch_dispatch_order.getText().toString()));
//				nameValuePairs.add(new BasicNameValuePair("epod_data[0][received_order]", tv_ch_rcvd_order.getText().toString()));
//				nameValuePairs.add(new BasicNameValuePair("epod_data[0][pending_order]", et_ch_pending_order.getText().toString()));
//				nameValuePairs.add(new BasicNameValuePair("epod_data[0][delivery_qty]", tv_ch_delivery_qty.getText().toString()));
//				nameValuePairs.add(new BasicNameValuePair("epod_data[0][remarks]", et_ch_delivery_remarks.getText().toString()));

				POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);
				jo = new JSONObject(POST_result);
			}
			catch (Exception e)
			{
				e.printStackTrace();
			}

			return null;
		}

		@Override
		public void onPostExecute(String result)
		{
			super.onPostExecute(result);
			try
			{
				if(jo.optString("process_status").equalsIgnoreCase("No"))
				{
					show_msg_alert(mContext, jo.optString("process_message"), false);
				}
				else
				{
					show_msg_alert(mContext, jo.optString("process_message"), true);
				}
			}
			catch (Exception e)
			{
				e.printStackTrace();
			}
			finally
			{
				Utils.cancelProgressDialog();
			}
		}
	}

	public void show_zoom(final Bitmap bitmap)
	{
		try
		{

			final Dialog dialog = new Dialog(mContext, R.style.MyDialog);
			dialog.setContentView(R.layout.dialog_imageview);


			dialog.setCancelable(true);
			dialog.setCanceledOnTouchOutside(true);
			dialog.show();

			final TextView tv_zoom_name =  (TextView) dialog.findViewById(R.id.tv_zoom_name);
			tv_zoom_name.setText("EPOD selected image");
			final ImageView iv_zoom_cross =  (ImageView) dialog.findViewById(R.id.iv_zoom_cross);
			final ImageView iv_zoom_rotate =  (ImageView) dialog.findViewById(R.id.iv_zoom_rotate);
			final ImageView iv_zoom =  (ImageView) dialog.findViewById(R.id.iv_zoom);
			iv_zoom_cross.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					dialog.dismiss();
				}
			});
			iv_zoom_rotate.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					iv_zoom_rotate.setRotation(iv_zoom_rotate.getRotation()+90);
					iv_zoom.setRotation(iv_zoom_rotate.getRotation()+90);
				}
			});

			iv_zoom.setImageBitmap(bitmap);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
	public void show_zoom(final int top_or_bottom)
	{
		try
		{
			Uri img_path = null;
			if(top_or_bottom == 111)
			{
//				img_path = Uri.parse(img_1_top.getTag().toString());
			}
			else if(top_or_bottom == 222)
			{
//				img_path = Uri.parse(img_2_track.getTag().toString());
			}
			final Dialog dialog = new Dialog(mContext, R.style.MyDialog);
			dialog.setContentView(R.layout.dialog_imageview);


			dialog.setCancelable(true);
			dialog.setCanceledOnTouchOutside(true);
			dialog.show();

			final TextView tv_zoom_name =  (TextView) dialog.findViewById(R.id.tv_zoom_name);
			tv_zoom_name.setText("EPOD selected image");
			final ImageView iv_zoom_cross =  (ImageView) dialog.findViewById(R.id.iv_zoom_cross);
			final ImageView iv_zoom_rotate =  (ImageView) dialog.findViewById(R.id.iv_zoom_rotate);
			final ImageView iv_zoom =  (ImageView) dialog.findViewById(R.id.iv_zoom);
			iv_zoom_cross.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					dialog.dismiss();
				}
			});
			iv_zoom_rotate.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					iv_zoom_rotate.setRotation(iv_zoom_rotate.getRotation()+90);
					iv_zoom.setRotation(iv_zoom_rotate.getRotation()+90);
				}
			});
			Glide.with(mContext).
					load(img_path)
					.diskCacheStrategy(DiskCacheStrategy.NONE)
					.placeholder(android.R.drawable.progress_indeterminate_horizontal) //place holder
					.error(R.drawable.default_)
					.into(iv_zoom);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}


	public byte[] uriToByteArray(final Uri uri) throws IOException
	{
		InputStream inputStream = null;
		byte[] byteArray = null;

		try
		{
			inputStream = getContentResolver().openInputStream(uri);
			byteArray = readBytes(inputStream);
		}
		finally
		{
			if (inputStream != null)
			{
				inputStream.close();
			}
		}

		return byteArray;
	}

	private byte[] readBytes(final InputStream inputStream) throws IOException
	{
		final ByteArrayOutputStream byteBuffer = new ByteArrayOutputStream();
		final int bufferSize = 1024;
		final byte[] buffer = new byte[bufferSize];

		int len = 0;
		while ((len = inputStream.read(buffer)) != -1)
		{
			byteBuffer.write(buffer, 0, len);
		}

		return byteBuffer.toByteArray();
	}

	public String uriToBase64String(final Uri uri) throws IOException
	{
		final byte[] byteArray = uriToByteArray(uri);
		return Base64.encodeToString(byteArray, Base64.DEFAULT);
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data)
	{
		super.onActivityResult(requestCode, resultCode, data);
		if (requestCode == 111 || requestCode == 222)
		{
			if (resultCode == Activity.RESULT_OK)
			{
				try
				{
					final Uri uri = data.getParcelableExtra("path");
					outPutFile = new File(uri.getPath());

					if (requestCode == 111)
					{
//						img_1_top.setImageURI(uri);
//						img_1_top.setTag(uri);

						try
						{
							final String base64String = uriToBase64String(uri);

							print_log_d("base64String ", base64String);
						}
						catch (IOException e)
						{
							e.printStackTrace();
						}
                        print_log_d("got it", "");
					}
					else
					{
//						img_2_track.setImageURI(uri);
//						img_2_track.setTag(uri);
					}


				}
				catch (Exception e)
				{
					e.printStackTrace();
				}
			}
		}
	}

	@RequiresApi(api = Build.VERSION_CODES.R)
	@Override
	public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults)
	{
		super.onRequestPermissionsResult(requestCode, permissions, grantResults);
		switch(requestCode)
		{
			case 111:
				if (mMP.checkPermissionForCamera())
				{
					openCamera(111);
				}
				else
				{
					Intent intent = new Intent(Settings.ACTION_MANAGE_APP_ALL_FILES_ACCESS_PERMISSION);
					intent.addCategory("android.intent.category.DEFAULT");
					intent.setData(Uri.parse(String.format("package:%s", getApplicationContext().getPackageName())));
					mContext.startActivityForResult(intent, 111);
				}
				break;
			case 222:
				if (mMP.checkPermissionForCamera())
				{
					openCamera(222);
				}
				else
				{
					Intent intent = new Intent(Settings.ACTION_MANAGE_APP_ALL_FILES_ACCESS_PERMISSION);
					intent.addCategory("android.intent.category.DEFAULT");
					intent.setData(Uri.parse(String.format("package:%s", getApplicationContext().getPackageName())));
					mContext.startActivityForResult(intent, 222);
				}
				break;
		}
	}
}
