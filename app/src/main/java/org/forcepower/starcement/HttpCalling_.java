package org.forcepower.starcement;

import static org.forcepower.starcement.Utils_.print_Log_d;

import org.forcepower.starcement.util.MCrypt;

import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

/**
 * Created by Suvradip on 24/10/2017.
 */

public final class HttpCalling_
{
    public static final MediaType JSON= MediaType.parse("application/json; charset=utf-8");
    private OkHttpClient client;

    public HttpCalling_() {
        client = new OkHttpClient();
//        client = initClient();
    }

    public String httpPostCallWithXmlResponseDecrypted(final String url, final String json) {
        String responseFromServer ="";
        try {

            final RequestBody body = RequestBody.create(JSON, json);
            final Request request = new Request.Builder()
                    .url(url)
                    .post(body)
                    .build();
            final Response response = client.newCall(request).execute();
            responseFromServer =response.body().string();
            final MCrypt mcrypt = new MCrypt();
            responseFromServer =new String( mcrypt.decrypt(responseFromServer) );
        }
        catch (Exception e)
        {
            e.printStackTrace();
            responseFromServer ="Network Failure";
            print_Log_d("_orur4_L_O_error_61 " , e.toString());
        }

        return responseFromServer;
    }
}
