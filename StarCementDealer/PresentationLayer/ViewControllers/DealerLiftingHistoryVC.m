//
//  DealerLiftingHistoryVC.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 25/05/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "DealerLiftingHistoryVC.h"
#import "DashboardViewController.h"
#import "DealerLiftingHistoryCell.h"

@interface DealerLiftingHistoryVC ()<UITableViewDelegate, UITableViewDataSource, UIPickerViewDelegate, UIPickerViewDataSource, UISearchBarDelegate>{
    __weak IBOutlet UITableView *tblViewLiftingHistory;
    NSMutableArray *arrPendingLiftingHistory;
    NSMutableArray *arrApproveLiftingHistory;
    NSMutableArray *arrRejectLiftingHistory;
    
    //    NSMutableArray *arrPendingTemp;
    //    NSMutableArray *arrApproveTemp;
    //    NSMutableArray *arrRejectTemp;
    
    int intPageNoPending;
    int intPageNoApprove;
    int intPageNoReject;
    
    __weak IBOutlet UIView *viewMonthYearCal;
    __weak IBOutlet UIPickerView *pickerViewMonthYear;
    NSMutableArray *arrMonth;
    NSMutableArray *arrYear;
    NSString *strSelectedMonthYear;
    __weak IBOutlet UIButton *btnPending;
    __weak IBOutlet UIButton *btnApproved;
    __weak IBOutlet UIButton *btnRejected;
    int intSelectedBtnTag;
    IBOutlet UIView *viewForRejection;
    __weak IBOutlet UITextView *txtViewReason;
    __weak IBOutlet UISearchBar *searchBarLifting;
}

@end

@implementation DealerLiftingHistoryVC

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

- (void)designView {
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
    
    UIBarButtonItem *barBtnAssignRssd = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemAdd target:self action:@selector(btnAssignRssdClicked:)];
    barBtnAssignRssd.tintColor = [UIColor whiteColor];
    
//    if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"dealer"] || [[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]){
//        self.navigationItem.rightBarButtonItems = @[barButtonFilter, barBtnAssignRssd];
//    }else{
//        self.navigationItem.rightBarButtonItem = barButtonFilter;
//    }
    
    if (checkUserType(kDealer) == true || checkUserType(kbroker) == true){
        self.navigationItem.rightBarButtonItems = @[barButtonFilter, barBtnAssignRssd];
    }else{
        self.navigationItem.rightBarButtonItem = barButtonFilter;
    }
    
    [tblViewLiftingHistory registerNib:[UINib nibWithNibName:@"DealerLiftingHistoryCell" bundle:nil] forCellReuseIdentifier:@"cellDealerLiftingHistory"];
    tblViewLiftingHistory.separatorColor = [UIColor clearColor];
}

- (void)loadData {
    
    intPageNoPending = 1;
    intPageNoApprove = 1;
    intPageNoReject = 1;
    
    arrPendingLiftingHistory = [[NSMutableArray alloc] init];
    arrApproveLiftingHistory = [[NSMutableArray alloc] init];
    arrRejectLiftingHistory = [[NSMutableArray alloc] init];
    
    //    arrPendingTemp = [[NSMutableArray alloc] init];
    //    arrApproveTemp = [[NSMutableArray alloc] init];
    //    arrRejectTemp = [[NSMutableArray alloc] init];
    
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

#pragma mark - UISearchBar Delegate

- (void)searchBar:(UISearchBar *)searchBar textDidChange:(NSString *)searchText{
    
    /*
     NSPredicate *bPredicate = [NSPredicate predicateWithFormat:@"SELF.product_name contains[cd] %@",searchText];
     
     if (intSelectedBtnTag == 101) {
     if (searchText.length != 0) {
     arrPendingLiftingHistory = [[arrPendingTemp filteredArrayUsingPredicate:bPredicate] mutableCopy];
     AppLog(@"HERE %@",arrPendingLiftingHistory);
     }else{
     arrPendingLiftingHistory = [arrPendingTemp mutableCopy];
     }
     }else if (intSelectedBtnTag == 102){
     if (searchText.length != 0) {
     arrApproveLiftingHistory = [[arrApproveTemp filteredArrayUsingPredicate:bPredicate] mutableCopy];
     AppLog(@"HERE %@",arrApproveLiftingHistory);
     }else{
     arrApproveLiftingHistory = [arrApproveTemp mutableCopy];
     }
     }else {
     if (searchText.length != 0) {
     arrRejectLiftingHistory = [[arrRejectTemp filteredArrayUsingPredicate:bPredicate] mutableCopy];
     AppLog(@"HERE %@",arrRejectLiftingHistory);
     }else{
     arrRejectLiftingHistory = [arrRejectTemp mutableCopy];
     }
     }*/
    
    if ([searchText isEqualToString:@""]) {
        if (intSelectedBtnTag == 101) {
            intPageNoPending = 1;
            [arrPendingLiftingHistory removeAllObjects];
            [self wsPendingLiftingHistory:searchText];
        }else if (intSelectedBtnTag == 102){
            intPageNoApprove = 1;
            [arrApproveLiftingHistory removeAllObjects];
            [self wsApproveLiftingHistory:searchText];
        }else {
            intPageNoReject = 1;
            [arrRejectLiftingHistory removeAllObjects];
            [self wsRejectPendingLiftingHistory:searchText];
        }
    }
}

- (void)searchBarSearchButtonClicked:(UISearchBar *)searchBar {
    if (intSelectedBtnTag == 101) {
        intPageNoPending = 1;
        [arrPendingLiftingHistory removeAllObjects];
        [self wsPendingLiftingHistory:searchBar.text];
    }else if (intSelectedBtnTag == 102){
        intPageNoApprove = 1;
        [arrApproveLiftingHistory removeAllObjects];
        [self wsApproveLiftingHistory:searchBar.text];
    }else {
        intPageNoReject = 1;
        [arrRejectLiftingHistory removeAllObjects];
        [self wsRejectPendingLiftingHistory:searchBar.text];
    }
    [self.view endEditing:true];
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

-(void)wsPendingLiftingHistory:(NSString*)strSubDelaerName{
    
    if (APP_DELEGATE.isServerReachable) {
        
        //NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        //[dict setValue:[defaults valueForKey:@"selected_cust_code"] forKey:@"dealer_cust_code"];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"dealer_cust_code"];
        [dict setValue:strSelectedMonthYear forKey:@"year_month"];
        [dict setValue:[NSString stringWithFormat:@"%d",intPageNoPending] forKey:@"page_no"];
        [dict setValue:strSubDelaerName forKey:@"search_sub_dealer_name"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_show_pending_lifting_history_for_dealer.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    [arrPendingLiftingHistory addObjectsFromArray:[dictResponse valueForKey:@"lifting_history_data"]];
                    //[arrPendingTemp addObjectsFromArray:[dictResponse valueForKey:@"lifting_history_data"]];
                    intPageNoPending += 1;
                    [tblViewLiftingHistory reloadData];
                }else{
                    if (intPageNoPending == 1) {
                        [arrPendingLiftingHistory removeAllObjects];
                        [tblViewLiftingHistory reloadData];
                    }
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                }
                
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)wsApproveLiftingHistory:(NSString*)strSubDelaerName {
    if (APP_DELEGATE.isServerReachable) {
        
        //NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        //[dict setValue:[defaults valueForKey:@"selected_cust_code"] forKey:@"dealer_cust_code"];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"dealer_cust_code"];
        [dict setValue:strSelectedMonthYear forKey:@"year_month"];
        [dict setValue:[NSString stringWithFormat:@"%d",intPageNoApprove] forKey:@"page_no"];
        [dict setValue:strSubDelaerName forKey:@"search_sub_dealer_name"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_show_approve_lifting_history_for_dealer.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    [arrApproveLiftingHistory addObjectsFromArray:[dictResponse valueForKey:@"lifting_history_data"]];
                    //[arrApproveTemp addObjectsFromArray:[dictResponse valueForKey:@"lifting_history_data"]];
                    intPageNoApprove += 1;
                    [tblViewLiftingHistory reloadData];
                }else{
                    if (intPageNoApprove == 1) {
                        [arrApproveLiftingHistory removeAllObjects];
                        [tblViewLiftingHistory reloadData];
                    }
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                }
                
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)wsRejectPendingLiftingHistory:(NSString*)strSubDelaerName {
    if (APP_DELEGATE.isServerReachable) {
        
        //NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        //[dict setValue:[defaults valueForKey:@"selected_cust_code"] forKey:@"dealer_cust_code"];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"dealer_cust_code"];
        [dict setValue:strSelectedMonthYear forKey:@"year_month"];
        [dict setValue:[NSString stringWithFormat:@"%d",intPageNoReject] forKey:@"page_no"];
        [dict setValue:strSubDelaerName forKey:@"search_sub_dealer_name"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_show_reject_lifting_history_for_dealer.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    [arrRejectLiftingHistory addObjectsFromArray:[dictResponse valueForKey:@"lifting_history_data"]];
                    //[arrRejectTemp addObjectsFromArray:[dictResponse valueForKey:@"lifting_history_data"]];
                    intPageNoReject += 1;
                    [tblViewLiftingHistory reloadData];
                    
                }else{
                    if (intPageNoReject == 1) {
                        [arrRejectLiftingHistory removeAllObjects];
                        [tblViewLiftingHistory reloadData];
                    }
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                }
                
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)wsUpdateLiftingStatus:(NSString*)strStatus LiftingId:(NSString*)strLiftingId RejectionMsg:(NSString*)strRejectionMsg {
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[defaults valueForKey:@"emp_code"] forKey:@"dealer_cust_code"];
        [dict setValue:strLiftingId forKey:@"the_lifting_id"];
        [dict setValue:strStatus forKey:@"the_status"];
        [dict setValue:strRejectionMsg forKey:@"reason_for_rejection"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_update_lifting_status_by_dealer.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    
                    intPageNoPending = 1;
                    intPageNoApprove = 1;
                    intPageNoReject = 1;
                    
                    [arrPendingLiftingHistory removeAllObjects];
                    [arrApproveLiftingHistory removeAllObjects];
                    [arrRejectLiftingHistory removeAllObjects];
                    
                    //                    [arrPendingTemp removeAllObjects];
                    //                    [arrApproveTemp removeAllObjects];
                    //                    [arrRejectTemp removeAllObjects];
                    
                    [self btnStatusClicked:btnPending];
                    
                }else{
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                }
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - IBAction's

- (IBAction)btnStatusClicked:(UIButton *)sender {
    NSArray *arrSegment = @[btnPending, btnApproved, btnRejected];
    for (UIButton *btnSegment in arrSegment) {
        [btnSegment setSelected:NO];
    }
    [sender setSelected:YES];
    intSelectedBtnTag = (int)sender.tag;
    
    if (intSelectedBtnTag == 101) {
        if (!arrPendingLiftingHistory.count) {
            [self wsPendingLiftingHistory:searchBarLifting.text];
        }
    }else if (intSelectedBtnTag == 102){
        if (!arrApproveLiftingHistory.count) {
            [self wsApproveLiftingHistory:searchBarLifting.text];
        }
    }else {
        if (!arrRejectLiftingHistory.count) {
            [self wsRejectPendingLiftingHistory:searchBarLifting.text];
        }
    }
    [tblViewLiftingHistory reloadData];
}

-(void)btnBackClicked:(UIButton*)btn{
    DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
    [self.navigationController pushViewController:dvc animated:NO];
    //[self.navigationController popViewControllerAnimated:true];
}

- (void)btnFilterClicked:(UIBarButtonItem *)sender {
    viewMonthYearCal.hidden = false;
}

- (void)btnAssignRssdClicked:(UIBarButtonItem *)sender {
    [self performSegueWithIdentifier:@"dealerLiftingToAssignRssd" sender:self];
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
        //[arrPendingTemp removeAllObjects];
        intPageNoPending = 1;
        [self wsPendingLiftingHistory:searchBarLifting.text];
    }else if (intSelectedBtnTag == 102){
        [arrApproveLiftingHistory removeAllObjects];
        //[arrApproveTemp removeAllObjects];
        intPageNoApprove = 1;
        [self wsApproveLiftingHistory:searchBarLifting.text];
    }else {
        [arrRejectLiftingHistory removeAllObjects];
        //[arrRejectTemp removeAllObjects];
        intPageNoReject = 1;
        [self wsRejectPendingLiftingHistory:searchBarLifting.text];
    }
}

- (IBAction)btnSubmitClicked:(UIButton *)sender {
    NSDictionary *dict = viewForRejection.accessibilityElements[0];
    txtViewReason.text = ([txtViewReason.text isEqualToString:@"Write your reason..."]) ? @"" : txtViewReason.text;
    NSString *strComment = [txtViewReason.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
    
    if ([strComment isEqualToString:@""]) {
        UDShowToastAlertWithTitle(@"Please write reason of rejection.", 2);
        return;
    }
    [self wsUpdateLiftingStatus:@"REJECTED" LiftingId:[dict safeValueeForKey:@"lid"] RejectionMsg:strComment];
    txtViewReason.text = @"";
    [sender.superview.superview removeFromSuperview];
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

- (DealerLiftingHistoryCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    static NSString *cellIdentifier = @"cellDealerLiftingHistory";
    DealerLiftingHistoryCell *cell = [tblViewLiftingHistory dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    
    NSDictionary *dict;
    if (intSelectedBtnTag == 101) {
        dict = arrPendingLiftingHistory[indexPath.row];
        cell.btnReject.accessibilityElements = @[dict];
    }else if (intSelectedBtnTag == 102){
        dict = arrApproveLiftingHistory[indexPath.row];
    } else {
        dict = arrRejectLiftingHistory[indexPath.row];
    }
    
    cell.lblSubDealerName.text  = [NSString stringWithFormat:@"Sub Dealer/RSSD Name : %@",[dict safeValueeForKey:@"sub_dealer_rssd_name"]];
    cell.lblProdName.text       = [NSString stringWithFormat:@"Product Name : %@",[dict safeValueeForKey:@"product_name"]];
    cell.lblQty.text            = [NSString stringWithFormat:@"Quantity in Bags : %@",[dict safeValueeForKey:@"qty_in_bags"]];
    cell.lblDate.text           = [NSString stringWithFormat:@"Date of Lifting : %@",[dict safeValueeForKey:@"date_of_lifting_show"]];
    cell.lblChallanNumber.text  = [NSString stringWithFormat:@"Challan Number : %@",[dict safeValueeForKey:@"challan_number"]];
    cell.lblStatus.text         = [NSString stringWithFormat:@"Status : %@",[dict safeValueeForKey:@"status"]];
    cell.lblReasonForRejection.text  = [NSString stringWithFormat:@"Reason for Rejection : %@",[dict safeValueeForKey:@"reason_for_rejection"]];
    
    cell.btnReject.tag = indexPath.row;
    cell.btnApprove.tag = indexPath.row;
    
    [cell.btnReject addTarget:self action:@selector(btnRejectClicked:) forControlEvents:UIControlEventTouchUpInside];
    [cell.btnApprove addTarget:self action:@selector(btnApproveClicked:) forControlEvents:UIControlEventTouchUpInside];
    
    if ([[dict safeValueeForKey:@"status"] isEqualToString:@"PENDING"]) {
        
        cell.constraintBtnRejectHeight.constant = 30;
        cell.constraintBtnApproveHeight.constant = 30;
        
        cell.btnApprove.hidden = false;
        cell.btnReject.hidden = false;
        
        cell.constraintRejectionDateHeight.constant = 0;
        cell.constraintReasonForRejectionHeight.constant = 0;
        
    }else if ([[dict safeValueeForKey:@"status"] isEqualToString:@"APPROVED"]){
        
        cell.constraintBtnRejectHeight.constant = 0;
        cell.constraintBtnApproveHeight.constant = 0;
        
        cell.btnApprove.hidden = true;
        cell.btnReject.hidden = true;
        
        cell.lblRejectionDate.text = [NSString stringWithFormat:@"Approved Date : %@",[dict safeValueeForKey:@"approved_rejection_date_show"]];
        
        cell.constraintRejectionDateHeight.constant = 20;
        cell.constraintReasonForRejectionHeight.constant = 0;
        
    }else {
        
        cell.constraintBtnRejectHeight.constant = 0;
        cell.constraintBtnApproveHeight.constant = 0;
        
        cell.btnApprove.hidden = true;
        cell.btnReject.hidden = true;
        
        cell.lblRejectionDate.text = [NSString stringWithFormat:@"Rejection Date : %@",[dict safeValueeForKey:@"approved_rejection_date_show"]];
        
        cell.constraintRejectionDateHeight.constant = 20;
        cell.constraintReasonForRejectionHeight.constant = 20;
    }
    
    
    return cell;
}

- (void)scrollViewDidEndDragging:(UIScrollView *)scrollView willDecelerate:(BOOL)decelerate {
    CGFloat currentOffset = scrollView.contentOffset.y;
    CGFloat maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height;
    
    if (maximumOffset - currentOffset <= 10.0) {
        if (intSelectedBtnTag == 101) {
            [self wsPendingLiftingHistory:searchBarLifting.text];
        }else if (intSelectedBtnTag == 102){
            [self wsApproveLiftingHistory:searchBarLifting.text];
        }else {
            [self wsRejectPendingLiftingHistory:searchBarLifting.text];
        }
    }
}

//- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath {
//    if (intSelectedBtnTag == 101) {
//        return 184.0f;
//    } else {
//        return 136.0f;
//    }
//}

- (void)scrollViewDidScroll:(UIScrollView *)scrollView {
    viewMonthYearCal.hidden = true;
    [self.view endEditing:true];
}

#pragma mark - Cell Action

-(void)btnRejectClicked:(UIButton*)btnReject {
    AppLog(@"-->>%ld",(long)btnReject.tag);
    viewForRejection.frame = CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, APP_CONSTANTS.fltAppHeight);
    viewForRejection.accessibilityElements = @[btnReject.accessibilityElements[0]];
    [self.view.window addSubview:viewForRejection];
}

-(void)btnApproveClicked:(UIButton*)btnApprove {
    AppLog(@"-->>%ld",(long)btnApprove.tag);
    NSDictionary *dict = arrPendingLiftingHistory[btnApprove.tag];
    [self wsUpdateLiftingStatus:@"APPROVED" LiftingId:[dict safeValueeForKey:@"lid"] RejectionMsg:@""];
}

#pragma mark - Gesture

- (IBAction)hideWriteReview:(UITapGestureRecognizer *)sender {
    [sender.view removeFromSuperview];
}

- (BOOL)gestureRecognizer:(UIGestureRecognizer *)gestureRecognizer shouldReceiveTouch:(UITouch *)touch {
    return touch.view == gestureRecognizer.view;
}

#pragma mark - UITextView Delegate

- (BOOL) textViewShouldBeginEditing:(UITextView *)textView {
    textView.text = @"";
    textView.textColor = [UIColor blackColor];
    return YES;
}

-(void) textViewDidChange:(UITextView *)textView {
    if(textView.text.length == 0) {
        textView.textColor = [UIColor lightGrayColor];
        textView.text = @"Write your reason...";
        [textView resignFirstResponder];
    }
}

-(BOOL) textViewShouldEndEditing:(UITextView *)textView {
    if(textView.text.length == 0) {
        textView.textColor = [UIColor lightGrayColor];
        textView.text = @"Write your reason...";
        [textView resignFirstResponder];
    }
    return YES;
}

- (BOOL)textView:(UITextView *)textView shouldChangeTextInRange:(NSRange)range replacementText:(NSString *)text {
    if([text isEqualToString:@"\n"]) {
        [textView resignFirstResponder];
        return NO;
    }
    return YES;
}

@end
