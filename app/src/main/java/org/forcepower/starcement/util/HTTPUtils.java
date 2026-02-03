package org.forcepower.starcement.util;

import static org.forcepower.starcement.Utils_.ApiRes;
import static org.forcepower.starcement.Utils_.print_Log_d;

import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;

import org.json.JSONObject;

import java.net.SocketTimeoutException;
import java.util.ArrayList;
import java.util.concurrent.TimeUnit;

import cz.msebera.android.httpclient.NameValuePair;
import okhttp3.FormBody;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public final class HTTPUtils {

	// Shared OkHttpClient without logging interceptor
	private static volatile OkHttpClient sharedClient = null;

	private static OkHttpClient getSharedClient() {
		if (sharedClient == null) {
			synchronized (HTTPUtils.class) {
				if (sharedClient == null) {
					sharedClient = new OkHttpClient.Builder()
							.connectTimeout(30, TimeUnit.SECONDS)
							.readTimeout(60, TimeUnit.SECONDS)
							.writeTimeout(60, TimeUnit.SECONDS)
							.retryOnConnectionFailure(true)
							.build();
				}
			}
		}
		return sharedClient;
	}

	private static OkHttpClient getLongTimeoutClient() {
		return new OkHttpClient.Builder()
				.connectTimeout(35, TimeUnit.SECONDS)
				.readTimeout(120, TimeUnit.SECONDS)
				.writeTimeout(120, TimeUnit.SECONDS)
				.retryOnConnectionFailure(true)
				.build();
	}

	public static boolean isConnectionPossible(final Context mContext) {
		try {
			ConnectivityManager cm = (ConnectivityManager)
					mContext.getSystemService(Context.CONNECTIVITY_SERVICE);
			NetworkInfo netInfo = cm != null ? cm.getActiveNetworkInfo() : null;
			return (netInfo != null && netInfo.isConnected());
		} catch (Exception e) {
			print_Log_d("HTTPUtils_NetworkError", e.toString());
			return false;
		}
	}

	public static String getDataByHTTP_GET(final Context context, final String url) {
		print_Log_d("HTTP_GET_URL", url);

		if (!isConnectionPossible(context)) {
			return ApiRes("Network Failure");
		}

		String responseFromServer = "Network Failure";
		OkHttpClient client = getSharedClient();

		Request request = new Request.Builder().url(url).build();

		try (Response response = client.newCall(request).execute()) {
			if (response != null && response.body() != null) {
				responseFromServer = response.body().string();
			}
		} catch (SocketTimeoutException e) {
			print_Log_d("HTTP_GET_TIMEOUT", e.toString());
		} catch (Exception e) {
			print_Log_d("HTTP_GET_EXCEPTION", e.toString());
		}

		return ApiRes(responseFromServer);
	}

	public static String getDataByHTTP_POST(final Context context, final String url,
											final ArrayList<NameValuePair> nameValuePairs) {

		print_Log_d("HTTP_POST_URL", url);

		if (!isConnectionPossible(context)) {
			return ApiRes("Network Failure");
		}

		String responseFromServer = "Network Failure";
		OkHttpClient client = getSharedClient();

		try {
			FormBody.Builder formBuilder = new FormBody.Builder();

			if (nameValuePairs != null) {
				for (NameValuePair pair : nameValuePairs) {
					formBuilder.add(
							pair.getName() == null ? "" : pair.getName(),
							pair.getValue() == null ? "" : pair.getValue()
					);
				}
			}

			RequestBody formBody = formBuilder.build();

			Request request = new Request.Builder()
					.url(url)
					.post(formBody)
					.build();

			try (Response response = client.newCall(request).execute()) {
				if (response != null && response.body() != null) {
					responseFromServer = response.body().string();
				}
			}
		} catch (SocketTimeoutException e) {
			print_Log_d("HTTP_POST_TIMEOUT", e.toString());
		} catch (Exception e) {
			print_Log_d("HTTP_POST_EXCEPTION", e.toString());
		}

		return ApiRes(responseFromServer);
	}

	public static String getDataByHTTP_POST1(final Context context, final String url,
											 final JSONObject nameValuePairs) {

		if (!isConnectionPossible(context)) {
			return ApiRes("Network Failure");
		}

		String responseFromServer = "Network Failure";
		OkHttpClient client = getSharedClient();

		try {
			MediaType JSON = MediaType.parse("application/json; charset=utf-8");
			RequestBody body = RequestBody.create(
					nameValuePairs != null ? nameValuePairs.toString() : "{}",
					JSON
			);

			Request request = new Request.Builder()
					.url(url)
					.post(body)
					.build();

			try (Response response = client.newCall(request).execute()) {
				if (response != null && response.body() != null) {
					responseFromServer = response.body().string();
				}
			}

		} catch (SocketTimeoutException e) {
			print_Log_d("HTTP_POST1_TIMEOUT", e.toString());
		} catch (Exception e) {
			print_Log_d("HTTP_POST1_EXCEPTION", e.toString());
		}

		return ApiRes(responseFromServer);
	}

	public static String getDataByHTTP_POST_Time(final Context context, final String url,
												 final ArrayList<NameValuePair> nameValuePairs) {

		print_Log_d("HTTP_POST_TIME_URL", url);

		if (!isConnectionPossible(context)) {
			return ApiRes("Network Failure");
		}

		String responseFromServer = "Network Failure";
		OkHttpClient client = getLongTimeoutClient();

		try {
			FormBody.Builder formBuilder = new FormBody.Builder();

			if (nameValuePairs != null) {
				for (NameValuePair pair : nameValuePairs) {
					formBuilder.add(
							pair.getName() == null ? "" : pair.getName(),
							pair.getValue() == null ? "" : pair.getValue()
					);
				}
			}

			RequestBody formBody = formBuilder.build();

			Request request = new Request.Builder()
					.url(url)
					.post(formBody)
					.build();

			try (Response response = client.newCall(request).execute()) {
				if (response != null && response.body() != null) {
					responseFromServer = response.body().string();
				}
			}

		} catch (SocketTimeoutException e) {
			print_Log_d("HTTP_POST_TIME_TIMEOUT", e.toString());
		} catch (Exception e) {
			print_Log_d("HTTP_POST_TIME_EXCEPTION", e.toString());
		}

		return ApiRes(responseFromServer);
	}
}
