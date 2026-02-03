package org.forcepower.starcement.bean;

public final class DumpMaster
{
    String branch_code = ""; String dump_code = "";
    String dump_name = ""; String acedns = "";
    String is_plant = ""; String download_time;

    public void set_branch_code(String rowDatum)
    {
        this.branch_code = rowDatum;
    }
    public void set_dump_code(String rowDatum)
    {
        this.dump_code = rowDatum;
    }
    public void set_dump_name(String rowDatum)
    {
        this.dump_name = rowDatum;
    }
    public void set_acedns(String rowDatum)
    {
        this.acedns = rowDatum;
    }
    public void set_is_plant(String rowDatum)
    {
        this.is_plant = rowDatum;
    }
    public void set_download_time(String rowDatum)
    {
        this.download_time = rowDatum;
    }

    public String get_branch_code()
    {
        return branch_code;
    }
    public String get_dump_code()
    {
        return dump_code;
    }
    public String get_dump_name()
    {
        return dump_name;
    }
    public String get_acedns()
    {
        return acedns;
    }
    public String get_is_plant()
    {
        return is_plant;
    }
    public String get_download_time()
    {
        return download_time;
    }

}
