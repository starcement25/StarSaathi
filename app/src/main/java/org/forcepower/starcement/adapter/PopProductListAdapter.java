package org.forcepower.starcement.adapter;

import static org.forcepower.starcement.SharedPrefData.get_dns_emp_code;

import android.app.Activity;
import android.app.Dialog;
import android.content.Context;
import android.text.Editable;
import android.text.InputFilter;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.annotation.NonNull;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.PopProductModel;
import org.forcepower.starcement.constants.Constants;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Locale;

public final class PopProductListAdapter extends ArrayAdapter<PopProductModel> {
    private final Activity mContext;
    private ArrayList<PopProductModel> nameValuesProductListLocal;
    private TextView tvNotiCount;

    public PopProductListAdapter(final Activity mContext,final ArrayList<PopProductModel> nameValues,
                                 final TextView tvNotiCount)
    {
        super(mContext,0,nameValues);
        this.mContext = mContext;
        this.nameValuesProductListLocal = nameValues;
        this.tvNotiCount = tvNotiCount;
    }

    @Override
    public int getCount() {
        return nameValuesProductListLocal.size();
    }

    @Override
    public PopProductModel getItem(int position) {
        return nameValuesProductListLocal.get(position);
    }
    @Override
    public long getItemId(int position) {
        return position;
    }
    @Override
    public int getViewTypeCount() {

        return getCount();
    }

    @Override
    public int getItemViewType(int position) {

        return position;
    }
    @NonNull
    @Override
    public View getView(final int position, View convertView, @NonNull ViewGroup parent)
    {
        ViewHolder viewHolder;
        if (convertView == null)
        {
            final LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(R.layout.list_item_pop_product, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.img_prod = (ImageView) convertView.findViewById(R.id.img_prod);
            viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.tv_pop_price = (TextView) convertView.findViewById(R.id.tv_pop_price);
            viewHolder.tv_pop_gst = (TextView) convertView.findViewById(R.id.tv_pop_gst);
            viewHolder.tv_min_order_qty = (TextView) convertView.findViewById(R.id.tv_min_order_qty);

            viewHolder.etProdQty_new = (EditText) convertView.findViewById(R.id.etProdQty_new);
            viewHolder.etProdQty_new.setFilters(new InputFilter[]{new CustomRangeInputFilter(0, 9999)});
            convertView.setTag(viewHolder);
        }
        else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        try
        {

            viewHolder.txtViewProductDesc.setText(nameValuesProductListLocal.get(position).getProd_desc()+"");
            viewHolder.tv_pop_price.setText(nameValuesProductListLocal.get(position).getPrice_per_piece()+"");
            viewHolder.tv_pop_gst.setText(nameValuesProductListLocal.get(position).getGST_rate()+"");
            viewHolder.tv_min_order_qty.setText(nameValuesProductListLocal.get(position).getMin_order_qty()+"");
            viewHolder.etProdQty_new.setText(nameValuesProductListLocal.get(position).get_qty()+"");

            viewHolder.img_prod.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    show_zoom(position);
                }
            });
            viewHolder.etProdQty_new.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                }

                @Override
                public void onTextChanged(CharSequence editable, int start, int before, int count) {
                    try
                    {
                        final String text = editable.toString()+"";
                        if(!text.matches(""))
                        {
                            final double val = Double.parseDouble(text);
                            if(val > 0)
                            {
                                nameValuesProductListLocal.get(position).set_qty(text+"");

                                final String global_timeStamp = Constants.dateString+ new SimpleDateFormat("HHmmss", Locale.getDefault()).format(Calendar.getInstance().getTime());
                                nameValuesProductListLocal.get(position).set_u_order_id("POP"+get_dns_emp_code(mContext)+ global_timeStamp+""+nameValuesProductListLocal.get(position).getDns_prod_code());
                                Constants.selectedList.put(nameValuesProductListLocal.get(position).getDns_prod_code(),nameValuesProductListLocal.get(position));
                            }
                            else
                            {
                                nameValuesProductListLocal.get(position).set_qty("");
                                Constants.selectedList.remove(nameValuesProductListLocal.get(position).getDns_prod_code());
                            }
                        }
                        else
                        {
                            nameValuesProductListLocal.get(position).set_qty("");
                            Constants.selectedList.remove(nameValuesProductListLocal.get(position).getDns_prod_code());
                        }
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                    finally
                    {
                        tvNotiCount.setText(Constants.selectedList.size()+"");
                    }
                }

                @Override
                public void afterTextChanged(Editable editable) {

                }
            });
            Glide.with(mContext).
                    load(nameValuesProductListLocal.get(position).getProd_image())
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .error(R.drawable.default_)
                    .placeholder(R.drawable.default_)
                    .into(viewHolder.img_prod);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return convertView;
    }

    private class ViewHolder
    {
        private TextView txtViewProductDesc, tv_pop_price, tv_pop_gst, tv_min_order_qty;
        private EditText etProdQty_new;
        private ImageView img_prod;
    }
    public void show_zoom(final int position)
    {
        try
        {
            final Dialog dialog = new Dialog(mContext, R.style.MyDialog);
            dialog.setContentView(R.layout.dialog_imageview);


            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(true);
            dialog.show();

            final TextView tv_zoom_name =  (TextView) dialog.findViewById(R.id.tv_zoom_name);
            tv_zoom_name.setText(nameValuesProductListLocal.get(position).getProd_desc());
            final ImageView iv_zoom_cross =  (ImageView) dialog.findViewById(R.id.iv_zoom_cross);
            final ImageView iv_zoom_rotate =  (ImageView) dialog.findViewById(R.id.iv_zoom_rotate);
            final ImageView iv_zoom =  (ImageView) dialog.findViewById(R.id.iv_zoom);
            iv_zoom_cross.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    dialog.dismiss();
                }
            });
            iv_zoom_rotate.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    iv_zoom_rotate.setRotation(iv_zoom_rotate.getRotation()+90);
                    iv_zoom.setRotation(iv_zoom_rotate.getRotation()+90);
                }
            });
            Glide.with(mContext).
                    load(nameValuesProductListLocal.get(position).getProd_image())
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
}
