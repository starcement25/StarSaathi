package org.forcepower.starcement.parser;

import org.forcepower.starcement.bean.MenuDetails;
import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.DefaultHandler;
import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.util.ArrayList;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

public final class MenuDetailsXMLParsing extends DefaultHandler {

	ArrayList<MenuDetails> list = new ArrayList<MenuDetails>();
	private MenuDetails details;
	StringBuilder sb;

	boolean menuId;
	boolean userId;
	boolean attndnc;
	boolean routePlan;
	boolean order;
	boolean collection;
	boolean stkAudit;
	boolean businessPros;
	boolean tourExp;
	boolean captureImg;
	boolean notesInfo;
	boolean activityReport;
	boolean loyalty;
	boolean timeStamp;
	boolean misReport;
	boolean deleteTransaction;
	boolean loadingFreight;
	boolean saudaalloc;
	boolean isSurvey;
	boolean isPromotion;
	boolean isReplacement;
	boolean isMarketFeedback;
	boolean isSaudaBookedFromApp;
	boolean isPendingContract;
	boolean isSaudaMis;
	boolean isOrderStatus;
	boolean isCheckout;
	boolean isSaudaOutstanding;
	boolean isSalePerformance;
	boolean isCheckInOut;
	boolean isOutstanding;
	boolean isOutstandingAgeing;
	boolean isTargetAchevement;
	boolean isWholeSaleInfo;
	boolean isSelfAppraisalDetails;
	boolean isYellowCard;
	boolean isCatalogue;
	boolean isCatalogueUrl;
	boolean isTelephonicTransaction;
	boolean isTDAllocation;
	boolean catalogue_dependency;
	boolean TD_allocation_vertical;
	boolean run_time_TD_approval_vertical;
	boolean quotation;
	boolean CRM_app;
	boolean ISP;
	boolean monthly_report_mail;
	boolean retailer_app;

	public MenuDetails getParsedData() {
		return this.details;
	}

	public MenuDetailsXMLParsing(String datafromserver) {
		SAXParserFactory spf = SAXParserFactory.newInstance();
		SAXParser sp = null;
		XMLReader xr = null;
		try {
			sp = spf.newSAXParser();
		} catch (ParserConfigurationException e) {
			e.printStackTrace();
		} catch (SAXException e) {
			e.printStackTrace();
		}
		try {
			xr = sp.getXMLReader();

		} catch (SAXException e) {
			e.printStackTrace();
		}
		try {
			xr.setContentHandler(this);
			xr.parse(new InputSource(new ByteArrayInputStream(datafromserver.getBytes("ISO-8859-1"))));
		} catch (IOException e) {
			e.printStackTrace();
		} catch (SAXException e) {
			e.printStackTrace();
		}
	}

	@Override
	public void startDocument() throws SAXException {
		super.startDocument();
	}

	@Override
	public void endDocument() throws SAXException {
		super.endDocument();
	}

	public void startElement(String uri, String localName, String qName,
			Attributes attributes) throws SAXException {
		// print_log_d("tag start",localName);

		sb = new StringBuilder();

		super.startElement(uri, localName, qName, attributes);

		if (localName.equalsIgnoreCase("data")) {
			details = new MenuDetails();
		}

		if (localName.equalsIgnoreCase("menu_id")) {
			menuId = true;
		}
		if (localName.equalsIgnoreCase("user_id")) {
			userId = true;
		}
		if (localName.equalsIgnoreCase("attendance")) {
			attndnc = true;
		}
		if (localName.equalsIgnoreCase("route_plan")) {
			routePlan = true;
		}
		if (localName.equalsIgnoreCase("order")) {
			order = true;
		}
		if (localName.equalsIgnoreCase("collection")) {
			collection = true;
		}
		if (localName.equalsIgnoreCase("stk_audit")) {
			stkAudit = true;
		}
		if (localName.equalsIgnoreCase("business_prospect")) {
			businessPros = true;
		}
		if (localName.equalsIgnoreCase("tour_exp")) {
			tourExp = true;
		}
		if (localName.equalsIgnoreCase("capture_image")) {
			captureImg = true;
		}
		if (localName.equalsIgnoreCase("notes_and_info")) {
			notesInfo = true;
		}
		if (localName.equalsIgnoreCase("activity_report")) {
			activityReport = true;
		}
		if (localName.equalsIgnoreCase("loyalty")) {
			loyalty = true;
		}
		if (localName.equalsIgnoreCase("last_update_time")) {
			timeStamp = true;
		}
		if (localName.equalsIgnoreCase("mis_report")) {
			misReport = true;
		}
		if (localName.equalsIgnoreCase("delete_transaction")) {
			deleteTransaction = true;
		}
		if (localName.equalsIgnoreCase("loading_freight")) {
			loadingFreight = true;
		}
		if (localName.equalsIgnoreCase("sauda_allocation")) {
			saudaalloc = true;
		}
		if (localName.equalsIgnoreCase("survey")) {
			isSurvey = true;
		}
		if (localName.equalsIgnoreCase("product_promotion")) {
			isPromotion = true;
		}
		if (localName.equalsIgnoreCase("replacement")) {
			isReplacement = true;
		}
		if (localName.equalsIgnoreCase("market_feedback")) {
			isMarketFeedback=true;
		}
		if (localName.equalsIgnoreCase("sauda_allocation_app")) {
			isSaudaBookedFromApp=true;
		}
		if (localName.equalsIgnoreCase("pending_contract")) {
			isPendingContract=true;
		}
		if (localName.equalsIgnoreCase("sauda_mis")) {
			isSaudaMis=true;
		}
		if (localName.equalsIgnoreCase("order_status")) {
			isOrderStatus=true;
		}
		if (localName.equalsIgnoreCase("checkout")) {
			isCheckout=true;
		}
		if (localName.equalsIgnoreCase("sauda_outstanding")) {
			isSaudaOutstanding=true;
		}		
		if (localName.equalsIgnoreCase("sale_performance")) {
			isSalePerformance=true;
		}
		if (localName.equalsIgnoreCase("check_in_out")) {
			isCheckInOut=true;
		}
		if (localName.equalsIgnoreCase("outstanding")) {
			isOutstanding=true;
		}
		if (localName.equalsIgnoreCase("outstanding_ageing")) {
			isOutstandingAgeing=true;
		}
		if (localName.equalsIgnoreCase("target_achievement")) {
			isTargetAchevement=true;
		}
		if (localName.equalsIgnoreCase("wholesaler_info")) {
			isWholeSaleInfo=true;
		}
		if (localName.equalsIgnoreCase("self_appraisal")) {
			isSelfAppraisalDetails=true;
		}
		if (localName.equalsIgnoreCase("yellow_card")) {
			isYellowCard=true;
		}
		if (localName.equalsIgnoreCase("catalogue")) {
			isCatalogue=true;
		}
		if (localName.equalsIgnoreCase("catalogue_url")) {
			isCatalogueUrl=true;
		}
		if (localName.equalsIgnoreCase("tele_tran")) {
			isTelephonicTransaction =true;
		}
		if (localName.equalsIgnoreCase("TD_allocation_app")) {
			isTDAllocation =true;
		}
		if (localName.equalsIgnoreCase("catalogue_dependency")) {
			catalogue_dependency =true;
		}
		if (localName.equalsIgnoreCase("TD_allocation_vertical")) {
			TD_allocation_vertical =true;
		}
		if (localName.equalsIgnoreCase("run_time_TD_approval_vertical")) {
			run_time_TD_approval_vertical =true;
		}
		if (localName.equalsIgnoreCase("quotation")) {
			quotation =true;
		}
		if (localName.equalsIgnoreCase("CRM_app")) {
			CRM_app =true;
		}
		if (localName.equalsIgnoreCase("ISP")) {
			ISP =true;
		}
		if (localName.equalsIgnoreCase("monthly_report_mail")) {
			monthly_report_mail =true;
		}
		if (localName.equalsIgnoreCase("retailer_app")) {
			retailer_app =true;
		}
	}

	public void characters(char[] ch, int start, int length)
			throws SAXException {
		super.characters(ch, start, length);

		if (menuId) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}

		if (userId) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}

		if (attndnc) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}

		if (routePlan) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}

		if (order) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}

		if (collection) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}

		if (stkAudit) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}

		if (businessPros) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}

		if (tourExp) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}

		if (captureImg) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}

		if (notesInfo) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (activityReport) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (loyalty) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (timeStamp) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (misReport) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (deleteTransaction) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (loadingFreight) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (saudaalloc) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isSurvey) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}		
		if (isPromotion) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		
		if (isReplacement) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isMarketFeedback) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isSaudaBookedFromApp) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}		
		if (isPendingContract) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isSaudaMis) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isOrderStatus) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isCheckout) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isSaudaOutstanding) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isSalePerformance) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isCheckInOut) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isOutstanding) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}		
		if (isOutstandingAgeing) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}		
		if (isTargetAchevement) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isWholeSaleInfo) {
			if (details != null)
				for (int i = start; i < start + length; i++) {
					sb.append(ch[i]);
				}
		}
		if (isSelfAppraisalDetails)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (isYellowCard)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (isCatalogue)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (isCatalogueUrl)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (isTelephonicTransaction)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (isTDAllocation)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (catalogue_dependency)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (TD_allocation_vertical)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (run_time_TD_approval_vertical)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (quotation)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (CRM_app)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}

		if (ISP)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (monthly_report_mail)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}
		if (retailer_app)
		{
			if (details != null)
				for (int i = start; i < start + length; i++)
				{
					sb.append(ch[i]);
				}
		}

	}

	public void endElement(String uri, String localName, String qName)
			throws SAXException {
		// print_log_d("tag end",localName);
		super.endElement(uri, localName, qName);

		if (menuId) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setMenuId(trueData);
			menuId = false;
		}

		if (userId) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setUserid(trueData);
			userId = false;
		}

		if (attndnc) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setAttendance(trueData);
			attndnc = false;
		}

		if (routePlan) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setRoutePlan(trueData);
			routePlan = false;
		}

		if (order) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setOrder(trueData);
			order = false;
		}

		if (collection) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setCollection(trueData);
			collection = false;
		}

		if (stkAudit) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setStkAudit(trueData);
			stkAudit = false;
		}

		if (businessPros) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setBusinessProspect(trueData);
			businessPros = false;
		}

		if (tourExp) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setTourExp(trueData);
			tourExp = false;
		}

		if (captureImg) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setCaptureImage(trueData);
			captureImg = false;
		}

		if (notesInfo) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setNotesInfo(trueData);
			notesInfo = false;
		}
		if (activityReport) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setActivityReport(trueData);
			activityReport = false;
		}
		if (loyalty) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setLoyalty(trueData);
			loyalty = false;
		}
		if (timeStamp) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setLastUpdateTime(trueData);
			timeStamp = false;
		}
		if (misReport) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setMisReport(trueData);
			misReport = false;
		}
		if (deleteTransaction) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setDeleteTransaction(trueData);
			deleteTransaction = false;
		}
		if (loadingFreight) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setLoadingFreight(trueData);
			loadingFreight = false;
		}
		if (saudaalloc) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setSaudaAllocation(trueData);
			saudaalloc = false;
		}
		
		if (isSurvey) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setSurvey(trueData);
			isSurvey = false;
		}
		
		if (isPromotion) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setSampling(trueData);
			isPromotion = false;
		}
		
		if (isReplacement) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setReplacement(trueData);
			isReplacement = false;
		}
		if (isMarketFeedback) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setMarketFeedback(trueData);
			isMarketFeedback = false;
		}
		if (isSaudaBookedFromApp) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setSaudaAllocationfromApp(trueData);
			isSaudaBookedFromApp = false;
		}
		if (isPendingContract) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setPendingContract(trueData);
			isPendingContract = false;
		}
		if (isSaudaMis) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setSaudaMis(trueData);
			isSaudaMis = false;
		}
		
		if (isOrderStatus) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setOrderStatus(trueData);
			isOrderStatus = false;
		}
		if (isCheckout) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setCheckOut(trueData);
			isCheckout = false;
		}
		if (isSaudaOutstanding) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setSaudaOutstanding(trueData);
			isSaudaOutstanding = false;
		}
		if (isSalePerformance) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setSalePerFormance(trueData);
			isSalePerformance = false;
		}
		
		if (isCheckInOut) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setCheckInOut(trueData);
			isCheckInOut = false;
		}
		
		if (isOutstanding) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setOutstanding(trueData);
			isOutstanding = false;
		}
		
		if (isOutstandingAgeing) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setOutstandingAgeing(trueData);
			isOutstandingAgeing = false;
		}
		
		if (isTargetAchevement) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setTargetAcheivement(trueData);
			isTargetAchevement = false;
		}
		
		if (isWholeSaleInfo) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setWholeSaleInfo(trueData);
			isWholeSaleInfo = false;
		}
		if (isSelfAppraisalDetails) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setSelfAppraisalDetails(trueData);
			isSelfAppraisalDetails = false;
		}
		if (isYellowCard) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setYellowCard(trueData);
			isYellowCard = false;
		}
		if (isCatalogue) {
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setCatalogue(trueData);
			isCatalogue = false;
		}
		if (isCatalogueUrl)
		{
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setCatalogueUrl(trueData);
			isCatalogueUrl = false;
		}
		if (isTelephonicTransaction)
		{
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setTelephonicTransaction(trueData);
			isTelephonicTransaction = false;
		}
		if (isTDAllocation)
		{
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setTDAllocation(trueData);
			isTDAllocation = false;
		}
		if (catalogue_dependency)
		{
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setcatalogue_dependency(trueData);
			catalogue_dependency = false;
		}
		if (TD_allocation_vertical)
		{
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setTD_allocation_vertical(trueData);
			TD_allocation_vertical = false;
		}
		if (run_time_TD_approval_vertical)
		{
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setrun_time_TD_approval_vertical(trueData);
			run_time_TD_approval_vertical = false;
		}
		if (quotation)
		{
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setquotation(trueData);
			quotation = false;
		}
		if (CRM_app)
		{
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setCRM_app(trueData);
			CRM_app = false;
		}
		if (ISP)
		{
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setISP(trueData);
			ISP = false;
		}
		if (monthly_report_mail)
		{
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setmonthly_report_mail(trueData);
			monthly_report_mail = false;
		}

		if (retailer_app)
		{
			String finalString = sb.toString();
			String trueData = getTrueData(finalString);
			details.setretailer_app(trueData);
			retailer_app = false;
		}

		if (localName.equalsIgnoreCase("data")) {
			list.add(details);
		}
	}

	public String getTrueData(String data) {
		// String[] dataArray = data.split("[");
		// String[] dataArray1 = dataArray[2].split("]");
		// return dataArray1[0];
		return data;
	}

}