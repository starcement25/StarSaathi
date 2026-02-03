//
//  LedgerViewController.m
//  StarCementDealer
//

#import "LedgerViewController.h"
#import "LedgerCell.h"
#import "UIAlertView+Category.h"
#include <CommonCrypto/CommonDigest.h>
#import "WebViewController.h"
#import "LedgerStatementViewController.h"
#import <SVProgressHUD/SVProgressHUD.h>

@interface LedgerViewController () <UITableViewDelegate, UITableViewDataSource, UITextFieldDelegate> {
    
    __weak IBOutlet UITableView *tblViewLedger;
    __weak IBOutlet UILabel *lblRupees;
    __weak IBOutlet UILabel *lblLastTransactionCount;
    __weak IBOutlet UIButton *btnConfirmBal;
    __weak IBOutlet UIButton *btnMakePayment;
    NSArray *arrLedgerData;
    NSDictionary *dictLedger;
    NSString *strBalance;
    NSString *strPaymentBy;
    IBOutlet UIView *viewBaseMakePayment;
    __weak IBOutlet UIView *viewPayNow;
    __weak IBOutlet UITextField *txtFieldAmount;
    __weak IBOutlet UITextField *txtFieldPayableAmount;
    __weak IBOutlet UITextField *txtFieldStartDate;
    __weak IBOutlet UITextField *txtFieldEndDate;
    IBOutletCollection(UIButton) NSArray *btnCollPayOption;
    __weak IBOutlet UIView *viewDateSelection;
    UITextField *txtFieldActive;
}

@end

@implementation LedgerViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

- (void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [self.navigationController setNavigationBarHidden:NO animated:animated];
    self.navigationItem.hidesBackButton = YES;
}

#pragma mark - Initialization

-(void)designView{
    
    viewDateSelection.hidden = true;
    
    [tblViewLedger registerNib:[UINib nibWithNibName:@"LedgerCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewLedger.separatorColor = [UIColor clearColor];
    tblViewLedger.tableFooterView = [UIView new];
    
    UIView *viewRightBarButton = [[UIView alloc]initWithFrame:CGRectMake(0, 0, 40, 40)];
    UIImageView *imgViewDetails = [[UIImageView alloc]initWithFrame:CGRectMake(0, 0, 40, 22)];
    imgViewDetails.contentMode = UIViewContentModeScaleAspectFit;
    [imgViewDetails setImage:[UIImage imageNamed:@"details"]];
    UILabel *lblDetails = UDcreateLabel(CGRectMake(0, 22, 40, 18), @"Details", [UIFont systemFontOfSize:10]);
    lblDetails.textColor = [UIColor whiteColor];
    lblDetails.textAlignment = NSTextAlignmentCenter;
    
    [viewRightBarButton addSubview:imgViewDetails];
    [viewRightBarButton addSubview:lblDetails];
    
    UIBarButtonItem *barBtnRight = [[UIBarButtonItem alloc]initWithCustomView:viewRightBarButton];
    self.navigationItem.rightBarButtonItem = barBtnRight;
    
    UITapGestureRecognizer *tapGesture = [[UITapGestureRecognizer alloc]initWithTarget:self action:@selector(btnDetailsClicked:)];
    [viewRightBarButton addGestureRecognizer:tapGesture];
    
    UIToolbar *toolbar = [[UIToolbar alloc] initWithFrame:CGRectZero];
    toolbar.barStyle = UIBarStyleDefault;
    [toolbar sizeToFit];
    
    UIBarButtonItem *flexSpace = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:nil action:nil];
    UIBarButtonItem *doneBtn = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone target:self action:@selector(resignPicker)];
    [toolbar setItems:@[flexSpace, doneBtn] animated:YES];
    
    UIDatePicker *datePickerStartDate = [[UIDatePicker alloc]init];
    if (@available(iOS 13.4, *)) {
        datePickerStartDate.preferredDatePickerStyle = UIDatePickerStyleWheels;
    }
    [datePickerStartDate setDate:[NSDate date]];
    datePickerStartDate.datePickerMode = UIDatePickerModeDate;
    datePickerStartDate.maximumDate = [NSDate date];
    [datePickerStartDate addTarget:self action:@selector(setDate:) forControlEvents:UIControlEventValueChanged];
    
    UIDatePicker *datePickerEndDate = [[UIDatePicker alloc]init];
    if (@available(iOS 13.4, *)) {
        datePickerEndDate.preferredDatePickerStyle = UIDatePickerStyleWheels;
    }
    [datePickerEndDate setDate:[NSDate date]];
    datePickerEndDate.datePickerMode = UIDatePickerModeDate;
    datePickerEndDate.maximumDate = [NSDate date];
    [datePickerEndDate addTarget:self action:@selector(setDate:) forControlEvents:UIControlEventValueChanged];
    
    txtFieldStartDate.inputView = datePickerStartDate;
    txtFieldStartDate.inputAccessoryView = toolbar;
    
    txtFieldEndDate.inputView = datePickerEndDate;
    txtFieldEndDate.inputAccessoryView = toolbar;
    
    txtFieldAmount.inputAccessoryView = toolbar;
}

-(void)loadData{
    strPaymentBy = @"";
    [self getLedgerDetails];
}

#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return arrLedgerData.count;
}

- (LedgerCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    LedgerCell *cell = [tblViewLedger dequeueReusableCellWithIdentifier:@"cell" forIndexPath:indexPath];
    
    NSDictionary *dict = arrLedgerData[indexPath.row];
    cell.lblVoucherDebit.text   = convertDateFormat1([dict safeValueeForKey:@"voucher_date"]);
    cell.lblVoucherNo.text      = [dict safeValueeForKey:@"voucher_no"];
    cell.lblQuantity.text       = [dict safeValueeForKey:@"quantity"];
    cell.lblAmountDr.text       = [NSString stringWithFormat:@"₹ %@",[dict safeValueeForKey:@"amount_dr"]];
    cell.lblAmountCr.text       = [NSString stringWithFormat:@"₹ %@",[dict safeValueeForKey:@"amount_cr"]];
    
    cell.btnNarration.accessibilityHint = [dict safeValueeForKey:@"narration"];
    [cell.btnNarration addTarget:self action:@selector(btnNarrationClicked:) forControlEvents:UIControlEventTouchUpInside];
    
    return  cell;
}

-(void)btnNarrationClicked:(UIButton*)btn{
    UDShowAlertWithTitle(nil, btn.accessibilityHint);
}

#pragma mark - UITextField Delegate

- (void)textFieldDidBeginEditing:(UITextField *)textField {
    txtFieldActive = textField;
}

- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    return [textField resignFirstResponder];
}

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
    
    NSString *strEnteredAmt = [textField.text stringByReplacingCharactersInRange:range withString:string];
    for (UIButton *btnPaymentOption in btnCollPayOption) {
        if (btnPaymentOption.selected) {
            if ([btnPaymentOption.titleLabel.text isEqualToString:@"Credit Card"]) {
                float fltVal = [strEnteredAmt floatValue] * 0.95;
                fltVal = fltVal / 100.0;
                txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",[strEnteredAmt floatValue] + fltVal];
            } else if ([btnPaymentOption.titleLabel.text isEqualToString:@"Net Banking"]){
                float fltVal = [strEnteredAmt floatValue] + 10.0 ;
                txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",fltVal];
            } else if ([btnPaymentOption.titleLabel.text isEqualToString:@"UPI"]){
                txtFieldPayableAmount.text = strEnteredAmt;
            } else if ([btnPaymentOption.titleLabel.text isEqualToString:@"Debit Card"]){
                float fltVal = [strEnteredAmt floatValue];
                if (fltVal <= 2000) {
                    txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",fltVal];
                } else {
                    fltVal = fltVal * 0.85 ;
                    fltVal = fltVal / 100.0;
                    txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",[strEnteredAmt floatValue] + fltVal];
                }
            }
        }
    }
    return YES;
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

- (void)btnDetailsClicked:(UITapGestureRecognizer *)gesture {
    NSString *strUrl = @"https://starsaathi.com/SAP/dashboard/";
    [[UIApplication sharedApplication] openURL:[NSURL URLWithString:strUrl] options:@{} completionHandler:nil];
}

- (IBAction)btnConfirmBalanceClicked:(UIButton *)sender {
    // Your existing code
}

- (IBAction)btnMakePaymentClicked:(UIButton *)sender {
    if ([APP_DELEGATE.strBranchWisePGRollOut isEqualToString:@"ACTIVE"]) {
        [self performSegueWithIdentifier:@"ledgerToMakePayment" sender:self];
    } else {
        UDShowAlertWithTitle(kAppName, @"Coming Soon");
    }
}

- (IBAction)btnCloseMakePaymentView:(UIButton *)sender {
    [sender.superview.superview removeFromSuperview];
}

- (IBAction)btnPaynowClicked:(UIButton *)sender {
    if (!APP_DELEGATE.isServerReachable) {
        UDShowToastAlertWithTitle(kNoInternet, 2);
        return;
    }
    
    if (![[txtFieldAmount.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] length] || !strPaymentBy.length) {
        UDShowToastAlertWithTitle(@"Please enter amount", 2);
        return;
    }
    
    NSString *strAmt = txtFieldPayableAmount.text;
    NSMutableDictionary *dictMakeOrder = [[NSMutableDictionary alloc]init];
    [dictMakeOrder setValue:APP_CONSTANTS.strCustCode forKey:@"customer_code"];
    [dictMakeOrder setValue:strAmt forKey:@"the_amount"];
    [dictMakeOrder setValue:strPaymentBy forKey:@"payment_by"];
    
    [SVProgressHUD show];
    [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_pay_ledger_amount.php" parameters:dictMakeOrder completion:^(NSDictionary *dictResponse){
        dispatch_async(dispatch_get_main_queue(), ^{
            [SVProgressHUD dismiss];
            if ([[dictResponse safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
                NSString *strOrderId = [dictResponse safeValueeForKey:@"the_order_id"];
                [self performSegueWithIdentifier:@"ledgerToWebview" sender:strOrderId];
            } else {
                UDShowToastAlertWithTitle([dictResponse safeValueeForKey:@"process_message"], 2);
            }
        });
    }];
}

- (IBAction)btnPaymentOptionClicked:(UIButton *)sender {
    for (UIButton *btnPaymentMode in btnCollPayOption) {
        [btnPaymentMode setSelected:NO];
    }
    [sender setSelected:YES];
    
    if ([sender.titleLabel.text isEqualToString:@"Credit Card"]) {
        strPaymentBy = @"creditcard";
    } else if ([sender.titleLabel.text isEqualToString:@"Net Banking"]) {
        strPaymentBy = @"netbanking";
    } else if ([sender.titleLabel.text isEqualToString:@"UPI"]) {
        strPaymentBy = @"upi";
    } else if ([sender.titleLabel.text isEqualToString:@"Debit Card"]) {
        strPaymentBy = @"debitcard";
    }
    
    // Recalculate payable amount
    [self textField:txtFieldAmount shouldChangeCharactersInRange:NSMakeRange(0, txtFieldAmount.text.length) replacementString:txtFieldAmount.text];
}

- (IBAction)btnDownloadStatementClicked:(UIButton *)sender {
    viewDateSelection.hidden = !viewDateSelection.hidden;
}

- (IBAction)btnProceedClicked:(UIButton *)sender {
    
    if ([txtFieldStartDate.text isEqualToString:@"00-00-0000"]) {
        UDShowToastAlertWithTitle(@"Please select start date", 2);
        return;
    } else if ([txtFieldEndDate.text isEqualToString:@"00-00-0000"]) {
        UDShowToastAlertWithTitle(@"Please select end date", 2);
        return;
    }
    
    [APP_CONSTANTS.dateFormater setDateFormat:@"MMM dd, yyyy"];
    NSDate *dateStart = [APP_CONSTANTS.dateFormater dateFromString:txtFieldStartDate.text];
    NSDate *dateEnd = [APP_CONSTANTS.dateFormater dateFromString:txtFieldEndDate.text];
    if ([dateStart compare:dateEnd] == NSOrderedDescending) {
        UDShowToastAlertWithTitle(@"End date cannot be prior to start date", 2);
        return;
    }
    
    [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
    NSString *strStartDate = [APP_CONSTANTS.dateFormater stringFromDate:dateStart];
    NSString *strEndDate = [APP_CONSTANTS.dateFormater stringFromDate:dateEnd];
    NSString *strUserType = [[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"];
    
    NSString *strUrl = [NSString stringWithFormat:@"https://starsaathi.com/SAP/dashboard/detailed_statement_pdf_download.php?the_start_date=%@&the_end_date=%@&the_customer_code=%@&user_type=%@",strStartDate, strEndDate, APP_CONSTANTS.strCustCode, strUserType];
    
    // Perform segue to LedgerStatementViewController
    [self performSegueWithIdentifier:@"ledgerToDetailedStatement" sender:strUrl];
}

#pragma mark - Web Service Method

-(void)getLedgerDetails{
    if (!APP_DELEGATE.isServerReachable) {
        UDShowToastAlertWithTitle(kNoInternet, 2);
        return;
    }
    
    NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
    [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
    
    [SVProgressHUD showWithStatus:@"Loading..."];
    [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_ledger_by_id.php" parameters:dict completion:^(NSDictionary *dictResponse){
        dispatch_async(dispatch_get_main_queue(), ^{
            [SVProgressHUD dismiss];
            if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                dictLedger = dictResponse;
                [self populateData:dictResponse];
            } else {
                UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            }
        });
    }];
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"ledgerToWebview"]) {
        WebViewController *wvc = segue.destinationViewController;
        NSString *strOrderId = sender;
        wvc.strWeblink = [NSString stringWithFormat:@"https://starsaathi.com/SAP/ccavenue_pg/make_ledger_payment_with_ccavenue.php?order_id=%@",strOrderId];
        wvc.strTitle = @"Ledger";
    } else if ([segue.identifier isEqualToString:@"ledgerToDetailedStatement"]) {
        LedgerStatementViewController *lsvc = segue.destinationViewController;
        lsvc.strWeblink = sender;
    }
}

#pragma mark - Set Data Method

-(void)populateData:(NSDictionary*)dictResponse {
    arrLedgerData = [dictResponse safeValueeForKey:@"ledger_data"];
    strBalance = [[dictResponse safeValueeForKey:@"ledger_balance_data"] safeValueeForKey:@"balance"];
    lblRupees.text = [NSString stringWithFormat:@"₹ %@", [[dictResponse safeValueeForKey:@"ledger_balance_data"] safeValueeForKey:@"balance"]];
    lblLastTransactionCount.text = [NSString stringWithFormat:@"*LAST 50 TRANSACTIONS AS ON %@", convertDateFormat1([[dictResponse safeValueeForKey:@"ledger_balance_data"] safeValueeForKey:@"date"])];
    [tblViewLedger reloadData];
}

#pragma mark - Picker Helper

-(void)resignPicker{
    [self.view endEditing:YES];
}

- (NSString *)formatDate:(NSDate *)date {
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateFormat:@"MMM dd, yyyy"];
    return [dateFormatter stringFromDate:date];
}

-(void)setDate:(UIDatePicker*)picker {
    txtFieldActive.text = [self formatDate:picker.date];
}

@end
