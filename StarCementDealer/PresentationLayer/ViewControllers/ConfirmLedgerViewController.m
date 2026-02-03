//
//  ConfirmLedgerViewController.m
//  StarCementDealer
//
//  Created by Apple on 09/04/19.
//  Copyright © 2019 Coral . All rights reserved.
//

#import "ConfirmLedgerViewController.h"
#import "LedgerBalanceCell.h"

@interface ConfirmLedgerViewController ()<UITableViewDelegate, UITableViewDataSource, UITextViewDelegate>{
    NSDictionary *dictLedger;
    IBOutlet UIView *viewWriteReview;
    __weak IBOutlet UITableView *tblViewLedger;
    __weak IBOutlet UITextView *txtViewRemarks;
}

@end

@implementation ConfirmLedgerViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView {
    [tblViewLedger registerNib:[UINib nibWithNibName:@"LedgerBalanceCell" bundle:nil] forCellReuseIdentifier:@"cell"];
}

-(void)loadData {
    [self getMonthWiseLedger];
}

#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return [[dictLedger allKeys] count];
}

- (LedgerBalanceCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *cellIdentifier = @"cell";
    LedgerBalanceCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    NSDictionary *dict = [dictLedger valueForKey:[[dictLedger allKeys] objectAtIndex:indexPath.row]];
    cell.lblDate.text = [dict safeValueeForKey:@"ledger_of"];
    cell.lblAmountDR.text = [[dict safeValueeForKey:@"total_amount_dr"] stringValue];
    cell.lblAmountCR.text = [[dict safeValueeForKey:@"total_amount_cr"] stringValue];
    
    [cell.btnConfirm addTarget:self action:@selector(btnConfirmClicked:) forControlEvents:UIControlEventTouchUpInside];
    cell.btnConfirm.accessibilityElements = @[dict];
    [cell.btnReject addTarget:self action:@selector(btnRejectClicked:) forControlEvents:UIControlEventTouchUpInside];
    cell.btnReject.accessibilityElements = @[dict];
    return cell;
}

#pragma mark - Cell Action

-(void)btnConfirmClicked:(UIButton*)btnConfirm {
    AppLog(@"%@",btnConfirm.accessibilityElements[0]);
    //REJECTED/APPROVED
    [self updateThisMonthLedger:btnConfirm.accessibilityElements[0] status:@"APPROVED" comment:@""];
}

-(void)btnRejectClicked:(UIButton*)btnReject {
    AppLog(@"%@",btnReject.accessibilityElements[0]);
    viewWriteReview.frame = CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, APP_CONSTANTS.fltAppHeight);
    viewWriteReview.accessibilityElements = @[btnReject.accessibilityElements[0]];
    [self.view.window addSubview:viewWriteReview];
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
        textView.text = @"Write your remarks";
        [textView resignFirstResponder];
    }
}

-(BOOL) textViewShouldEndEditing:(UITextView *)textView {
    if(textView.text.length == 0) {
        textView.textColor = [UIColor lightGrayColor];
        textView.text = @"Write your remarks";
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

#pragma mark - Web Service

-(void)getMonthWiseLedger{
    if (APP_DELEGATE.isServerReachable) {
        
        //NSString *strCustCode;
//        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
//        FMDatabase *db = [FMDatabase databaseWithPath:path];
//        if ([db open]) {
//            FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
//            while ([s next]) {
//                strCustCode = [s stringForColumn:@"customer_code"];
//            }
//            [db close];
//        }
        
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
//        }else{
//            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
//            FMDatabase *db = [FMDatabase databaseWithPath:path];
//            if ([db open]) {
//                FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
//                while ([s next]) {
//                    strCustCode = [s stringForColumn:@"customer_code"];
//                }
//                [db close];
//            }
//        }
        
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
//        }else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"sub dealer"]){
//            strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
//        }else{
//            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
//            FMDatabase *db = [FMDatabase databaseWithPath:path];
//            if ([db open]) {
//                FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
//                while ([s next]) {
//                    strCustCode = [s stringForColumn:@"customer_code"];
//                }
//                [db close];
//            }
//        }
        
        //strCustCode = @"C/0002199"; // Remove this line
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_show_month_wize_ledger_by_id.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    dictLedger = [dictResponse safeValueeForKey:@"check_ledger_data"];
                    [tblViewLedger reloadData];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)updateThisMonthLedger:(NSDictionary*)dictData status:(NSString*)strStatus comment:(NSString*)strComment{
    if (APP_DELEGATE.isServerReachable) {
        
        //        {
        //            "customer_code": "C\/0008012",
        //            "dns_customer_code": "Z004",
        //            "total_amount_dr": 0,
        //            "total_amount_cr": 1800,
        //            "ledger_month_year": "02-2019",
        //            "ledger_year_month_day": "2019-02-1",
        //            "ledger_of": "Feb,2019"
        //        }
        
        //customer_code,dns_customer_code,total_amount_dr,total_amount_cr,status,ledger_year_month_day
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[dictData safeValueeForKey:@"customer_code"]                 forKey:@"customer_code"];
        [dict setValue:[dictData safeValueeForKey:@"dns_customer_code"]             forKey:@"dns_customer_code"];
        [dict setValue:[[dictData safeValueeForKey:@"total_amount_dr"] stringValue] forKey:@"total_amount_dr"];
        [dict setValue:[[dictData safeValueeForKey:@"total_amount_cr"] stringValue] forKey:@"total_amount_cr"];
        [dict setValue:strStatus                                                    forKey:@"status"];
        [dict setValue:[dictData safeValueeForKey:@"ledger_year_month_day"]         forKey:@"ledger_year_month_day"];
        [dict setValue:strComment                                                   forKey:@"comment"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_update_this_month_ledger_status.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                    [self.navigationController popViewControllerAnimated:YES];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)btnUpdateRemarksClicked:(UIButton *)sender {
    AppLog(@"-->>%@",viewWriteReview.accessibilityElements[0]);
    txtViewRemarks.text = ([txtViewRemarks.text isEqualToString:@"Write your remarks"]) ? @"" : txtViewRemarks.text;
    AppLog(@"-->>%@",txtViewRemarks.text);
    NSString *strComment = [txtViewRemarks.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
    [self updateThisMonthLedger:viewWriteReview.accessibilityElements[0] status:@"REJECTED" comment:strComment];
    [sender.superview.superview removeFromSuperview];
}

#pragma mark - Gesture

- (IBAction)hideWriteReview:(UITapGestureRecognizer *)sender {
    [sender.view removeFromSuperview];
}

- (BOOL)gestureRecognizer:(UIGestureRecognizer *)gestureRecognizer shouldReceiveTouch:(UITouch *)touch {
    return touch.view == gestureRecognizer.view;
}


@end
