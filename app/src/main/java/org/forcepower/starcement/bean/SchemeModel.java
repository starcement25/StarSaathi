package org.forcepower.starcement.bean;

public class SchemeModel {

    private String schemeId;
    private String schemeName;
    private String category;
    private String status;
    private String pdfUrl;
    private String startDate;
    private String endDate;
    private String liftingStart;
    private String liftingEnd;
    private int daysRemaining;

    // ✅ New fields for graph
    private String slabType;          // "single" / "multiple"
    private int applicableQty;        // for single
    private String slabDetailsJson;   // full slab_details JSON string
    private String achievementsJson;  // achievements[] JSON string

    public SchemeModel() { }

    public SchemeModel(String schemeId,
                       String schemeName,
                       String category,
                       String status,
                       String pdfUrl,
                       String startDate,
                       String endDate,
                       String liftingStart,
                       String liftingEnd,
                       int daysRemaining,
                       String slabType,
                       int applicableQty,
                       String slabDetailsJson,
                       String achievementsJson) {

        this.schemeId = schemeId;
        this.schemeName = schemeName;
        this.category = category;
        this.status = status;
        this.pdfUrl = pdfUrl;
        this.startDate = startDate;
        this.endDate = endDate;
        this.liftingStart = liftingStart;
        this.liftingEnd = liftingEnd;
        this.daysRemaining = daysRemaining;

        this.slabType = slabType;
        this.applicableQty = applicableQty;
        this.slabDetailsJson = slabDetailsJson;
        this.achievementsJson = achievementsJson;
    }

    public String getSchemeId() { return schemeId; }
    public void setSchemeId(String schemeId) { this.schemeId = schemeId; }

    public String getSchemeName() { return schemeName; }
    public void setSchemeName(String schemeName) { this.schemeName = schemeName; }

    public String getCategory() { return category; }
    public void setCategory(String category) { this.category = category; }

    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }

    public String getPdfUrl() { return pdfUrl; }
    public void setPdfUrl(String pdfUrl) { this.pdfUrl = pdfUrl; }

    public String getStartDate() { return startDate; }
    public void setStartDate(String startDate) { this.startDate = startDate; }

    public String getEndDate() { return endDate; }
    public void setEndDate(String endDate) { this.endDate = endDate; }

    public String getLiftingStart() { return liftingStart; }
    public void setLiftingStart(String liftingStart) { this.liftingStart = liftingStart; }

    public String getLiftingEnd() { return liftingEnd; }
    public void setLiftingEnd(String liftingEnd) { this.liftingEnd = liftingEnd; }

    public int getDaysRemaining() { return daysRemaining; }
    public void setDaysRemaining(int daysRemaining) { this.daysRemaining = daysRemaining; }

    // ✅ New getters/setters
    public String getSlabType() { return slabType; }
    public void setSlabType(String slabType) { this.slabType = slabType; }

    public int getApplicableQty() { return applicableQty; }
    public void setApplicableQty(int applicableQty) { this.applicableQty = applicableQty; }

    public String getSlabDetailsJson() { return slabDetailsJson; }
    public void setSlabDetailsJson(String slabDetailsJson) { this.slabDetailsJson = slabDetailsJson; }

    public String getAchievementsJson() { return achievementsJson; }
    public void setAchievementsJson(String achievementsJson) { this.achievementsJson = achievementsJson; }
}
