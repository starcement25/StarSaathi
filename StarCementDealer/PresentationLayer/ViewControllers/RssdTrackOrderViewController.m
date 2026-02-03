//
//  RssdTrackOrderViewController.m
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 20/02/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "RssdTrackOrderViewController.h"
#import "TrackOrderCell.h"
#import "FMDB.h"
#import "OrderDataBO.h"
#import "OrderChallanBO.h"
#import "MaterialReceivedViewController.h"

#define Section_Heder_Height 130

@interface RssdTrackOrderViewController ()<UITableViewDelegate, UITableViewDataSource>{
    NSArray *arrSegment;
    __weak IBOutlet UIButton *btnAppOrder;
    __weak IBOutlet UIButton *btnOfflineOrder;
    __weak IBOutlet UITableView *tblViewOrders;
    __weak IBOutlet UIDatePicker *datePickerTrackOrder;
    __weak IBOutlet UIToolbar *toolbarDone;
    __weak IBOutlet UILabel *lblStartDate;
    __weak IBOutlet UILabel *lblEndDate;
    __weak IBOutlet UIButton *btnStartDate;
    __weak IBOutlet UIButton *btnEndDate;
    
    FMDatabase *db;
    NSArray *arrOrderData;
    NSArray *arrOrderChallanData;
    NSString *strOrderType;
    
    NSString *strFromDate;
    NSString *strToDate;
    int integerBtnDateIdentifier;
    NSUserDefaults *defaults;
    NSInteger intSelectedIdx;
}

@end

@implementation RssdTrackOrderViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [self btnSegmentClicked:btnAppOrder];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView{
    defaults = [NSUserDefaults standardUserDefaults];
    arrSegment = @[btnAppOrder, btnOfflineOrder];
    [tblViewOrders registerNib:[UINib nibWithNibName:@"TrackOrderCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewOrders.tableFooterView = [UIView new];
    tblViewOrders.separatorColor = [UIColor clearColor];
    
    datePickerTrackOrder.maximumDate = [NSDate date];
    [datePickerTrackOrder setBackgroundColor:[UIColor whiteColor]];
    
    datePickerTrackOrder.hidden = true;
    toolbarDone.hidden = true;
}

-(void)loadData{
    
    intSelectedIdx = -1;
    
    NSDate *dateFrom = [[NSCalendar currentCalendar] dateByAddingUnit:NSCalendarUnitDay value:-30 toDate:[NSDate date] options:0];
    NSDate *dateTo = [NSDate date];
    
    [APP_CONSTANTS.dateFormater setDateFormat:@"MMM dd, yyyy"];
    
    lblStartDate.text = [APP_CONSTANTS.dateFormater stringFromDate:dateFrom];
    lblEndDate.text = [APP_CONSTANTS.dateFormater stringFromDate:dateTo];
    
    [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
    
    strFromDate = [APP_CONSTANTS.dateFormater stringFromDate:dateFrom];
    strToDate = [APP_CONSTANTS.dateFormater stringFromDate:dateTo];
}

#pragma mark - UITableView Delegate and Datasource

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView{
    return arrOrderData.count;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    NSString *strKey = ([strOrderType isEqualToString:@"APP ORDER"]) ? @"subdealer_challan_data" : @"order_challan_data";
    NSArray *arrChallanData = [[arrOrderData objectAtIndex:section] safeValueeForKey:strKey];
    return (section == intSelectedIdx) ? [arrChallanData count] : 0;
}

- (TrackOrderCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *cellIdentifier = @"cell";
    TrackOrderCell *cell = [tblViewOrders dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    NSString *strKey = ([strOrderType isEqualToString:@"APP ORDER"]) ? @"subdealer_challan_data" : @"order_challan_data";
    NSDictionary *dictChallanData = [[[arrOrderData objectAtIndex:indexPath.section] safeValueeForKey:strKey] objectAtIndex:indexPath.row];
    
    cell.lblChallanNo.text = [dictChallanData safeValueeForKey:@"challanno"];
    cell.lblChallanDate.text = [dictChallanData safeValueeForKey:@"challandt"];
    cell.lblChallanQty.text = [dictChallanData safeValueeForKey:@"challanqty"];
    cell.lblTruckNo.text = [dictChallanData safeValueeForKey:@"truckno"];
    cell.lblDriverContact.text = [dictChallanData safeValueeForKey:@"driverno"];
    cell.lblTransporterName.text = [dictChallanData safeValueeForKey:@"transporter_name"];
    cell.lblCHStatus.text = [dictChallanData safeValueeForKey:@"ch_status"];
    
    [cell.btnFeedback addTarget:self action:@selector(btnFeedbackClicked:) forControlEvents:UIControlEventTouchUpInside];
    cell.btnFeedback.accessibilityElements = @[[dictChallanData safeValueeForKey:@"apporderno"], [dictChallanData safeValueeForKey:@"ch_uid"], [dictChallanData safeValueeForKey:@"challanno"]];
    
    cell.btnFeedback.accessibilityHint = [dictChallanData safeValueeForKey:@"is_confirmed_challan_material_received"];
    //[cell.btnFeedback setTitleColor:[self colorFromHexString:OCBO.strColourCode] forState:UIControlStateNormal];
    
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    
}

- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section{
    return Section_Heder_Height;
}

- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section{
    
    NSDictionary *dictOrder = arrOrderData[section];
    
    UIView *viewHeader = [[UIView alloc] initWithFrame:CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, Section_Heder_Height)];
    viewHeader.backgroundColor = UIColorFromRGB(0xf2f6f8);
    
    UIImageView *imgViewBubble = UDcreateImageView(CGRectMake(5, 0, APP_CONSTANTS.fltAppWidth - 10, Section_Heder_Height), nil);
    imgViewBubble.image = [UIImage imageNamed:@"section_header"];
    
    //NSString *strOrderNo = ([strOrderType isEqualToString:@"APP ORDER"]) ? ODB.strApporderno : ODB.strErporderno;
    NSString *strOrderNo = ([strOrderType isEqualToString:@"APP ORDER"]) ? [dictOrder safeValueeForKey:@"order_id"] : [dictOrder safeValueeForKey:@"erporderno"];
    UILabel *lblOrderNo = UDcreateLabel(CGRectMake(12, 10, 140, 15), strOrderNo, [UIFont systemFontOfSize:12 weight:UIFontWeightSemibold]);
    lblOrderNo.textColor = UIColorFromRGB(0x374147);
    
    NSString *strQuantity = [NSString stringWithFormat:@"X %@",[dictOrder safeValueeForKey:@"qty"]];
    UILabel *lblQuantity = UDcreateLabel(CGRectMake(157, 10.5, 55, 14), strQuantity, [UIFont systemFontOfSize:11 weight:UIFontWeightRegular]);
    lblQuantity.textAlignment = NSTextAlignmentCenter;
    lblQuantity.textColor = UIColorFromRGB(0x434c51);
    
    UILabel *lblStatus = UDcreateLabel(CGRectMake(APP_CONSTANTS.fltAppWidth - 112, 10, 100, 15), [dictOrder safeValueeForKey:@"status"], [UIFont systemFontOfSize:12 weight:UIFontWeightSemibold]);
    lblStatus.textAlignment = NSTextAlignmentRight;
    lblStatus.textColor = [self getColorWithText:[dictOrder safeValueeForKey:@"status"]];
    
    UILabel *lblProdName = UDcreateLabel(CGRectMake(12, 31, APP_CONSTANTS.fltAppWidth - 24, 15), [dictOrder safeValueeForKey:@"prod_display_name"], [UIFont systemFontOfSize:12 weight:UIFontWeightRegular]);
    lblOrderNo.textColor = UIColorFromRGB(0x475055);
    
    NSString *strOrderDate = ([strOrderType isEqualToString:@"APP ORDER"]) ? [dictOrder safeValueeForKey:@"order_date"] : [dictOrder safeValueeForKey:@"erporderdt"];
    UILabel *lblOrderDate = UDcreateLabel(CGRectMake(12, 51, APP_CONSTANTS.fltAppWidth - 24, 15), strOrderDate, [UIFont systemFontOfSize:12 weight:UIFontWeightRegular]);
    lblOrderDate.textColor = UIColorFromRGB(0x475055);
    
    NSString *strDestAdd = ([strOrderType isEqualToString:@"APP ORDER"]) ? [dictOrder safeValueeForKey:@"destination_address"] : [dictOrder safeValueeForKey:@"destination_name"];
    UILabel *lblDestAdd = UDcreateLabel(CGRectMake(12, 71, APP_CONSTANTS.fltAppWidth - 24, 20), strDestAdd, [UIFont systemFontOfSize:11 weight:UIFontWeightRegular]);
    lblDestAdd.textColor = [UIColor darkGrayColor];
    
    UILabel *lblFright = UDcreateLabel(CGRectMake(12, 96, APP_CONSTANTS.fltAppWidth - 24, 20), [dictOrder safeValueeForKey:@"freight"], [UIFont systemFontOfSize:11 weight:UIFontWeightRegular]);
    lblFright.textColor = [UIColor darkGrayColor];
    
    [viewHeader addSubview:imgViewBubble];
    [viewHeader addSubview:lblOrderNo];
    [viewHeader addSubview:lblQuantity];
    [viewHeader addSubview:lblStatus];
    [viewHeader addSubview:lblProdName];
    [viewHeader addSubview:lblOrderDate];
    [viewHeader addSubview:lblDestAdd];
    [viewHeader addSubview:lblFright];
    
    UITapGestureRecognizer *tapGesture = [[UITapGestureRecognizer alloc]initWithTarget:self action:@selector(tblSectionClicked:)];
    tapGesture.accessibilityHint = [NSString stringWithFormat:@"%ld",(long)section];
    [viewHeader addGestureRecognizer:tapGesture];
    return viewHeader;
}

#pragma mark - Cell Action

-(void)btnFeedbackClicked:(UIButton*)btnFeedback {
    AppLog(@"-->>%@",btnFeedback.accessibilityValue);
    if ([btnFeedback.accessibilityHint isEqualToString:@"NO"]) {
        [self performSegueWithIdentifier:@"trackOrderRssdToMaterialReceived" sender:btnFeedback];
    }else{
        UDShowToastAlertWithTitle(@"Feedback already submitted.", 2);
    }
}

#pragma mark - Gesture Action

-(void)tblSectionClicked:(UITapGestureRecognizer*)tapGesture{
    
    int index = [tapGesture.accessibilityHint intValue];
    intSelectedIdx = (intSelectedIdx >= 0) ? -1 : index;
    [tblViewOrders reloadData];
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"trackOrderRssdToMaterialReceived"]) {
        MaterialReceivedViewController *mrvc = segue.destinationViewController;
        UIButton *btnFeedback = (UIButton*)sender;
        mrvc.arrChallanDetails = btnFeedback.accessibilityElements;
    }
}

#pragma mark - IBAction's

- (IBAction)btnSegmentClicked:(UIButton *)sender {
    
    for (UIButton *btnSegment in arrSegment) {
        [btnSegment setSelected:NO];
    }
    [sender setSelected:YES];
    strOrderType = (sender.tag == 101) ? @"APP ORDER" : @"OFFLINE ORDER";
    [self getTrackOrder:sender.tag];
}

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)btnStartDateClicked:(UIButton *)sender {
    AppLog(@"-->>");
    integerBtnDateIdentifier = (int)sender.tag;
    
    btnStartDate.userInteractionEnabled = false;
    btnEndDate.userInteractionEnabled = false;
    
    datePickerTrackOrder.hidden = false;
    toolbarDone.hidden = false;
}

- (IBAction)btnEndDateClicked:(UIButton *)sender {
    AppLog(@"-->>");
    integerBtnDateIdentifier = (int)sender.tag;
    
    btnStartDate.userInteractionEnabled = false;
    btnEndDate.userInteractionEnabled = false;
    
    datePickerTrackOrder.hidden = false;
    toolbarDone.hidden = false;
}

- (IBAction)btnDoneClicked:(UIBarButtonItem *)sender {
    
    btnStartDate.userInteractionEnabled = true;
    btnEndDate.userInteractionEnabled = true;
    
    datePickerTrackOrder.hidden = true;
    toolbarDone.hidden = true;
    //[APP_CONSTANTS.dateFormater setDateFormat:@"MMM dd, yyyy"];
    
    if (integerBtnDateIdentifier == 101) {
        [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
        strFromDate = [APP_CONSTANTS.dateFormater stringFromDate:datePickerTrackOrder.date];
        [APP_CONSTANTS.dateFormater setDateFormat:@"MMM dd, yyyy"];
        lblStartDate.text = [APP_CONSTANTS.dateFormater stringFromDate:datePickerTrackOrder.date];
    }else{
        [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
        strToDate = [APP_CONSTANTS.dateFormater stringFromDate:datePickerTrackOrder.date];
        [APP_CONSTANTS.dateFormater setDateFormat:@"MMM dd, yyyy"];
        lblEndDate.text = [APP_CONSTANTS.dateFormater stringFromDate:datePickerTrackOrder.date];
    }
    
    if (![strFromDate isEqualToString:@""] && ![strToDate isEqualToString:@""]) {
        AppLog(@"Start and End Date selected");
        [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
        NSDate *dateStart = [APP_CONSTANTS.dateFormater dateFromString:strFromDate];
        NSDate *dateEnd = [APP_CONSTANTS.dateFormater dateFromString:strToDate];
        
        NSComparisonResult result;
        //has three possible values: NSOrderedSame,NSOrderedDescending, NSOrderedAscending
        
        result = [dateStart compare:dateEnd]; // comparing two dates
        
        if(result==NSOrderedAscending){
            AppLog(@"start date is less");
        }else if(result==NSOrderedDescending){
            AppLog(@"end date is less");
            UDShowToastAlertWithTitle(@"End date can not be less than start date", 2);
            return;
        }else{
            AppLog(@"Both dates are same");
        }
        
        //[arrOrderData removeAllObjects];
        [self dateRangeStartDate:[APP_CONSTANTS.dateFormater stringFromDate:dateStart] EndDate:[APP_CONSTANTS.dateFormater stringFromDate:dateEnd]];
        
        
    }
    
}

- (void)dateRangeStartDate:(NSString *)strStartDate EndDate:(NSString*)strEndDate {
    AppLog(@"Start Date-->>%@",strStartDate);
    AppLog(@"End Date-->>%@",strEndDate);
    strFromDate = strStartDate;
    strToDate = strEndDate;
    int tag = ([strOrderType isEqualToString:@"APP ORDER"]) ? 101 : 102;
    [self getTrackOrder:tag];
}

#pragma mark - Web Service Method

-(void)getTrackOrder:(NSInteger)tag{
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
        [dict setValue:[defaults valueForKey:@"user_type"] forKey:@"user_type"];
        [dict setValue:strFromDate forKey:@"start_date"];
        [dict setValue:strToDate forKey:@"end_date"];
        
        NSString *strUrl = ([strOrderType isEqualToString:@"APP ORDER"]) ? @"https://starsaathi.com/SAP/acedns_show_order_list_for_subdealer_V1.php" : @"https://starsaathi.com/SAP/acedns_show_offline_order_list.php";
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:strUrl parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    if ([strOrderType isEqualToString:@"APP ORDER"]) {
                        arrOrderData = [dictResponse safeValueeForKey:@"subdealer_order_data"];
                    }else{
                        arrOrderData = [dictResponse safeValueeForKey:@"order_data"];
                    }
                    [tblViewOrders reloadData];
                }else{
                    arrOrderData = @[];
                    [tblViewOrders reloadData];
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                }
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - Helper Method

-(UIColor*)getColorWithText:(NSString*)strStatus {
    
    //    All order status:
    //    Order received
    //    DO approved
    //    Dispatched
    //    Order authorized
    //    Order canceled
    
    if ([strStatus caseInsensitiveCompare:@"Order received"] == NSOrderedSame) {
        return [UIColor grayColor];
    }else if ([strStatus caseInsensitiveCompare:@"DO approved"] == NSOrderedSame){
        return UIColorFromRGB(0xedbe00);//yellow
    }else if ([strStatus caseInsensitiveCompare:@"Dispatched"] == NSOrderedSame){
        return UIColorFromRGB(0x3a8a00);//green
    }else if ([strStatus caseInsensitiveCompare:@"Order authorized"] == NSOrderedSame){
        return [UIColor blueColor];
    }else if ([strStatus caseInsensitiveCompare:@"Order canceled"] == NSOrderedSame){
        return UIColorFromRGB(0xFF0000);
    }else{
        return [UIColor blackColor];
    }
}

- (UIColor *)colorFromHexString:(NSString *)hexString {
    unsigned rgbValue = 0;
    NSScanner *scanner = [NSScanner scannerWithString:hexString];
    [scanner setScanLocation:1]; // bypass '#' character
    [scanner scanHexInt:&rgbValue];
    return [UIColor colorWithRed:((rgbValue & 0xFF0000) >> 16)/255.0 green:((rgbValue & 0xFF00) >> 8)/255.0 blue:(rgbValue & 0xFF)/255.0 alpha:1.0];
}


@end
