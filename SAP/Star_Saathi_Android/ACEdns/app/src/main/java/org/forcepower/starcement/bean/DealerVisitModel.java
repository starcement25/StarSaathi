package org.forcepower.starcement.bean;

public class DealerVisitModel {
    private String emp_name;

    public String getEmp_name() {
        return emp_name;
    }

    public void setEmp_name(String emp_name) {
        this.emp_name = emp_name;
    }

    public String getVisit_datetime() {
        return visit_datetime;
    }

    public void setVisit_datetime(String visit_datetime) {
        this.visit_datetime = visit_datetime;
    }

    private String visit_datetime;

    public String getEmp_code() {
        return emp_code;
    }

    public void setEmp_code(String emp_code) {
        this.emp_code = emp_code;
    }

    private String emp_code;
}
