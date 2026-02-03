package org.forcepower.starcement.util;

import static org.forcepower.starcement.Utils_.print_Log_d;

import android.content.Context;
import android.util.Base64;

import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.HttpHeaderParser;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;

import java.io.UnsupportedEncodingException;
import java.util.HashMap;
import java.util.Map;

/**
 * Created by @Amitabha,
 */

public final class VolleyApiCAll
{
    private Context context;

    public VolleyApiCAll(final Context context_) {
        context=context_;
    }

    public interface VolleyCallback {
        void onSuccessResponse(final String result);
    }
    public void makeServiceCall(final String url, final VolleyCallback callback)
    {

        final StringRequest strreq = new StringRequest(Request.Method.GET, url, new Response.Listener < String > ()
        {
            @Override
            public void onResponse(final String Response)
            {
                callback.onSuccessResponse(Response);
            }

        }, new Response.ErrorListener()
        {
            @Override
            public void onErrorResponse(final VolleyError e)
            {
                callback.onSuccessResponse("VOLLEY_NETWORK_ERROR");
                print_Log_d("VOLLEY_GET_RESPONSE_ERROR", e.toString());
            }
        })
        {
            /**
             * Passing some request headers
             * */
/*            @Override
            public Map<String, String> getHeaders() throws AuthFailureError
            {
                return createBasicAuthHeader("enter_username", "enter_password");
            }*/

            @Override
            protected Map<String, String> getParams()
            {
                final Map<String, String> params = new HashMap<>();
                params.put("Username", "enter_username");
                params.put("Password", "enter_password");
                params.put("grant_type", "password");

                return params;
            }
        };
        MySingleton.getInstance(context).addToRequestQueue(strreq);
    }
    private HashMap<String, String> createBasicAuthHeader(final String username, final String password)
    {
        final HashMap<String, String> headerMap = new HashMap<>();

        final String credentials = username + ":" + password;
        final String base64EncodedCredentials =
                Base64.encodeToString(credentials.getBytes(), Base64.NO_WRAP);
        headerMap.put("Authorization", "Basic " + base64EncodedCredentials);

        return headerMap;
    }
    final String BOUNDARY = "s2retfgsGSRFsERFGHfg";
    public void makeServiceCallPost(final Map<String, String> params, final String url, final VolleyCallback callback)
    {
        try
        {

            final RequestQueue requestQueue = Volley.newRequestQueue(context);

            print_Log_d("VOLLEY_POST_url", url+"");
            final StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>()
            {
                @Override
                public void onResponse(final String response)
                {
                    callback.onSuccessResponse(response);
                    print_Log_d("VOLLEY_POST_RESPONSE_SUCCESS", response);
                }
            }, new Response.ErrorListener()
            {
                @Override
                public void onErrorResponse(final VolleyError e)
                {
                    callback.onSuccessResponse("VOLLEY_NETWORK_ERROR");
                    print_Log_d("VOLLEY_POST_RESPONSE_ERROR", e.toString());
                }
            })
            {
                /**
                 * Passing some request headers
                 * */
                @Override
                public Map<String, String> getHeaders() throws AuthFailureError {
                    final Map<String,String> params = new HashMap<>();

                    params.put("Content-Type", "multipart/form-data; boundary=" + BOUNDARY+"; charset=utf-8");
                    return params;
                }

                @Override
                public String getBodyContentType() {
                    return "multipart/form-data; charset=utf-8";
                }

                @Override
                public byte[] getBody() throws AuthFailureError
                {
                    try
                    {
                        final String postBody = createPostBody(params);

                        return postBody.getBytes("utf-8");
                    }
                    catch (final NegativeArraySizeException n)
                    {
                        n.printStackTrace();
                        return null;
                    }
                    catch (final UnsupportedEncodingException uee)
                    {
                        print_Log_d("POST_ERROR_1", uee.toString());
                        uee.printStackTrace();
                        return null;
                    }
                }

                @Override
                protected Response<String> parseNetworkResponse(final NetworkResponse response)
                {
                    print_Log_d("VOLLEY_POST_1", response.headers.toString());
                    String responseString = "post_error";
                    try {
                        responseString = new String(response.data, HttpHeaderParser.parseCharset(response.headers));
                    } catch (Exception e) {
                        print_Log_d("VOLLEY_POST_2", e.toString());
                        e.printStackTrace();
                    }
                    return Response.success(responseString, HttpHeaderParser.parseCacheHeaders(response));
                }
            };
            stringRequest.setRetryPolicy(new DefaultRetryPolicy(
                    5000,
                    DefaultRetryPolicy.DEFAULT_MAX_RETRIES,
                    DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
            requestQueue.add(stringRequest);
        }
        catch (final Exception n)
        {
            n.printStackTrace();
        }
    }
    public void makeServiceCallPost_Time(final Map<String, String> params, final String url, final VolleyCallback callback)
    {
        try
        {

            final RequestQueue requestQueue = Volley.newRequestQueue(context);

            print_Log_d("VOLLEY_POST_url", url+"");
            final StringRequest stringRequest = new StringRequest(Request.Method.POST, url, new Response.Listener<String>()
            {
                @Override
                public void onResponse(final String response)
                {
                    callback.onSuccessResponse(response);
                    print_Log_d("VOLLEY_POST_RESPONSE_SUCCESS", response);
                }
            }, new Response.ErrorListener()
            {
                @Override
                public void onErrorResponse(final VolleyError e)
                {
                    callback.onSuccessResponse("VOLLEY_NETWORK_ERROR");
                    print_Log_d("VOLLEY_POST_RESPONSE_ERROR", e.toString());
                }
            })
            {
                /**
                 * Passing some request headers
                 * */
                @Override
                public Map<String, String> getHeaders() throws AuthFailureError {
                    final Map<String,String> params = new HashMap<>();

                    params.put("Content-Type", "multipart/form-data; boundary=" + BOUNDARY+"; charset=utf-8");
                    return params;
                }

                @Override
                public String getBodyContentType() {
                    return "multipart/form-data; charset=utf-8";
                }

                @Override
                public byte[] getBody() throws AuthFailureError
                {
                    try
                    {
                        final String postBody = createPostBody(params);

                        return postBody.getBytes("utf-8");
                    }
                    catch (final NegativeArraySizeException n)
                    {
                        n.printStackTrace();
                        return null;
                    }
                    catch (final UnsupportedEncodingException uee)
                    {
                        print_Log_d("POST_ERROR_1", uee.toString());
                        uee.printStackTrace();
                        return null;
                    }
                }

                @Override
                protected Response<String> parseNetworkResponse(final NetworkResponse response)
                {
                    print_Log_d("VOLLEY_POST_1", response.headers.toString());
                    String responseString = "post_error";
                    try {
                        responseString = new String(response.data, HttpHeaderParser.parseCharset(response.headers));
                    } catch (Exception e) {
                        print_Log_d("VOLLEY_POST_2", e.toString());
                        e.printStackTrace();
                    }
                    return Response.success(responseString, HttpHeaderParser.parseCacheHeaders(response));
                }
            };
            stringRequest.setRetryPolicy(new DefaultRetryPolicy(
                    55000,
                    DefaultRetryPolicy.DEFAULT_MAX_RETRIES,
                    DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
            requestQueue.add(stringRequest);
        }
        catch (final Exception n)
        {
            n.printStackTrace();
        }
    }
    private String createPostBody(final Map<String, String> params) {
        final StringBuilder sbPost = new StringBuilder();
        for (final String key : params.keySet()) {
            if (params.get(key) != null) {
                sbPost.append("\r\n" + "--" + BOUNDARY + "\r\n");
                sbPost.append("Content-Disposition: form-data; name=\"").append(key).append("\"").append("\r\n\r\n");
                sbPost.append(params.get(key));
            }
        }

        return sbPost.toString();
    }
}
