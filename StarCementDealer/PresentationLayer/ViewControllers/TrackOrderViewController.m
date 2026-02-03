//
//  TrackOrderViewController.m
//  StarCementDealer
//
//  Created by Coral  on 17/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "TrackOrderViewController.h"
#import "TrackOrderCell.h"
#import "TrackOrderInvoiceCell.h"
#import "FMDB.h"
#import "OrderDataBO.h"
#import "OrderInvoiceBO.h"
#import "MaterialReceivedViewController.h"

#define Section_Heder_Height 155

@interface TrackOrderViewController ()<UITableViewDelegate, UITableViewDataSource>{
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
    //NSDictionary *dictTrackOrder;
    FMDatabase *db;
    NSMutableArray *arrOrderData;
    NSArray *arrOrderDataOffline;
    //NSMutableArray *arrOrderChallanData;
    NSMutableArray *arrOrderInvoiceData;
    NSString *strOrderType;
    
    NSString *strFromDate;
    NSString *strToDate;
    int integerBtnDateIdentifier;
    NSInteger intSelectedIdx;
}

@end

@implementation TrackOrderViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    db = [FMDatabase databaseWithPath:path];
    
    arrOrderData = [[NSMutableArray alloc]init];
    //arrOrderChallanData = [[NSMutableArray alloc]init];
    arrOrderInvoiceData = [[NSMutableArray alloc] init];
    [self btnSegmentClicked:btnAppOrder];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView{
    
    if (@available(iOS 15, *)) {
        tblViewOrders.sectionHeaderTopPadding = 0;
    }
    
    arrSegment = @[btnAppOrder, btnOfflineOrder];
    [tblViewOrders registerNib:[UINib nibWithNibName:@"TrackOrderCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    [tblViewOrders registerNib:[UINib nibWithNibName:@"TrackOrderInvoiceCell" bundle:nil] forCellReuseIdentifier:@"cell"];
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
    if ([strOrderType isEqualToString:@"APP ORDER"]) {
        return arrOrderData.count;
    }else{
        return arrOrderDataOffline.count;
    }
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    if ([strOrderType isEqualToString:@"APP ORDER"]) {
        //return [[arrOrderChallanData objectAtIndex:section] count];
        return [[arrOrderInvoiceData objectAtIndex:section] count];
    }else{
        NSArray *arrChallanData = [[arrOrderDataOffline objectAtIndex:section] safeValueeForKey:@"order_challan_data"];
        return (section == intSelectedIdx) ? [arrChallanData count] : 0;
    }}

- (void)callDriverTapped:(UITapGestureRecognizer *)gesture {
    NSString *phoneNumber = gesture.view.accessibilityLabel;
    if (phoneNumber.length > 0) {
        NSString *dialString = [NSString stringWithFormat:@"tel://%@", phoneNumber];
        NSURL *url = [NSURL URLWithString:dialString];
        if ([[UIApplication sharedApplication] canOpenURL:url]) {
            [[UIApplication sharedApplication] openURL:url];
        }
    }
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    
    static NSString *cellIdentifier = @"cell";
    //TrackOrderCell *cell = [tblViewOrders dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    
    if ([strOrderType isEqualToString:@"APP ORDER"]) {
        
        TrackOrderInvoiceCell *cell = [tblViewOrders dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
        OrderInvoiceBO *OIBO = [[arrOrderInvoiceData objectAtIndex:indexPath.section] objectAtIndex:indexPath.row];
        
        cell.lblInvoiceNo.text = OIBO.strInvoiceNo;
        // cell.lblInvoiceNo.text = @"Hello world";
        cell.lblInvoiceDate.text = OIBO.strInvoiceDate;
        cell.lblInvoiceQty.text = OIBO.strInvoiceQty;
        cell.lblTruckNo.text = OIBO.strTruckNo;
        cell.lblDriverNo.text = OIBO.strDriverNumber;
        cell.lblDriverNumber.text = @"Hello World 1";
        cell.lblDestination.text = OIBO.strDestination;
        cell.lblDriverNo.userInteractionEnabled = YES;
        UITapGestureRecognizer *tapCallDriver = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(callDriverTapped:)];
        [cell.lblDriverNo addGestureRecognizer:tapCallDriver];
        cell.lblDriverNo.accessibilityLabel = OIBO.strDriverNumber;
        return cell;
        
    }else{
        
        TrackOrderCell *cell = [tblViewOrders dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
        
        NSDictionary *dictChallanData = [[[arrOrderDataOffline objectAtIndex:indexPath.section] safeValueeForKey:@"order_challan_data"] objectAtIndex:indexPath.row];
        cell.lblChallanNo.text = [dictChallanData safeValueeForKey:@"challanno"];
        // cell.lblChallanNo.text = @"Hello world";
        cell.lblChallanDate.text = [dictChallanData safeValueeForKey:@"challandt"];
        cell.lblChallanQty.text = [dictChallanData safeValueeForKey:@"challanqty"];
        cell.lblTruckNo.text = [dictChallanData safeValueeForKey:@"truckno"];
        cell.lblDriverContact.text = [dictChallanData safeValueeForKey:@"driverno"];
        cell.lblTransporterName.text = [dictChallanData safeValueeForKey:@"transporter_name"];
        cell.lblCHStatus.text = [dictChallanData safeValueeForKey:@"ch_status"];

        /* 🔹 NEW ROW (AFTER FIRST LINE) */
        cell.lblErpOrderNo.text = [dictChallanData safeValueeForKey:@"erporderno"];
        
        [cell.btnFeedback addTarget:self action:@selector(btnFeedbackClicked:) forControlEvents:UIControlEventTouchUpInside];
        cell.btnFeedback.accessibilityElements = @[[dictChallanData safeValueeForKey:@"apporderno"], [dictChallanData safeValueeForKey:@"ch_uid"], [dictChallanData safeValueeForKey:@"challanno"]];
        
        cell.btnFeedback.accessibilityHint = [dictChallanData safeValueeForKey:@"is_confirmed_challan_material_received"];
        
        return cell;
    }
    
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath {
    if ([strOrderType isEqualToString:@"APP ORDER"]) {
        return 105.0f;
    }else{
        return 122.0f;
    }
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    
}

- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section{
    return Section_Heder_Height;
}

- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section{
    
    OrderDataBO *ODB;
    NSDictionary *dictOrder;
    if ([strOrderType isEqualToString:@"APP ORDER"]) {
        ODB = arrOrderData[section];
    }else{
        dictOrder = arrOrderDataOffline[section];
    }
    
    //int intYAxis = 10;
    
    UIView *viewHeader = [[UIView alloc] initWithFrame:CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, Section_Heder_Height)];
    viewHeader.backgroundColor = UIColorFromRGB(0xf2f6f8);
    
    UIImageView *imgViewBubble = UDcreateImageView(CGRectMake(5, 0, APP_CONSTANTS.fltAppWidth - 10, Section_Heder_Height), nil);
    imgViewBubble.image = [UIImage imageNamed:@"section_header"];
    
    NSString *strOrderNo = ([strOrderType isEqualToString:@"APP ORDER"]) ? ODB.strApporderno : [dictOrder safeValueeForKey:@"erporderno"];
    UILabel *lblOrderNo = UDcreateLabel(CGRectMake(12, 10, 140, 15), strOrderNo, [UIFont systemFontOfSize:12 weight:UIFontWeightSemibold]);
    lblOrderNo.textColor = UIColorFromRGB(0x374147);
    
    NSString *strQuantity = ([strOrderType isEqualToString:@"APP ORDER"]) ? [NSString stringWithFormat:@"X %@",ODB.strQty] : [NSString stringWithFormat:@"X %@",[dictOrder safeValueeForKey:@"qty"]];
    UILabel *lblQuantity = UDcreateLabel(CGRectMake(157, 10.5, 55, 14), strQuantity, [UIFont systemFontOfSize:11 weight:UIFontWeightRegular]);
    lblQuantity.textAlignment = NSTextAlignmentCenter;
    lblQuantity.textColor = UIColorFromRGB(0x434c51);
    
    NSString *strStatus = ([strOrderType isEqualToString:@"APP ORDER"]) ? ODB.strStatus : [dictOrder safeValueeForKey:@"status"];
    UILabel *lblStatus = UDcreateLabel(CGRectMake(APP_CONSTANTS.fltAppWidth - 182, 10, 170, 15), strStatus, [UIFont systemFontOfSize:12 weight:UIFontWeightSemibold]);
    lblStatus.textAlignment = NSTextAlignmentRight;
    lblStatus.textColor = [self getColorWithText:strStatus];
    
    NSString *strerporderno = ([strOrderType isEqualToString:@"APP ORDER"]) ? ODB.strErporderno : [dictOrder safeValueeForKey:@"erporderno"];
    UILabel *lblerporderno = UDcreateLabel(CGRectMake(12, 31, APP_CONSTANTS.fltAppWidth - 24, 15), strerporderno, [UIFont systemFontOfSize:12 weight:UIFontWeightRegular]);
    lblOrderNo.textColor = UIColorFromRGB(0x475055);
    
    NSString *strProdDisplayName = ([strOrderType isEqualToString:@"APP ORDER"]) ? ODB.strProdDisplayName : [dictOrder safeValueeForKey:@"prod_display_name"];
    UILabel *lblProdName = UDcreateLabel(CGRectMake(12, 51, APP_CONSTANTS.fltAppWidth - 24, 15), strProdDisplayName, [UIFont systemFontOfSize:12 weight:UIFontWeightRegular]);
    lblOrderNo.textColor = UIColorFromRGB(0x475055);
    
    NSString *strOrderFullDatetime = ([strOrderType isEqualToString:@"APP ORDER"]) ? ODB.strOrderFullDatetime : [dictOrder safeValueeForKey:@"erporderdt"];
    UILabel *lblOrderDate = UDcreateLabel(CGRectMake(12, 71, APP_CONSTANTS.fltAppWidth - 24, 15), strOrderFullDatetime, [UIFont systemFontOfSize:12 weight:UIFontWeightRegular]);
    lblOrderDate.textColor = UIColorFromRGB(0x475055);
    
    NSString *strDestAddress = ([strOrderType isEqualToString:@"APP ORDER"]) ? ODB.strDestAddress : [dictOrder safeValueeForKey:@"destination_name"];
    UILabel *lblDestAdd = UDcreateLabel(CGRectMake(12, 91, APP_CONSTANTS.fltAppWidth - 24, 20), strDestAddress, [UIFont systemFontOfSize:11 weight:UIFontWeightRegular]);
    lblDestAdd.textColor = [UIColor darkGrayColor];
    
    NSString *strFreight = ([strOrderType isEqualToString:@"APP ORDER"]) ? ODB.strFreight : [dictOrder safeValueeForKey:@"freight"];
    UILabel *lblFright = UDcreateLabel(CGRectMake(12, 120, APP_CONSTANTS.fltAppWidth - 24, 20), strFreight, [UIFont systemFontOfSize:11 weight:UIFontWeightRegular]);
    lblFright.textColor = [UIColor darkGrayColor];
    
    [viewHeader addSubview:imgViewBubble];
    [viewHeader addSubview:lblOrderNo];
    [viewHeader addSubview:lblQuantity];
    [viewHeader addSubview:lblStatus];
    [viewHeader addSubview:lblerporderno];
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
        [self performSegueWithIdentifier:@"trackOrderToMaterialReceived" sender:btnFeedback];
    }else{
        UDShowToastAlertWithTitle(@"Feedback already submitted.", 2);
    }
}

#pragma mark - Gesture Action

-(void)tblSectionClicked:(UITapGestureRecognizer*)tapGesture{
    
    if ([strOrderType isEqualToString:@"APP ORDER"]) {
        int index = [tapGesture.accessibilityHint intValue];
        [arrOrderInvoiceData removeAllObjects];
        
        NSString *strAppOrderNo;
        for (int i=0 ; i < arrOrderData.count ; i++) {
            
            AppLog(@"-->>%d",i);
            if (index == i) {
                OrderDataBO *ODB = arrOrderData[index];
                strAppOrderNo = ODB.strApporderno;
                if ([db open]) {
                    
                    NSString *strQuery = ([strOrderType isEqualToString:@"APP ORDER"]) ? [NSString stringWithFormat:@"SELECT * FROM T_INVOICE WHERE apporderno = '%@'",ODB.strApporderno] : [NSString stringWithFormat:@"SELECT * FROM T_INVOICE WHERE erporderno = '%@'",ODB.strErporderno];
                    FMResultSet *order = [db executeQuery:strQuery];
                    NSMutableArray *arrInvoiceData = [[NSMutableArray alloc]init];
                    while ([order next]) {
                        //retrieve values for each record
                        OrderInvoiceBO *OIBO = [[OrderInvoiceBO alloc]init];
                        
                        //CREATE TABLE T_INVOICE (ch_uid TEXT, apporderno TEXT, erporderno TEXT, challanno TEXT, invno TEXT, invdt TEXT, prod_display_name TEXT, invqty TEXT, customer_code TEXT,  TEXT, destination TEXT)
                        
                        OIBO.strChUid               = [order stringForColumn:@"ch_uid"];
                        OIBO.strApporderno          = [order stringForColumn:@"apporderno"];
                        OIBO.strErporderno          = [order stringForColumn:@"erporderno"];
                        OIBO.strChallanNo           = [order stringForColumn:@"challanno"];
                        OIBO.strInvoiceNo           = [order stringForColumn:@"invno"];
                        OIBO.strInvoiceDate         = [order stringForColumn:@"invdt"];
                        OIBO.strProdDisplayName     = [order stringForColumn:@"prod_display_name"];
                        OIBO.strInvoiceQty          = [order stringForColumn:@"invqty"];
                        OIBO.strCustomerCode        = [order stringForColumn:@"customer_code"];
                        OIBO.strTruckNo             = [order stringForColumn:@"truckno"];
                        OIBO.strDriverNumber             = [order stringForColumn:@"driverno"];
                        OIBO.strDestination         = [order stringForColumn:@"destination"];
                        [arrInvoiceData addObject:OIBO];
                    }
                    [arrOrderInvoiceData addObject:arrInvoiceData];
                    [db close];
                }
            }else{
                [arrOrderInvoiceData addObject:@[]];
            }
        }
        
        AppLog(@"-->>%@",arrOrderInvoiceData);
        [tblViewOrders reloadData];
    }else{
        int index = [tapGesture.accessibilityHint intValue];
        intSelectedIdx = (intSelectedIdx >= 0) ? -1 : index;
        [tblViewOrders reloadData];
    }
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"trackOrderToMaterialReceived"]) {
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
        
        [arrOrderData removeAllObjects];
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
        if (tag == 101) {
            NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
            //[dict setValue:@"C/0000646" forKey:@"the_id"];
            [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
            [dict setValue:strFromDate forKey:@"start_date"];
            [dict setValue:strToDate forKey:@"end_date"];
            
            [SVProgressHUD showWithStatus:@"Loading..."];
            [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_order_details_by_id_v1.php" parameters:dict completion:^(NSDictionary *dictResponse){
                dispatch_async(dispatch_get_main_queue(), ^(void) {
                    [SVProgressHUD dismiss];
                    if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                        //dictTrackOrder = dictResponse;
                        if ([db open]) {
                            [db executeUpdate:@"DROP TABLE T_APPERPDO"];
                            //[db executeUpdate:@"DROP TABLE T_DOCHALLAN"];
                            [db executeUpdate:@"DROP TABLE T_INVOICE"];
                        }
                        [self populateData:dictResponse Tag:tag];
                    }else
                        UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                });
            }];
        }else{
            
            NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
            [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
            //[dict setValue:[defaults valueForKey:@"user_type"] forKey:@"user_type"];
            [dict setValue:strFromDate forKey:@"start_date"];
            [dict setValue:strToDate forKey:@"end_date"];
            
            [SVProgressHUD showWithStatus:@"Loading..."];
            [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_show_offline_order_list.php" parameters:dict completion:^(NSDictionary *dictResponse){
                dispatch_async(dispatch_get_main_queue(), ^(void) {
                    [SVProgressHUD dismiss];
                    if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                        arrOrderDataOffline = [dictResponse safeValueeForKey:@"order_data"];
                        [tblViewOrders reloadData];
                    }else{
                        arrOrderDataOffline = @[];
                        [tblViewOrders reloadData];
                        UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                    }
                });
            }];
        }
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - Set Data Method

-(void)populateData:(NSDictionary*)dictResponse Tag:(NSInteger)tag{
    
    //    "apporderno": "SS0662685",
    //    "erporderno": "OGL3\/00907\/21-22",
    //    "erporderdt": "2021-09-02 10:02:43",
    //    "isdoimp": "",
    //    "order_for": "",
    //    "customer_code": "C\/0029909",
    //    "dns_customer_code": "NEK130",
    //    "status": "Dispatched",
    //    "prod_code": "12123",
    //    "dns_prod_code": "F000000001",
    //    "prod_display_name": "STAR CEMENT PPC",
    //    "qty": "20.000",
    //    "order_full_date_time": "2nd Sep 2021 10:02 AM",
    //    "freight": "EX",
    //    "destination_address": "LOKHRA DUMP3",
    //    "is_confirmed_material_received": "NO",
    //    "quantity_checking": "",
    //    "quality_checking": "",
    //    "remarks": ""
    
    NSString *strQryOrderData = @"CREATE TABLE T_APPERPDO (apporderno TEXT, erporderno TEXT, erporderdt TEXT, isdoimp TEXT, order_for TEXT, customer_code TEXT, dns_customer_code TEXT, status TEXT, prod_code TEXT, dns_prod_code TEXT, prod_display_name TEXT, qty TEXT, order_full_date_time TEXT, destination_address TEXT, freight TEXT, is_confirmed_material_received TEXT, quantity_checking TEXT, quality_checking TEXT, remarks TEXT)";
    
    //    NSString *strQryOrderChallan = @"CREATE TABLE T_DOCHALLAN (ch_uid TEXT, apporderno TEXT, erporderno TEXT, erporderdt TEXT, challanno TEXT, challandt TEXT, prod_code TEXT, dns_prod_code TEXT, prod_display_name TEXT, qty TEXT, challanqty TEXT, customer_code TEXT, dns_customer_code TEXT, truckno TEXT, driverno TEXT, ischlnimp TEXT, is_confirmed_challan_material_received TEXT, challan_quantity_checking TEXT, challan_quality_checking TEXT, challan_remarks TEXT, ch_quality_no_of_damaged_bags TEXT, ch_quantity_no_of_bags TEXT, ch_status TEXT, transporter_name TEXT, colour_code TEXT)";
    
    //    {
    //              "ch_uid": "6430",
    //              "apporderno": "SS0484121",
    //              "erporderno": "3000495403",
    //              "challanno": "8000766005",
    //              "invno": "F23600057607",
    //              "invdt": "20240930",
    //              "prod_display_name": "STAR CEMENT PPC TRADE",
    //              "invqty": "40.000",
    //              "customer_code": "1000000341",
    //              "truckno": "AS06AC8866",
    //              "destination": " 7861030001   TENGAKHAT"
    //            }
    
    NSString *strQryOrderInvoice = @"CREATE TABLE T_INVOICE (ch_uid TEXT, apporderno TEXT, erporderno TEXT, challanno TEXT, invno TEXT, invdt TEXT, prod_display_name TEXT, invqty TEXT, customer_code TEXT, truckno TEXT, destination TEXT, driverno TEXT)";
    
    //NSArray *arrQuery = @[strQryOrderData, strQryOrderChallan];
    NSArray *arrQuery = @[strQryOrderData, strQryOrderInvoice];
    
    
    for (NSString *strQuery in arrQuery) {
        if ([db open]) {
            BOOL success = [db executeStatements:strQuery];
            AppLog(@"-->>%d",success);
        }
    }
    
    NSArray *arrOrder = [dictResponse safeValueeForKey:@"order_data"];
    for (NSDictionary *dict in arrOrder) {
        
        
        
        NSString *strInsertOrder = [NSString stringWithFormat:@"INSERT INTO T_APPERPDO VALUES('%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@')",
                                    [dict safeValueeForKey:@"apporderno"],
                                    [dict safeValueeForKey:@"erporderno"],
                                    [dict safeValueeForKey:@"erporderdt"],
                                    [dict safeValueeForKey:@"isdoimp"],
                                    [dict safeValueeForKey:@"order_for"],
                                    [dict safeValueeForKey:@"customer_code"],
                                    [dict safeValueeForKey:@"dns_customer_code"],
                                    [dict safeValueeForKey:@"status"],
                                    [dict safeValueeForKey:@"prod_code"],
                                    [dict safeValueeForKey:@"dns_prod_code"],
                                    [dict safeValueeForKey:@"prod_display_name"],
                                    [dict safeValueeForKey:@"qty"],
                                    [dict safeValueeForKey:@"order_full_date_time"],
                                    [dict safeValueeForKey:@"destination_address"],
                                    [dict safeValueeForKey:@"freight"],
                                    [dict safeValueeForKey:@"is_confirmed_material_received"],
                                    [dict safeValueeForKey:@"quantity_checking"],
                                    [dict safeValueeForKey:@"quality_checking"],
                                    [dict safeValueeForKey:@"remarks"]];
        [db executeStatements:strInsertOrder];
        
        //        NSArray *arrOrderChallan = [dict safeValueeForKey:@"order_challan_data"];
        //        for (NSDictionary *dictChallan in arrOrderChallan) {
        //
        //            NSString *strInsertOrderChallan = [NSString stringWithFormat:@"INSERT INTO T_DOCHALLAN VALUES('%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@')",
        //                                               [dictChallan safeValueeForKey:@"ch_uid"],
        //                                               [dictChallan safeValueeForKey:@"apporderno"],
        //                                               [dictChallan safeValueeForKey:@"erporderno"],
        //                                               [dictChallan safeValueeForKey:@"erporderdt"],
        //                                               [dictChallan safeValueeForKey:@"challanno"],
        //                                               [dictChallan safeValueeForKey:@"challandt"],
        //                                               [dictChallan safeValueeForKey:@"prod_code"],
        //                                               [dictChallan safeValueeForKey:@"dns_prod_code"],
        //                                               [dictChallan safeValueeForKey:@"prod_display_name"],
        //                                               [dictChallan safeValueeForKey:@"qty"],
        //                                               [dictChallan safeValueeForKey:@"challanqty"],
        //                                               [dictChallan safeValueeForKey:@"customer_code"],
        //                                               [dictChallan safeValueeForKey:@"dns_customer_code"],
        //                                               [dictChallan safeValueeForKey:@"truckno"],
        //                                               [dictChallan safeValueeForKey:@"driverno"],
        //                                               [dictChallan safeValueeForKey:@"ischlnimp"],
        //                                               [dictChallan safeValueeForKey:@"is_confirmed_challan_material_received"],
        //                                               [dictChallan safeValueeForKey:@"challan_quantity_checking"],
        //                                               [dictChallan safeValueeForKey:@"challan_quality_checking"],
        //                                               [dictChallan safeValueeForKey:@"challan_remarks"],
        //                                               [dictChallan safeValueeForKey:@"ch_quality_no_of_damaged_bags"],
        //                                               [dictChallan safeValueeForKey:@"ch_quantity_no_of_bags"],
        //                                               [dictChallan safeValueeForKey:@"ch_status"],
        //                                               [dictChallan safeValueeForKey:@"transporter_name"],
        //                                               [dictChallan safeValueeForKey:@"colour_code"]];
        //
        //            [db executeStatements:strInsertOrderChallan];
        //        }
        
        NSArray *arrOrderInvoice = [dict safeValueeForKey:@"order_invoice_data"];
        for (NSDictionary *dictInvoice in arrOrderInvoice) {
            
            //            {
            //                      "ch_uid": "6430",
            //                      "apporderno": "SS0484121",
            //                      "erporderno": "3000495403",
            //                      "challanno": "8000766005",
            //                      "invno": "F23600057607",
            //                      "invdt": "20240930",
            //                      "prod_display_name": "STAR CEMENT PPC TRADE",
            //                      "invqty": "40.000",
            //                      "customer_code": "1000000341",
            //                      "truckno": "AS06AC8866",
            //                      "destination": " 7861030001   TENGAKHAT"
            //                    }
            
            NSString *strInsertOrderInvoice = [NSString stringWithFormat:@"INSERT INTO T_INVOICE VALUES('%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@')",
                                               [dictInvoice safeValueeForKey:@"ch_uid"],
                                               [dictInvoice safeValueeForKey:@"apporderno"],
                                               [dictInvoice safeValueeForKey:@"erporderno"],
                                               [dictInvoice safeValueeForKey:@"challanno"],
                                               [dictInvoice safeValueeForKey:@"invno"],
                                               [dictInvoice safeValueeForKey:@"invdt"],
                                               [dictInvoice safeValueeForKey:@"prod_display_name"],
                                               [dictInvoice safeValueeForKey:@"invqty"],
                                               [dictInvoice safeValueeForKey:@"customer_code"],
                                               [dictInvoice safeValueeForKey:@"truckno"],
                                               [dictInvoice safeValueeForKey:@"destination"],
                                               [dictInvoice safeValueeForKey:@"driverno"]];
            
            [db executeStatements:strInsertOrderInvoice];
        }
        
        
    }
    
    [self showData:tag];
    //[self btnSegmentClicked:btnAppOrder];
    
}

-(void)showData:(NSInteger)tag{
    if ([db open]) {
        
        NSString *strQuery = (tag == 101) ? @"SELECT * FROM T_APPERPDO WHERE apporderno != '' ORDER BY datetime(erporderdt) DESC" : @"SELECT * FROM T_APPERPDO WHERE apporderno == '' ORDER BY datetime(erporderdt) DESC" ;
        
        [arrOrderData removeAllObjects];
        FMResultSet *order = [db executeQuery:strQuery];
        while ([order next]) {
            //retrieve values for each record
            OrderDataBO *ODB = [[OrderDataBO alloc]init];
            
            ODB.strApporderno                   = [order stringForColumn:@"apporderno"];
            ODB.strErporderno                   = [order stringForColumn:@"erporderno"];
            ODB.strErporderdt                   = [order stringForColumn:@"erporderdt"];
            ODB.strIsdoimp                      = [order stringForColumn:@"isdoimp"];
            ODB.strOrderFor                     = [order stringForColumn:@"order_for"];
            ODB.strCustomerCode                 = [order stringForColumn:@"customer_code"];
            ODB.strDnsCustomerCode              = [order stringForColumn:@"dns_customer_code"]; 
            ODB.strStatus                       = [order stringForColumn:@"status"];
            ODB.strProdCode                     = [order stringForColumn:@"prod_code"];
            ODB.strDnsProdCode                  = [order stringForColumn:@"dns_prod_code"];
            ODB.strProdDisplayName              = [order stringForColumn:@"prod_display_name"];
            ODB.strQty                          = [order stringForColumn:@"qty"];
            ODB.strOrderFullDatetime            = [order stringForColumn:@"order_full_date_time"];
            ODB.strDestAddress                  = [order stringForColumn:@"destination_address"];
            ODB.strFreight                      = [order stringForColumn:@"freight"];
            ODB.strIsConfirmedMaterialReceived  = [order stringForColumn:@"is_confirmed_material_received"];
            ODB.strQuantityChecking             = [order stringForColumn:@"quantity_checking"];
            ODB.strQualityChecking              = [order stringForColumn:@"quality_checking"];
            ODB.strRemarks                      = [order stringForColumn:@"remarks"];
            
            [arrOrderData addObject:ODB];
        }
        //Uncomment this line to show data in ascending order
        //arrOrderData=[[[arrOrderData reverseObjectEnumerator] allObjects] mutableCopy];
        [db close];
        //[arrOrderChallanData removeAllObjects];
        [arrOrderInvoiceData removeAllObjects];
        for (int i = 0 ; i < arrOrderData.count ; i++) {
            //[arrOrderChallanData addObject:@[]];
            [arrOrderInvoiceData addObject:@[]];
        }
        [tblViewOrders reloadData];
    }
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
