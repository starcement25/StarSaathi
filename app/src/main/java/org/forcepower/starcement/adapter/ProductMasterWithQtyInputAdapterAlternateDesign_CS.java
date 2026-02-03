package org.forcepower.starcement.adapter;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;

import android.content.Context;
import android.text.Editable;
import android.text.InputFilter;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.TextView;

import androidx.annotation.NonNull;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.ProductMasterDetails;
import org.forcepower.starcement.constants.Constants;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public final class ProductMasterWithQtyInputAdapterAlternateDesign_CS extends ArrayAdapter<ProductMasterDetails>
{
    private final Context context;
    private ArrayList<ProductMasterDetails> nameValuesProductListLocal;
    private TextView tv_ttl_qty;

    public ProductMasterWithQtyInputAdapterAlternateDesign_CS(final Context context,
                                                              final int resourceId,
                                                              final ArrayList<ProductMasterDetails> nameValues,
                                                              final TextView tv_ttl_qty) {
        super(context,resourceId,nameValues);
        this.context = context;
        this.tv_ttl_qty = tv_ttl_qty;
        nameValuesProductListLocal = nameValues;
    }
    @Override
    public int getCount() {
        return nameValuesProductListLocal.size();
    }
    @NonNull
    @Override
    public View getView(final int position, View convertView, @NonNull ViewGroup parent)
    {
        ViewHolder viewHolder;
        if (convertView == null)
        {
            final LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(R.layout.list_item__product_with_quantity_input_alternate_design, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.tv_input_type = (TextView) convertView.findViewById(R.id.tv_input_type);

            viewHolder.etProdQty_new = (EditText) convertView.findViewById(R.id.etProdQty_new);
            viewHolder.etProdQty_new.setFilters(new InputFilter[]{new CustomRangeInputFilter(0, 9999)});
//            convertView.setTag(viewHolder); //vola2715 need to rectify while free
        }
        else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        try
        {

            viewHolder.txtViewProductDesc.setText(nameValuesProductListLocal.get(position).getDesc()+"");
            viewHolder.tv_input_type.setText("QTY (Bags)");
            viewHolder.etProdQty_new.setText("");

            viewHolder.etProdQty_new.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                }

                @Override
                public void onTextChanged(CharSequence editable, int start, int before, int count) {
                    try
                    {
                        final String text = editable.toString().trim()+"";
                        if(text.matches("\\."))
                        {
                            nameValuesProductListLocal.get(position).setQty("");
                            Constants.selectedProductMasterList.remove(nameValuesProductListLocal.get(position).getProdCode());
                        }
                        else if(!text.matches(""))
                        {
                            final double val = Double.parseDouble(text);
                            if(val > 0)
                            {
                                nameValuesProductListLocal.get(position).setQty(text+"");

                                final String global_timeStamp = Constants.dateString+ new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                                nameValuesProductListLocal.get(position).set_u_order_id("O"+get_emp_or_customer_code(context)+ global_timeStamp+""+nameValuesProductListLocal.get(position).getProdCode());
                                Constants.selectedProductMasterList.put(nameValuesProductListLocal.get(position).getProdCode(),nameValuesProductListLocal.get(position));
                            }
                            else
                            {
                                nameValuesProductListLocal.get(position).setQty("");
                                Constants.selectedProductMasterList.remove(nameValuesProductListLocal.get(position).getProdCode());
                            }
                        }
                        else
                        {
                            nameValuesProductListLocal.get(position).setQty("");
                            Constants.selectedProductMasterList.remove(nameValuesProductListLocal.get(position).getProdCode());
                        }


                        String dm = "", ws = "";
                        if(Constants.selectedProductMasterList.containsKey("dhalai_master"))
                        {
                            dm = Constants.selectedProductMasterList.get("dhalai_master").getQty();
                        }

                        if(Constants.selectedProductMasterList.containsKey("weather_shield"))
                        {
                            ws = Constants.selectedProductMasterList.get("weather_shield").getQty();
                        }

                        if(dm.isEmpty())
                            dm = "0";
                        if(ws.isEmpty())
                            ws = "0";

                        final int ttl1 = Integer.parseInt(dm);
                        final int ttl2 = Integer.parseInt(ws);
                        tv_ttl_qty.setText((ttl1 + ttl2)+ "");
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                }

                @Override
                public void afterTextChanged(Editable editable) {


                }
            });

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return convertView;
    }

    private class ViewHolder
    {
        private TextView txtViewProductDesc, tv_input_type;
        private EditText etProdQty_new;
    }
}
