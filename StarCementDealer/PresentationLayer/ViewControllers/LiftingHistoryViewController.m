//
//  LiftingHistoryViewController.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 24/05/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "LiftingHistoryViewController.h"
#import "DashboardViewController.h"
#import "LiftingHistoryCell.h"
#import "AddLiftingViewController.h"

@interface LiftingHistoryViewController ()<UITableViewDelegate, UITableViewDataSource, UIPickerViewDelegate, UIPickerViewDataSource, AddLiftingViewControllerDelegate>{
    __weak IBOutlet UITableView *tblViewLiftingHistory;
    
    NSMutableArray *arrPendingLiftingHistory;
    NSMutableArray *arrApproveLiftingHistory;
    NSMutableArray *arrRejectLiftingHistory;
    
    int intPageNoPending;
    int intPageNoApprove;
    int intPageNoReject;
    
    int intSelectedBtnTag;
    
    __weak IBOutlet UIView *viewMonthYearCal;
    __weak IBOutlet UIPickerView *pickerViewMonthYear;
    __weak IBOutlet UIButton *btnPending;
    __weak IBOutlet UIButton *btnApproved;
    __weak IBOutlet UIButton *btnRejected;
    NSMutableArray *arrMonth;
    NSMutableArray *arrYear;
    NSString *strSelectedMonthYear;
}

@end

@implementation LiftingHistoryViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    
    self.title = @"Lifting History";
    
    viewMonthYearCal.hidden = true;
    
    UINavigationBar *bar = [self.navigationController navigationBar];
    [bar setBackgroundColor:UIColorFromRGB(0xEB2228)];
    
    if (@available(iOS 13.0, *)) {
        UIView *statusBar = [[UIView alloc]initWithFrame:[UIApplication sharedApplication].keyWindow.windowScene.statusBarManager.statusBarFrame] ;
        statusBar.backgroundColor = UIColorFromRGB(0xEB2228);
        [[UIApplication sharedApplication].keyWindow addSubview:statusBar];
    } else {
        // Fallback on earlier versions
    }
    
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"back_arrow"] style:UIBarButtonItemStylePlain target:self action:@selector(btnBackClicked:)];
    btnBack.tintColor = [UIColor whiteColor];
    self.navigationItem.leftBarButtonItem = btnBack;
    
    UIBarButtonItem *barButtonFilter = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"filter"] style:UIBarButtonItemStylePlain target:self action:@selector(btnFilterClicked:)];
    barButtonFilter.tintColor = [UIColor whiteColor];
    self.navigationItem.rightBarButtonItem = barButtonFilter;
    
    [tblViewLiftingHistory registerNib:[UINib nibWithNibName:@"LiftingHistoryCell" bundle:nil] forCellReuseIdentifier:@"cellLiftingHistory"];
    tblViewLiftingHistory.separatorColor = [UIColor clearColor];
}

-(void)loadData {
    
    intPageNoPending = 1;
    intPageNoApprove = 1;
    intPageNoReject = 1;
    
    arrPendingLiftingHistory = [[NSMutableArray alloc] init];
    arrApproveLiftingHistory = [[NSMutableArray alloc] init];
    arrRejectLiftingHistory = [[NSMutableArray alloc] init];
    
    NSDate *date = [NSDate date];
    [APP_CONSTANTS.dateFormater setDateFormat:@"MM"];
    NSString *month = [APP_CONSTANTS.dateFormater stringFromDate:date];
    //[APP_CONSTANTS.dateFormater setDateFormat:@"yyyy"];
    //NSString *year = [APP_CONSTANTS.dateFormater stringFromDate:date];
    
    strSelectedMonthYear = @"";
    //strSelectedMonthYear = [NSString stringWithFormat:@"%@-%@", year, month];
    
    arrMonth = [[NSMutableArray alloc] init];
    for (int monthNumber = 1; monthNumber <= 12; monthNumber++) {
        NSString *strMonthName = [[APP_CONSTANTS.dateFormater monthSymbols] objectAtIndex:(monthNumber-1)];
        [arrMonth addObject:strMonthName];
    }
    
    arrYear = [[NSMutableArray alloc] init];
    [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy"];
    NSString *strYear = [APP_CONSTANTS.dateFormater stringFromDate:[NSDate date]];
    NSInteger intCurrentYear = [strYear integerValue];
    
    for (int i = 0; i < 20; i++) {
        int year = (int)intCurrentYear - i;
        [arrYear addObject:[NSString stringWithFormat:@"%d",year]];
    }
    AppLog(@"-->>%@",arrYear);
    [pickerViewMonthYear selectRow:[month integerValue] - 1 inComponent:0 animated:true];
    [self btnStatusClicked:btnPending];
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"liftingHistoryToAddLifting"]) {
        AddLiftingViewController *alvc = segue.destinationViewController;
        alvc.delegate = self;
    }
}

#pragma mark - AddLiftingViewControllerDelegate

- (void)reloadLiftingHistory {
        intPageNoPending = 1;
        [arrPendingLiftingHistory removeAllObjects];
        [self wsPendingLiftingHistory];
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



#pragma mark - Web Service

-(void)wsPendingLiftingHistory {
    if (APP_DELEGATE.isServerReachable) {
        
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[defaults valueForKey:@"emp_code"] forKey:@"sub_dealer_cust_code"];
        [dict setValue:strSelectedMonthYear forKey:@"year_month"];
        [dict setValue:[NSString stringWithFormat:@"%d",intPageNoPending] forKey:@"page_no"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_show_pending_lifting_history_for_sub_dealer.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    [arrPendingLiftingHistory addObjectsFromArray:[dictResponse valueForKey:@"lifting_history_data"]];
                    intPageNoPending += 1;
                    [tblViewLiftingHistory reloadData];
                }else{
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                }
                
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)wsApprovedLiftingHistory {
    if (APP_DELEGATE.isServerReachable) {
        
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[defaults valueForKey:@"emp_code"] forKey:@"sub_dealer_cust_code"];
        [dict setValue:strSelectedMonthYear forKey:@"year_month"];
        [dict setValue:[NSString stringWithFormat:@"%d",intPageNoApprove] forKey:@"page_no"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_show_approve_lifting_history_for_sub_dealer.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    [arrApproveLiftingHistory addObjectsFromArray:[dictResponse valueForKey:@"lifting_history_data"]];
                    intPageNoApprove += 1;
                    [tblViewLiftingHistory reloadData];
                }else{
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                }
                
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)wsRejectedLiftingHistory {
    if (APP_DELEGATE.isServerReachable) {
        
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[defaults valueForKey:@"emp_code"] forKey:@"sub_dealer_cust_code"];
        [dict setValue:strSelectedMonthYear forKey:@"year_month"];
        [dict setValue:[NSString stringWithFormat:@"%d",intPageNoReject] forKey:@"page_no"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_show_reject_lifting_history_for_sub_dealer.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    [arrRejectLiftingHistory addObjectsFromArray:[dictResponse valueForKey:@"lifting_history_data"]];
                    intPageNoReject += 1;
                    [tblViewLiftingHistory reloadData];
                }else{
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                }
                
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - IBAction's

-(void)btnBackClicked:(UIButton*)btn{
    DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
    [self.navigationController pushViewController:dvc animated:NO];
    //[self.navigationController popViewControllerAnimated:true];
}

- (void)btnFilterClicked:(UIBarButtonItem *)sender {
    viewMonthYearCal.hidden = false;
}

- (IBAction)btnAddLiftingClicked:(id)sender {
    [self performSegueWithIdentifier:@"liftingHistoryToAddLifting" sender:self];
}

- (IBAction)btnDoneClicked:(UIBarButtonItem *)sender {
    
    NSInteger intSelectedMonthRow = [pickerViewMonthYear selectedRowInComponent:0];
    intSelectedMonthRow += 1;
    
    NSString *strSelectedMonth = [NSString stringWithFormat:@"%02ld",(long)intSelectedMonthRow];
    NSString *strSelectedYear = arrYear[[pickerViewMonthYear selectedRowInComponent:1]];
    strSelectedMonthYear = [NSString stringWithFormat:@"%@-%@", strSelectedYear, strSelectedMonth];
    
    viewMonthYearCal.hidden = true;
    
    if (intSelectedBtnTag == 101) {
        [arrPendingLiftingHistory removeAllObjects];
        intPageNoPending = 1;
        [self wsPendingLiftingHistory];
    }else if (intSelectedBtnTag == 102){
        [arrApproveLiftingHistory removeAllObjects];
        intPageNoApprove = 1;
        [self wsApprovedLiftingHistory];
    }else {
        [arrRejectLiftingHistory removeAllObjects];
        intPageNoReject = 1;
        [self wsRejectedLiftingHistory];
    }
}

- (IBAction)btnCancelClicked:(UIBarButtonItem *)sender {
    viewMonthYearCal.hidden = true;
}

- (IBAction)btnStatusClicked:(UIButton *)sender {
    NSArray *arrSegment = @[btnPending, btnApproved, btnRejected];
    for (UIButton *btnSegment in arrSegment) {
        [btnSegment setSelected:NO];
    }
    [sender setSelected:YES];
    intSelectedBtnTag = (int)sender.tag;
    
    if (intSelectedBtnTag == 101) {
        if (!arrPendingLiftingHistory.count) {
            [self wsPendingLiftingHistory];
        } else {
            [tblViewLiftingHistory reloadData];
        }
    }else if (intSelectedBtnTag == 102){
        if (!arrApproveLiftingHistory.count) {
            [self wsApprovedLiftingHistory];
        } else {
            [tblViewLiftingHistory reloadData];
        }
    }else {
        if (!arrRejectLiftingHistory.count) {
            [self wsRejectedLiftingHistory];
        } else {
            [tblViewLiftingHistory reloadData];
        }
    }
}


#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    if (intSelectedBtnTag == 101) {
        return arrPendingLiftingHistory.count;
    }else if (intSelectedBtnTag == 102){
        return arrApproveLiftingHistory.count;
    } else {
        return arrRejectLiftingHistory.count;
    }
}

- (LiftingHistoryCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    static NSString *cellIdentifier = @"cellLiftingHistory";
    LiftingHistoryCell *cell = [tblViewLiftingHistory dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    
    NSDictionary *dict;
    if (intSelectedBtnTag == 101) {
        dict = arrPendingLiftingHistory[indexPath.row];
    }else if (intSelectedBtnTag == 102){
        dict = arrApproveLiftingHistory[indexPath.row];
    } else {
        dict = arrRejectLiftingHistory[indexPath.row];
    }
    
    AppLog(@"-->>%@",dict);
    
    cell.lblProdName.text       = [NSString stringWithFormat:@"Product Name : %@",[dict safeValueeForKey:@"product_name"]];
    cell.lblQty.text            = [NSString stringWithFormat:@"Quantity in Bags : %@",[dict safeValueeForKey:@"qty_in_bags"]];
    cell.lblDate.text           = [NSString stringWithFormat:@"Date of Lifting : %@",[dict safeValueeForKey:@"date_of_lifting_show"]];
    cell.lblChallanNumber.text  = [NSString stringWithFormat:@"Challan Number : %@",[dict safeValueeForKey:@"challan_number"]];
    cell.lblStatus.text         = [NSString stringWithFormat:@"Status : %@",[dict safeValueeForKey:@"status"]];
    cell.lblLinkedDealerName.text  = [NSString stringWithFormat:@"Linked Dealer Name : %@",[dict safeValueeForKey:@"linked_dealer_name"]];
    cell.lblReasonForRejection.text  = [NSString stringWithFormat:@"Reason for Rejection : %@",[dict safeValueeForKey:@"reason_for_rejection"]];
    
    if ([[dict safeValueeForKey:@"status"] isEqualToString:@"PENDING"]) {
        
        cell.constraintApprovedDateHeight.constant = 0;
        cell.lblApprovedDate.text = @"";
        
        cell.constraintReasonForRejectionHeight.constant = 0;
        cell.lblReasonForRejection.text = @"";
        
    }else if ([[dict safeValueeForKey:@"status"] isEqualToString:@"APPROVED"]){
        
        cell.constraintApprovedDateHeight.constant = 16;
        cell.lblApprovedDate.text = [NSString stringWithFormat:@"Approved Date : %@",[dict safeValueeForKey:@"approved_rejection_date_show"]];
        
        cell.constraintReasonForRejectionHeight.constant = 0;
    }else {
        
        cell.constraintApprovedDateHeight.constant = 16;
        cell.lblApprovedDate.text = [NSString stringWithFormat:@"Rejection Date : %@",[dict safeValueeForKey:@"approved_rejection_date_show"]];
        
        cell.constraintReasonForRejectionHeight.constant = 16;
    }
    
    return cell;
}

- (void)scrollViewDidEndDragging:(UIScrollView *)scrollView willDecelerate:(BOOL)decelerate {
    CGFloat currentOffset = scrollView.contentOffset.y;
    CGFloat maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height;
    
    if (maximumOffset - currentOffset <= 10.0) {
        if (intSelectedBtnTag == 101) {
            [self wsPendingLiftingHistory];
        }else if (intSelectedBtnTag == 102){
            [self wsApprovedLiftingHistory];
        }else {
            [self wsRejectedLiftingHistory];
        }        
    }
}


@end
