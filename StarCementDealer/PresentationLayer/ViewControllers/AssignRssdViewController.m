//
//  AssignRssdViewController.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 11/04/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import "AssignRssdViewController.h"
#import "LiftingAssignedCell.h"
#import "LiftingAllocateCell.h"
#import "SubDealerListForLiftingAllocation.h"

#define Section_Heder_Height 169

@interface  AssignRssdViewController ()<UITableViewDelegate, UITableViewDataSource, UISearchBarDelegate, UIPickerViewDataSource, UIPickerViewDelegate>{
    __weak IBOutlet UIButton *btnAllocated;
    __weak IBOutlet UIButton *btnAssigned;
    __weak IBOutlet UITableView *tblViewAssigned;
    __weak IBOutlet UITableView *tblViewAllocated;
    __weak IBOutlet NSLayoutConstraint *constraintBtnAssignedHeight;
    __weak IBOutlet NSLayoutConstraint *constrainedBtnAllocatedHeight;
    
    __weak IBOutlet UIView *viewMonthYearCal;
    __weak IBOutlet UIPickerView *pickerViewMonthYear;
    NSMutableArray *arrMonth;
    NSMutableArray *arrYear;
    NSString *strSelectedMonthYear;
    NSMutableSet *expandedSections;
    
    int intSelectedTabTag;
    NSArray *arrOrderData;
    NSMutableArray *arrAssignedSelectedIdx; //Using to open close section
    NSMutableArray *arrShowAllocatedButtonList; //Using to capture order_id where showing allocate button on cell
    NSArray *arrAllocatedList; //Using to show allocate list
    NSArray *arrAllocatedListConst; //Using to hold allocate list for filter purpose
    int intRssdAllocationDays;
    NSString *strUserType; //Only for rssd allocation history api call
    
    NSString *strTitleAssigned;
    NSString *strTitleAllocated;
    
    NSArray *counterNames;
    NSMutableArray *allocationsGrouped;
}

@end

@implementation AssignRssdViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    expandedSections = [NSMutableSet set];
    [self designView];
    [self loadData];
}

- (void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self];
}

#pragma mark - Initialization Method

-(void)designView {
    
    intSelectedTabTag = 101;
    viewMonthYearCal.hidden = true;
    
    if (@available(iOS 15, *)) {
        tblViewAssigned.sectionHeaderTopPadding = 0;
        tblViewAllocated.sectionHeaderTopPadding = 0;
    }
    
    UIBarButtonItem *barButtonFilter = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"filter"] style:UIBarButtonItemStylePlain target:self action:@selector(btnFilterClicked:)];
    barButtonFilter.tintColor = [UIColor whiteColor];
    self.navigationItem.rightBarButtonItem = barButtonFilter;
    
    tblViewAllocated.hidden = true;
    [tblViewAssigned registerNib:[UINib nibWithNibName:@"LiftingAssignedCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    [tblViewAllocated registerNib:[UINib nibWithNibName:@"LiftingAllocateCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"back_arrow"] style:UIBarButtonItemStylePlain target:self action:@selector(btnBackClicked:)];
    btnBack.tintColor = [UIColor whiteColor];
    self.navigationItem.leftBarButtonItem = btnBack;
    
    [btnAssigned setSelected:true];
    
    if (checkUserType(kSubDealer) == true || checkUserType(kRssd) == true) {
        strUserType = @"RSSD";
        [self btnSegmentClicked:btnAllocated];
        btnAssigned.hidden = true;
        btnAllocated.hidden = true;
        constraintBtnAssignedHeight.constant = 0;
        constrainedBtnAllocatedHeight.constant = 0;
    }else{
        strUserType = @"DEALER";
    }
    

}

-(void)loadData {
    
    NSDate *date = [NSDate date];
    [APP_CONSTANTS.dateFormater setDateFormat:@"MM"];
    NSString *month = [APP_CONSTANTS.dateFormater stringFromDate:date];
    [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy"];
    NSString *strYear = [APP_CONSTANTS.dateFormater stringFromDate:date];
    
    strSelectedMonthYear = [NSString stringWithFormat:@"%@-%@", strYear, month];
    
    arrMonth = [[NSMutableArray alloc] init];
    for (int monthNumber = 1; monthNumber <= 12; monthNumber++) {
        NSString *strMonthName = [[APP_CONSTANTS.dateFormater monthSymbols] objectAtIndex:(monthNumber-1)];
        [arrMonth addObject:strMonthName];
    }
    
    self.title = @"ASSIGNED";
    
    arrYear = [[NSMutableArray alloc] init];
    [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy"];
    NSInteger intCurrentYear = [strYear integerValue];
    
    for (int i = 0; i < 20; i++) {
        int year = (int)intCurrentYear - i;
        [arrYear addObject:[NSString stringWithFormat:@"%d",year]];
    }
    AppLog(@"-->>%@",arrYear);
    
    [pickerViewMonthYear selectRow:[month integerValue] - 1 inComponent:0 animated:true];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(updateBothTableData:)
                                                 name:@"updateLiftingAllocation"
                                               object:nil];
    
    arrAssignedSelectedIdx = [[NSMutableArray alloc] init];
    arrShowAllocatedButtonList = [[NSMutableArray alloc] init];
    
    //NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    if (checkUserType(kbroker) == true || checkUserType(kDealer) == true) {
        [self getDispatchedOrderList];
    }    
    
    //    if ([[defaults valueForKey:@"user_type"] isEqualToString:@"broker"] || [[defaults valueForKey:@"user_type"] isEqualToString:@"dealer"]) {
    //        [self getDispatchedOrderList];
    //    }
}

#pragma mark - IBAction's

-(void)btnBackClicked:(UIBarButtonItem*)button{
    [self.navigationController popViewControllerAnimated:true];
}

- (IBAction)btnSegmentClicked:(UIButton *)sender {
    NSArray *arrSegment = @[btnAssigned, btnAllocated];
    for (UIButton *btnSegment in arrSegment) {
        [btnSegment setSelected:NO];
    }
    intSelectedTabTag = (int)sender.tag;
    [sender setSelected:YES];
    
    tblViewAssigned.hidden = (intSelectedTabTag != 101) ? true : false;
    tblViewAllocated.hidden = (intSelectedTabTag == 101) ? true : false;
    
    if (intSelectedTabTag == 101) {
        self.title = @"ASSIGNED";
        viewMonthYearCal.hidden = true;
        [tblViewAssigned reloadData];
    }else{
        self.title = @"ALLOCATED";
        if (arrAllocatedList.count == 0) {
            [self getAllocationLifting];
            return;
        }
        [tblViewAllocated reloadData];
    }
}

- (void)btnFilterClicked:(UIBarButtonItem *)sender {
    viewMonthYearCal.hidden = false;
}

-(void)btnHeaderSectionClicked:(UIButton*)sender {
    NSArray *arrDispatchedChallanData = [[arrOrderData objectAtIndex:sender.tag] valueForKey:@"dispatched_invoice_data"];
    NSNumber *number = arrAssignedSelectedIdx[sender.tag];
    if ([number intValue] == 0) {
        [arrAssignedSelectedIdx replaceObjectAtIndex:sender.tag withObject:[NSNumber numberWithInt:(int)arrDispatchedChallanData.count]];
    }else{
        [arrAssignedSelectedIdx replaceObjectAtIndex:sender.tag withObject:[NSNumber numberWithInt:0]];
    }
    [tblViewAssigned reloadData];
}

- (IBAction)btnDoneClicked:(UIBarButtonItem *)sender {
    NSInteger intSelectedMonthRow = [pickerViewMonthYear selectedRowInComponent:0];
    intSelectedMonthRow += 1;
    
    NSString *strSelectedMonth = [NSString stringWithFormat:@"%02ld",(long)intSelectedMonthRow];
    NSString *strSelectedYear = arrYear[[pickerViewMonthYear selectedRowInComponent:1]];
    
    strSelectedMonthYear = [NSString stringWithFormat:@"%@-%@", strSelectedYear, strSelectedMonth];
    viewMonthYearCal.hidden = true;
    
    if (intSelectedTabTag == 101) {
        self.title = @"ASSIGNED";
        [self getDispatchedOrderList];
    }else{
        self.title = @"ALLOCATED";
        [self getAllocationLifting];
    }
}



#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"liftingAllocationToSubdealerList"]) {
        NSDictionary *dictOrder = (NSDictionary*)sender;
        SubDealerListForLiftingAllocation *subDealerVC = segue.destinationViewController;
        subDealerVC.dictOrder = dictOrder;
    }
}

#pragma mark - Web Service

-(void)getDispatchedOrderList{
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSString *strDealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:strDealerId forKey:@"customer_code"];
        [dict setValue:strSelectedMonthYear forKey:@"month_year"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/dispatched-order-list-invoicewise-v10.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    
                    [arrAssignedSelectedIdx removeAllObjects];
                    [arrShowAllocatedButtonList removeAllObjects];
                    
                    arrOrderData = [dictResponse safeValueeForKey:@"dispatched_order_data"];
                    intRssdAllocationDays = [[dictResponse safeValueeForKey:@"rssd_allocation_days"] intValue];
                    for (int i = 0; i < arrOrderData.count; i++) {
                        
                        [arrAssignedSelectedIdx addObject:[NSNumber numberWithInt:0]]; //To open close section
                        
                        NSDictionary *dictOrder = [arrOrderData objectAtIndex:i];
                        NSString *strOrderDate = [dictOrder safeValueeForKey:@"order_date"];
                        NSUInteger length = ([strOrderDate length] == 22) ? 13 : 12;
                        NSArray *arrOrderDate = [[strOrderDate substringToIndex:length] componentsSeparatedByString:@" "];
                        NSString *strDate = [arrOrderDate objectAtIndex:0];
                        strDate = [strDate substringToIndex:[strDate length] - 2];
                        NSString *strFormattedDate = [NSString stringWithFormat:@"%@-%@-%@",strDate, arrOrderDate[1], arrOrderDate[2]];
                        
                        APP_CONSTANTS.dateFormater.dateFormat = @"dd-MMM-yyyy";
                        NSDate *dateOrder = [APP_CONSTANTS.dateFormater dateFromString:strFormattedDate];
                        NSDate *dateCurrent = [NSDate date];
                        
                        int intDaysPassed = [self daysBetweenDates:dateOrder currentDate:dateCurrent];
                        BOOL flag = (intDaysPassed <= intRssdAllocationDays && intRssdAllocationDays != 0) ? true : false;
                        if (flag) {
                            [arrShowAllocatedButtonList addObject:[dictOrder safeValueeForKey:@"order_id"]];
                        }
                    }
                    [tblViewAssigned reloadData];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

- (void)getAllocationLifting {
    if (APP_DELEGATE.isServerReachable) {
        NSString *strDealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
        NSMutableDictionary *dict = [[NSMutableDictionary alloc] init];
        [dict setValue:strDealerId forKey:@"customer_id"];
        [dict setValue:strUserType forKey:@"user_type"];
        [dict setValue:strSelectedMonthYear forKey:@"year_month"];

        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/ajax_allocation_lifting_invoicewise_v10.php"
                        parameters:dict
                        completion:^(NSDictionary *dictResponse) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [SVProgressHUD dismiss];

                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    NSArray *allocationArray = [dictResponse safeValueeForKey:@"allocation_data"];

                    // Group the allocation data by counter_name
                    NSMutableDictionary<NSString *, NSMutableArray *> *groupedData = [NSMutableDictionary dictionary];
                    for (NSDictionary *item in allocationArray) {
                        NSString *counterName = item[@"counter_name"];
                        if (counterName.length == 0) continue;

                        if (!groupedData[counterName]) {
                            groupedData[counterName] = [NSMutableArray array];
                        }
                        [groupedData[counterName] addObject:item];
                    }

                    // Store the grouped data and counter names
                    counterNames = [[groupedData allKeys] sortedArrayUsingSelector:@selector(localizedCaseInsensitiveCompare:)];
                    allocationsGrouped = [NSMutableArray array];
                    for (NSString *name in counterNames) {
                        [allocationsGrouped addObject:groupedData[name]];
                    }

                    [tblViewAllocated reloadData];
                } else {
                    counterNames = @[];
                    allocationsGrouped = @[];
                    [tblViewAllocated reloadData];
                }
            });
        }];
    } else {
        UDShowToastAlertWithTitle(kNoInternet, 2);
    }
}


#pragma mark - UITableViewDelegate and DataSource

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    return (intSelectedTabTag == 101) ? arrOrderData.count : counterNames.count;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    if (intSelectedTabTag == 101) {
        NSNumber *number = arrAssignedSelectedIdx[section];
        return [number intValue];
    } else {
        // Return the number of items for the current counter name
       if ([expandedSections containsObject:@(section)]) {
            NSArray *items = allocationsGrouped[section];
            return items.count;
        } else {
            return 0;
        }
    }
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    static NSString *cellIdentifier = @"cell";
    if (intSelectedTabTag == 101) {
        LiftingAssignedCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
        NSArray *arrDispatchedChallanData = arrOrderData[indexPath.section][@"dispatched_invoice_data"];
        NSString *strOrderDate = arrOrderData[indexPath.section][@"order_date"];
        NSDictionary *dict = arrDispatchedChallanData[indexPath.row];

        cell.lblChallanNo.text = [dict safeValueeForKey:@"invno"];
        cell.lblDate.text = [dict safeValueeForKey:@"invdt"];
        cell.lblQty.text = [dict safeValueeForKey:@"invqty"];
        cell.btnAllocate.hidden = ![self showAllocateButton:strOrderDate];
        [cell.btnAllocate addTarget:self action:@selector(btnAllocateClicked:) forControlEvents:UIControlEventTouchUpInside];
        cell.btnAllocate.accessibilityElements = @[indexPath];
        return cell;
    } else {
        LiftingAllocateCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
        NSArray *items = allocationsGrouped[indexPath.section];
        NSDictionary *dict = items[indexPath.row];

        cell.lblProdDesc.text = [NSString stringWithFormat:@"Product : %@", [dict safeValueeForKey:@"prod_desc"]];
        cell.lblQty.text = [NSString stringWithFormat:@"Allocated Qty : %@", [dict safeValueeForKey:@"allocation_qty"]];
        cell.lblDate.text = [NSString stringWithFormat:@"Trans Date : %@", [dict safeValueeForKey:@"date_and_time"]];
        cell.lblCounterName.text = [NSString stringWithFormat:@"Counter Name : %@", [dict safeValueeForKey:@"counter_name"]];
        cell.lblChallanNo.text = [NSString stringWithFormat:@"Invoice No : %@", [dict safeValueeForKey:@"inv_no"]];
        cell.lblChallanDate.text = [NSString stringWithFormat:@"Invoice Date : %@", [dict safeValueeForKey:@"inv_date"]];
        return cell;
    }
}

- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section {
    if (intSelectedTabTag == 101) {
        
        UIView *viewHeader = [[UIView alloc] initWithFrame:CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, Section_Heder_Height)];
        
        UIView *viewPadding = [[UIView alloc] initWithFrame:CGRectMake(4, 4, APP_CONSTANTS.fltAppWidth - 8, Section_Heder_Height - 6)];
        viewPadding.backgroundColor = [UIColor whiteColor];
        
        NSDictionary *dictOrder = [arrOrderData objectAtIndex:section];
        
        int intYPos = 8;
        UILabel *lblOrderId = UDcreateLabel(CGRectMake(8, intYPos, APP_CONSTANTS.fltAppWidth - 156, 18), [dictOrder safeValueeForKey:@"order_id"], [UIFont systemFontOfSize:12 weight:UIFontWeightSemibold]);
        lblOrderId.backgroundColor = [UIColor whiteColor];
        
        BOOL flagQtyAvl = false;
        NSArray *arrDisInvData = [dictOrder safeValueeForKey:@"dispatched_invoice_data"];
        for (NSDictionary *dictInvData in arrDisInvData) {
            float fltAvlAllocationQty = [[dictInvData safeValueeForKey:@"available_allocation_qty"] floatValue];
            if (fltAvlAllocationQty > 0.0) {
                flagQtyAvl = true;
            }
        }
        lblOrderId.textColor = (!flagQtyAvl) ? UIColorFromRGB(0x4a973a) : [UIColor blackColor];        
        
        UILabel *lblStatus = UDcreateLabel(CGRectMake(APP_CONSTANTS.fltAppWidth - 8 - 50 - 80 , intYPos, 80, 18), [dictOrder safeValueeForKey:@"STATUS"], [UIFont systemFontOfSize:10 weight:UIFontWeightRegular]);
        lblStatus.textAlignment = NSTextAlignmentRight;
        lblStatus.backgroundColor = [UIColor whiteColor];
        
        intYPos += 20;
        UILabel *lblProdName = UDcreateLabel(CGRectMake(8, intYPos, APP_CONSTANTS.fltAppWidth - 50 - 8 - 8, 18), [dictOrder safeValueeForKey:@"prod_display_name"], [UIFont systemFontOfSize:10 weight:UIFontWeightRegular]);
        lblProdName.backgroundColor = [UIColor whiteColor];
        
        intYPos += 20;
        UILabel *lblOrderDate = UDcreateLabel(CGRectMake(8, intYPos, APP_CONSTANTS.fltAppWidth - 50 - 8 - 8, 18), [dictOrder safeValueeForKey:@"order_date"], [UIFont systemFontOfSize:10 weight:UIFontWeightRegular]);
        lblOrderDate.backgroundColor = [UIColor whiteColor];
        
        intYPos += 20;
        UILabel *lblDestination = UDcreateLabel(CGRectMake(8, intYPos, APP_CONSTANTS.fltAppWidth - 50 - 8 - 8, 18), [dictOrder safeValueeForKey:@"destination_name"], [UIFont systemFontOfSize:10 weight:UIFontWeightRegular]);
        lblDestination.backgroundColor = [UIColor whiteColor];
        
        intYPos += 20;
        UILabel *lblFreight = UDcreateLabel(CGRectMake(8, intYPos, APP_CONSTANTS.fltAppWidth - 50 - 8 - 8, 18), [dictOrder safeValueeForKey:@"freight"], [UIFont systemFontOfSize:10 weight:UIFontWeightRegular]);
        lblFreight.backgroundColor = [UIColor whiteColor];
        
        intYPos += 20;
        UILabel *lblQty = UDcreateLabel(CGRectMake(8, intYPos, APP_CONSTANTS.fltAppWidth - 50 - 8 - 8, 18), [NSString stringWithFormat:@"Qty : %@",[dictOrder safeValueeForKey:@"qty"]], [UIFont systemFontOfSize:10 weight:UIFontWeightRegular]);
        lblQty.backgroundColor = [UIColor whiteColor];
        
        intYPos += 25;
        UIButton *btnAllocate = [UIButton buttonWithType:UIButtonTypeCustom];
        btnAllocate.userInteractionEnabled = false;
        [btnAllocate setTitleColor:[UIColor blackColor] forState:UIControlStateNormal];
        btnAllocate.titleLabel.font = [UIFont systemFontOfSize:10 weight:UIFontWeightRegular];
        btnAllocate.frame = CGRectMake(APP_CONSTANTS.fltAppWidth - 8 - 8 - 70, intYPos, 70, 22);
        btnAllocate.backgroundColor = [UIColor clearColor];
        [btnAllocate setTitle:@"Allocate" forState:UIControlStateNormal];
        btnAllocate.hidden = (![arrShowAllocatedButtonList containsObject:[dictOrder safeValueeForKey:@"order_id"]]) ? true : false;
        
        UIImageView *imgViewArrow = [[UIImageView alloc] initWithFrame:CGRectMake(APP_CONSTANTS.fltAppWidth - 8 - 8 - 15, Section_Heder_Height / 2 - 4.5, 15, 9)];
        imgViewArrow.image = [UIImage imageNamed:@"down_arrow"];
        
        UIButton *btnAction = [UIButton buttonWithType:UIButtonTypeCustom];
        btnAction.tag = section;
        [btnAction addTarget:self action:@selector(btnHeaderSectionClicked:) forControlEvents:UIControlEventTouchUpInside];
        btnAction.frame = CGRectMake(0, 0, viewPadding.frame.size.width, btnAllocate.frame.origin.y - 7);
        btnAction.backgroundColor = [UIColor clearColor];
        
        viewPadding.layer.cornerRadius = 5.0;
        viewPadding.layer.masksToBounds = false;
        viewPadding.layer.shadowRadius = 2.0f;
        viewPadding.layer.shadowOffset = CGSizeMake(0, 0);
        viewPadding.layer.shadowColor = [UIColor blackColor].CGColor;
        viewPadding.layer.shadowOpacity = 0.4;
        
        [viewPadding addSubview:lblOrderId];
        [viewPadding addSubview:lblStatus];
        [viewPadding addSubview:lblProdName];
        [viewPadding addSubview:lblOrderDate];
        [viewPadding addSubview:lblDestination];
        [viewPadding addSubview:lblFreight];
        [viewPadding addSubview:lblQty];
        [viewPadding addSubview:btnAllocate];
        [viewPadding addSubview:imgViewArrow];
        [viewPadding addSubview:btnAction];
        
        [viewHeader addSubview:viewPadding];
        return viewHeader;
        
    }else{
        UIView *viewHeader = [[UIView alloc] initWithFrame:CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, 50)];
                viewHeader.backgroundColor = UIColorFromRGB(0xeeeeee); // Set background color to #eeeeee

                UILabel *lblCounterName = UDcreateLabel(CGRectMake(8, 0, APP_CONSTANTS.fltAppWidth - 16, 50), counterNames[section], [UIFont systemFontOfSize:14 weight:UIFontWeightRegular]);
                lblCounterName.backgroundColor = [UIColor clearColor];

                UIButton *btnToggle = [UIButton buttonWithType:UIButtonTypeCustom];
                btnToggle.frame = viewHeader.bounds;
                btnToggle.tag = section;
                [btnToggle addTarget:self action:@selector(toggleSection:) forControlEvents:UIControlEventTouchUpInside];

                [viewHeader addSubview:lblCounterName];
                [viewHeader addSubview:btnToggle];

                return viewHeader;
    }
     return nil;
}

- (CGFloat)tableView:(UITableView *)tableView heightForFooterInSection:(NSInteger)section {
    return intSelectedTabTag == 101?3.0:10.0; // Height of the gap
}

- (UIView *)tableView:(UITableView *)tableView viewForFooterInSection:(NSInteger)section {
    UIView *footerView = [[UIView alloc] initWithFrame:CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, 10)];
    footerView.backgroundColor = [UIColor clearColor]; // Clear color for the gap
    return footerView;
}

- (void)toggleSection:(UIButton *)sender {
    NSInteger section = sender.tag;
    if ([expandedSections containsObject:@(section)]) {
        [expandedSections removeObject:@(section)];
    } else {
        [expandedSections addObject:@(section)];
    }
    [tblViewAllocated reloadSections:[NSIndexSet indexSetWithIndex:section] withRowAnimation:UITableViewRowAnimationFade];
}

- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section {
    return (intSelectedTabTag == 101) ? 169 : 50;
}

- (void)scrollViewWillBeginDragging:(UIScrollView *)scrollView {
    // Resign the keyboard if it is visible
    [self.view endEditing:YES];
}

#pragma mark - Cell Action

-(void)btnAllocateClicked:(UIButton*)sender {
    
    NSIndexPath *indexPath = sender.accessibilityElements[0];
    NSDictionary *dictOrder = arrOrderData[indexPath.section];
    NSArray *arrDispatchedChallanData = [dictOrder safeValueeForKey:@"dispatched_invoice_data"];
    NSDictionary *dict = arrDispatchedChallanData[indexPath.row];
    
    NSMutableDictionary *dictData = [[NSMutableDictionary alloc] init];
    [dictData setValue:[dictOrder safeValueeForKey:@"order_id"] forKey:@"order_id"];
    [dictData setValue:[dictOrder safeValueeForKey:@"prod_display_name"] forKey:@"prod_display_name"];
    [dictData setValue:[dict safeValueeForKey:@"invdt"] forKey:@"order_date"];
    [dictData setValue:[dictOrder safeValueeForKey:@"dns_prod_code"] forKey:@"dns_prod_code"];
    [dictData setValue:[dict safeValueeForKey:@"invqty"] forKey:@"invoice_qty"];
    [dictData setValue:[NSString stringWithFormat:@"%@",[dict safeValueeForKey:@"available_allocation_qty"]] forKey:@"available_allocation_qty"];
    [dictData setValue:[dict safeValueeForKey:@"invno"] forKey:@"invoice_no"];
    [dictData setValue:[dict safeValueeForKey:@"invdt"] forKey:@"invoice_date"];
    
    [self performSegueWithIdentifier:@"liftingAllocationToSubdealerList" sender:dictData];
    
}

#pragma mark - UISearchBar Delegate

- (void)searchBar:(UISearchBar *)searchBar textDidChange:(NSString *)searchText {
    if (searchText.length != 0) {
        // Filter the counter names
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"SELF contains[cd] %@", searchText];
        NSArray *filteredCounterNames = [counterNames filteredArrayUsingPredicate:predicate];

        // Update the grouped data based on the filtered counter names
        NSMutableArray *filteredAllocationsGrouped = [NSMutableArray array];
        for (NSString *name in filteredCounterNames) {
            NSUInteger index = [counterNames indexOfObject:name];
            if (index != NSNotFound) {
                [filteredAllocationsGrouped addObject:allocationsGrouped[index]];
            }
        }

        // Update the data sources
        counterNames = filteredCounterNames;
        allocationsGrouped = filteredAllocationsGrouped;
    } else {
        // Reset the data sources
        NSMutableDictionary<NSString *, NSMutableArray *> *groupedData = [NSMutableDictionary dictionary];
        for (NSArray *items in allocationsGrouped) {
            for (NSDictionary *item in items) {
                NSString *counterName = item[@"counter_name"];
                if (counterName.length == 0) continue;

                if (!groupedData[counterName]) {
                    groupedData[counterName] = [NSMutableArray array];
                }
                [groupedData[counterName] addObject:item];
            }
        }

        counterNames = [[groupedData allKeys] sortedArrayUsingSelector:@selector(localizedCaseInsensitiveCompare:)];
        allocationsGrouped = [NSMutableArray array];
        for (NSString *name in counterNames) {
            [allocationsGrouped addObject:groupedData[name]];
        }
    }
    [expandedSections removeAllObjects];
    [tblViewAllocated reloadData];
}


- (void)searchBarSearchButtonClicked:(UISearchBar *)searchBar{
    [searchBar resignFirstResponder];
}

#pragma mark - UIPickerView Delegate and DataSource

- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView {
    return 2;
}

- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component {
    if (component == 0) {
        return arrMonth.count;
    } else {
        return arrYear.count;
    }
}

- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component {
    if (component == 0) {
        return arrMonth[row];
    } else {
        return arrYear[row];
    }
}

#pragma mark - Helper Method

- (int)daysBetweenDates: (NSDate *)startDate currentDate: (NSDate *)endDate {
    NSCalendar *calendar = [NSCalendar currentCalendar];
    NSDateComponents *dateComponent = [calendar components:NSCalendarUnitDay fromDate:startDate toDate:endDate options:0];
    
    int totalDays = (int)dateComponent.day;
    return totalDays;
}

- (void)updateBothTableData:(NSNotification *)notification {
    // Handle the updated data here
    arrOrderData = @[];
    arrAllocatedList = @[];
    arrAllocatedListConst = @[];
    [arrAssignedSelectedIdx removeAllObjects];
    [arrShowAllocatedButtonList removeAllObjects];
    
    [self getDispatchedOrderList];
    [self getAllocationLifting];
}

-(BOOL)showAllocateButton:(NSString *)strOrderDate {
    
    NSUInteger length = ([strOrderDate length] == 22) ? 13 : 12;
    NSArray *arrOrderDate = [[strOrderDate substringToIndex:length] componentsSeparatedByString:@" "];
    NSString *strDate = [arrOrderDate objectAtIndex:0];
    strDate = [strDate substringToIndex:[strDate length] - 2];
    NSString *strFormattedDate = [NSString stringWithFormat:@"%@-%@-%@",strDate, arrOrderDate[1], arrOrderDate[2]];
    
    APP_CONSTANTS.dateFormater.dateFormat = @"dd-MMM-yyyy";
    NSDate *dateOrder = [APP_CONSTANTS.dateFormater dateFromString:strFormattedDate];
    NSDate *dateCurrent = [NSDate date];
    
    int intDaysPassed = [self daysBetweenDates:dateOrder currentDate:dateCurrent];
    BOOL flag = (intDaysPassed <= intRssdAllocationDays && intRssdAllocationDays != 0) ? true : false;
    return flag;
    
}

@end
