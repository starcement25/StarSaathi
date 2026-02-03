package org.forcepower.starcement.bean;

/**
 * Created by amit on 09/01/2017.
 * invoice info data model
 */

public final class CashTransferReceive
{
    String status		="";

    public void setstatus(String status)
    {
        this.status=status;
    }
    public String getstatus()
    {
        return status;
    }
}
