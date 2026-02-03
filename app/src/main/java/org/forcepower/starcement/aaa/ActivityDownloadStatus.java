package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.constants.Constants.redirection;

import android.app.Activity;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;

import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.Utils;
import org.forcepower.starcement.util.commonAsyncTaskMaster;

public final class ActivityDownloadStatus
{
    private AceDnsDatabase dbHelper;
	private Handler mPrepareSurveyHandler;
	private int mCount=0;
	private String mDownLoadMenuname="";
    private Activity mContext;

    public ActivityDownloadStatus(Activity mContext_)
    {
		this.mContext = mContext_;

        dbHelper = new AceDnsDatabase(mContext);
        mCount=0;

        AceDnsDatabase setupDataHelperObj = new AceDnsDatabase(mContext);
        setupDataHelperObj.deleteNonIncrementalData();
        setupDataHelperObj.closeDatabase();
        Constants.isDownLoadComplete=true;

        mPrepareSurveyHandler = new Handler(Looper.myLooper()) {
            public void handleMessage(Message threadmsg) {
                //mPrepareSurveyProgressDialog.dismiss();
                final int listcount = threadmsg.getData().getInt("JOBALLOCATE");
                mContext.runOnUiThread(new Runnable() {
                    public void run() {
                        if(listcount==Constants.downloadTableList.size()-1)
                        {

                            if(redirection.equalsIgnoreCase("MenuActivity"))
                            {
                                ((MenuActivity)mContext).load_on_create("ssyynncc");
                            }
                        }
                        else
                        {
                            mCount+=1;
                            mDownLoadMenuname=Constants.downloadTableList.get(mCount).toString();
                            DownloadData(mCount,mDownLoadMenuname);
                        }
                    }
                });
            }
        };
        //mPrepareSurveyProgressDialog = new ProgressDialog(mContext);
        mDownLoadMenuname=Constants.downloadTableList.get(mCount).toString();
        DownloadData(mCount,mDownLoadMenuname);
    }

	public void DownloadData(final int task, final String params)
	{
		SeTLoaderText(params,1);

        new Downloading(mContext, params, task).execute();

	}


    public final class Downloading extends AsyncTaskCoroutine<String, String>
    {
        private Activity mContext;
        private String params;
        private int task;

        public Downloading(final Activity context, final String params, final int task) {
            this.mContext = context;
            this.params = params;
            this.task = task;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            Utils.showProgressDialog(mContext, "Updating...");
        }

        @Override
        public String doInBackground(String... par)
        {
            try
            {
                new commonAsyncTaskMaster(mContext,params);
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
                new Thread()
                {
                    public void run()
                    {

                        Message msg = mPrepareSurveyHandler.obtainMessage();
                        Bundle bundle = new Bundle();
                        bundle.putInt("JOBALLOCATE", task);
                        msg.setData(bundle);
                        mPrepareSurveyHandler.sendMessage(msg);
                    }
                }.start();
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

	public String SeTLoaderText(String params,int show) {
		return " ";
	}
}