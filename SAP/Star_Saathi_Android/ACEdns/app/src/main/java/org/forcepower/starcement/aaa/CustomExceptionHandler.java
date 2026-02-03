package org.forcepower.starcement.aaa;

import android.content.Context;
import android.os.Build;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.io.Writer;
import java.lang.Thread.UncaughtExceptionHandler;
import java.text.DateFormat;
import java.util.Date;

import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;

/**
 * A class which will use for unchecked exception
 * @author Sourav Das <souravd@coral.in>
 * @version 5.2.8.4
 */

public final class CustomExceptionHandler implements UncaughtExceptionHandler {

    private UncaughtExceptionHandler defaultUEH;
    Context mContext;
    public CustomExceptionHandler(Context mContext) {
        this.mContext = mContext;
        this.defaultUEH = Thread.getDefaultUncaughtExceptionHandler();
    }

    /*
     * (non-Javadoc)
     * @see java.lang.Thread.UncaughtExceptionHandler#uncaughtException(java.lang.Thread, java.lang.Throwable)
     */
    
    public void uncaughtException(Thread t, Throwable e) {
        final Writer result = new StringWriter();
        final PrintWriter printWriter = new PrintWriter(result);
        e.printStackTrace(printWriter);
        /*
         * This variable store the error track record
         */
        String stacktrace = result.toString();
        printWriter.close();
        
        DateFormat[] formats = new DateFormat[] 
         {        		  
        	   DateFormat.getDateTimeInstance(),        		   
         };
        String time = "";
    	 for (DateFormat df : formats) 
    	 {
    		   time += df.format(new Date(System.currentTimeMillis()));
    	 }
        	 
        stacktrace += " \nCrash Happen: At :"+time;
        stacktrace += "\n";
        stacktrace += "Manufacturer : " + Build.MANUFACTURER;
        stacktrace += "\n";
        stacktrace += "Device Model : " + Build.MODEL;
        stacktrace += "\n";
        stacktrace += "Operating System of device : " + Build.VERSION.RELEASE;
        stacktrace += "\n";
        stacktrace += "..................................................................................";
        stacktrace += "\n\n";
    	try {
			writeFile(stacktrace);
		} catch (IOException e1) {				
			e1.printStackTrace();
		}       
        defaultUEH.uncaughtException(t, e);
    }
    /**
     * This method is used to write error message in text file
     * @param data
     *            Which will be print in the text file.     
     * @throws IOException 
     */
    private void writeFile(String data) throws IOException{
    	File myFile = new File(get_direcory_path(mContext) + "StarSaathiCrashLog.txt");
    	if(!myFile.exists())
        myFile.createNewFile();
    	
        FileOutputStream fOut = new FileOutputStream(myFile,true);
        OutputStreamWriter myOutWriter = new OutputStreamWriter(fOut);
        myOutWriter.append(data);
        myOutWriter.close();
        fOut.close();
    }
}
