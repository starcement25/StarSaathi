//
//  RssdLedgerViewController.m
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 23/02/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "RssdLedgerViewController.h"
#import "RssdLedgerCell.h"
#import "UIAlertView+Category.h"
#include <CommonCrypto/CommonDigest.h>
#import "WebViewController.h"

@interface RssdLedgerViewController ()<UITableViewDelegate, UITableViewDataSource, UITextFieldDelegate>{
    
    __weak IBOutlet UITableView *tblViewLedger;
    __weak IBOutlet UILabel *lblRupees;
    __weak IBOutlet UILabel *lblLastTransactionCount;
    __weak IBOutlet UIButton *btnConfirmBal;
    __weak IBOutlet UIButton *btnMakePayment;
    NSArray *arrLedgerData;
    NSDictionary *dictLedger;
    //NSString *strBalance;
    //NSString *strCustCode;
    NSString *strPaymentBy;
    IBOutlet UIView *viewBaseMakePayment;
    __weak IBOutlet UIView *viewPayNow;
    __weak IBOutlet UITextField *txtFieldAmount;
    __weak IBOutlet UITextField *txtFieldPayableAmount;
    IBOutletCollection(UIButton) NSArray *btnCollPayOption;
    NSUserDefaults *defaults;
}


@end

@implementation RssdLedgerViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    defaults = [NSUserDefaults standardUserDefaults];
    [self designView];
    [self loadData];
}

- (void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [self.navigationController setNavigationBarHidden:NO animated:animated];
    self.navigationItem.hidesBackButton = YES;
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView{
    
    [tblViewLedger registerNib:[UINib nibWithNibName:@"RssdLedgerCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewLedger.separatorColor = [UIColor clearColor];
    tblViewLedger.tableFooterView = [UIView new];
    
    UIToolbar *toolbar = [[UIToolbar alloc] initWithFrame:CGRectZero];
    toolbar.barStyle = UIBarStyleDefault;
    [toolbar sizeToFit];
    
    UIBarButtonItem *flexSpace = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:self action:nil];
    
    UIBarButtonItem *doneBtn = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone target:self action:@selector(resignPicker)];
    
    [toolbar setItems:@[flexSpace, doneBtn] animated:YES];
    
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

- (RssdLedgerCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    
    static NSString *cellIdentifier =  @"cell";
    RssdLedgerCell *cell = [tblViewLedger dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    NSDictionary *dict = arrLedgerData[indexPath.row];
    
    AppLog(@"-->>%@",convertDateFormat2([dict safeValueeForKey:@"voucher_date"]));
    AppLog(@"-->>%@",convertDateFormat2([dict safeValueeForKey:@"entry_date"]));
    
    cell.lblVoucherDate.text = convertDateFormat2([dict safeValueeForKey:@"voucher_date"]);
    cell.lblVoucherNo.text   = [dict safeValueeForKey:@"voucher_no"];
    cell.lblEntryDate.text   = convertDateFormat2([dict safeValueeForKey:@"entry_date"]);
    cell.lblAmount.text      = [NSString stringWithFormat:@"₹ %@",[dict safeValueeForKey:@"amount"]];
    
    cell.btnNarration.hidden = ([[dict safeValueeForKey:@"narration"] isEqualToString:@""]) ? true : false;
    cell.btnNarration.accessibilityHint = [dict safeValueeForKey:@"narration"];
    [cell.btnNarration addTarget:self action:@selector(btnNarrationClicked:) forControlEvents:UIControlEventTouchUpInside];
    
    return  cell;
}

-(void)btnNarrationClicked:(UIButton*)btn{
    UDShowAlertWithTitle(nil, btn.accessibilityHint);
}

#pragma mark - UITextField Delegate

- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    return [textField resignFirstResponder];
}

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
    
    NSString *strEnteredAmt = [textField.text stringByReplacingCharactersInRange:range withString:string];
    for (UIButton *btnPaymentOption in btnCollPayOption) {
        if (btnPaymentOption.selected) {
            if ([btnPaymentOption.titleLabel.text isEqualToString:@"Credit Card"]) {
                float fltVal = [strEnteredAmt floatValue] * 0.95 ;
                fltVal = fltVal / 100.0;
                txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",[strEnteredAmt floatValue] + fltVal];
            }else if ([btnPaymentOption.titleLabel.text isEqualToString:@"Net Banking"]){
                float fltVal = [strEnteredAmt floatValue] + 10.0 ;
                txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",fltVal];
            }else if ([btnPaymentOption.titleLabel.text isEqualToString:@"UPI"]){
                txtFieldPayableAmount.text = strEnteredAmt;
            }else if ([btnPaymentOption.titleLabel.text isEqualToString:@"Debit Card"]){
                //                float fltVal = [strEnteredAmt floatValue] * 0.85 ;
                //                fltVal = fltVal / 100.0;
                //                txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",[strEnteredAmt floatValue] + fltVal];
                
                
                float fltVal = [strEnteredAmt floatValue];
                if (fltVal <= 2000) {
                    txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",fltVal];
                }else{
                    fltVal = fltVal * 0.85 ;
                    fltVal = fltVal / 100.0;
                    txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",[strEnteredAmt floatValue] + fltVal];
                }
                
                
            }
        }
    }
    return true;
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

- (void)btnDetailsClicked:(UITapGestureRecognizer *)gesture {
    NSString *strUrl = [[dictLedger safeValueeForKey:@"ledger_balance_data"] safeValueeForKey:@"link"];
    [[UIApplication sharedApplication] openURL:[NSURL URLWithString:strUrl] options:@{} completionHandler:nil];
}

- (IBAction)btnConfirmBalanceClicked:(UIButton *)sender {
    
}

- (IBAction)btnMakePaymentClicked:(UIButton *)sender {
    
    if ([APP_DELEGATE.strBranchWisePGRollOut isEqualToString:@"ACTIVE"]) {
        [self performSegueWithIdentifier:@"ledgerToMakePayment" sender:self];
    }else
        UDShowAlertWithTitle(kAppName, @"Coming Soon");
    
    /*
     txtFieldAmount.text = @"";
     txtFieldPayableAmount.text = @"";
     
     for (UIButton *btn in btnCollPayOption) {
     [btn setSelected:false];
     }
     
     CGRect navbarFrame = self.navigationController.navigationBar.frame;
     //float topWidth  = navbarFrame.size.width;
     float topHeight = navbarFrame.size.height + navbarFrame.origin.y;
     
     viewBaseMakePayment.frame = CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, APP_CONSTANTS.fltAppHeight - topHeight);
     [self.view addSubview:viewBaseMakePayment];
     */
}

- (IBAction)btnCloseMakePaymentView:(UIButton *)sender {
    [sender.superview.superview removeFromSuperview];
}

- (IBAction)btnPaynowClicked:(UIButton *)sender {
    
    
    
    if (APP_DELEGATE.isServerReachable) {
        //[PMBO.strQuantity stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
        if (![[txtFieldAmount.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] length]) {
            UDShowToastAlertWithTitle(@"Please enter amount", 2);
            return;
        }else if (![strPaymentBy length]){
            UDShowToastAlertWithTitle(@"Please enter amount", 2);
            return;
        }
        
        NSString *strAmt = txtFieldPayableAmount.text;
        
        NSDictionary *dictMakeOrder = [[NSMutableDictionary alloc]init];
        [dictMakeOrder setValue:APP_CONSTANTS.strCustCode   forKey:@"customer_code"];
        [dictMakeOrder setValue:strAmt                      forKey:@"the_amount"];
        [dictMakeOrder setValue:strPaymentBy                forKey:@"payment_by"];
        
        [SVProgressHUD show];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_pay_ledger_amount.php" parameters:dictMakeOrder completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
                    NSString *strOrderId = [dictResponse safeValueeForKey:@"the_order_id"];
                    [self performSegueWithIdentifier:@"ledgerToWebview" sender:strOrderId];
                }else
                    UDShowToastAlertWithTitle([dictResponse safeValueeForKey:@"process_message"], 2);
                
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
}

- (IBAction)btnPaymentOptionClicked:(UIButton *)sender {
    for (UIButton *btnPaymentMode in btnCollPayOption) {
        [btnPaymentMode setSelected:false];
    }
    [sender setSelected:true];
    
    if ([sender.titleLabel.text isEqualToString:@"Credit Card"]) {
        strPaymentBy = @"creditcard";
        float fltVal = [txtFieldAmount.text floatValue] * 0.95 ;
        fltVal = fltVal / 100.0;
        txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",[txtFieldAmount.text floatValue] + fltVal];
    }else if ([sender.titleLabel.text isEqualToString:@"Net Banking"]){
        strPaymentBy = @"netbanking";
        float fltVal = [txtFieldAmount.text floatValue] + 10.0 ;
        txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",fltVal];
    }else if ([sender.titleLabel.text isEqualToString:@"UPI"]){
        strPaymentBy = @"upi";
        txtFieldPayableAmount.text = txtFieldAmount.text;
    }else if ([sender.titleLabel.text isEqualToString:@"Debit Card"]){
        strPaymentBy = @"debitcard";
        float fltVal = [txtFieldAmount.text floatValue];
        if (fltVal <= 2000) {
            txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",fltVal];
        }else{
            fltVal = fltVal * 0.85 ;
            fltVal = fltVal / 100.0;
            txtFieldPayableAmount.text = [NSString stringWithFormat:@"%.2f",[txtFieldAmount.text floatValue] + fltVal];
        }
        
    }
}

#pragma mark - Web Service Method

-(void)getLedgerDetails{
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
        [dict setValue:[defaults valueForKey:@"user_type"] forKey:@"user_type"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_ledger_subdealer_rssd.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    dictLedger = dictResponse;
                    [self populateData:dictResponse];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"ledgerToWebview"]) {
        WebViewController *wvc = segue.destinationViewController;
        NSString *strOrderId = sender;
        wvc.strWeblink = [NSString stringWithFormat:@"https://starsaathi.com/SAP/ccavenue_pg/make_ledger_payment_with_ccavenue.php?order_id=%@",strOrderId];
        wvc.strTitle = @"Ledger";
    }
}

#pragma mark - Set Data Method

-(void)populateData:(NSDictionary*)dictResponse {
    
    arrLedgerData = [dictResponse safeValueeForKey:@"ledger_data"];
    
    APP_CONSTANTS.dateFormater.dateFormat = @"MM/dd/yyyy";
    
    NSMutableArray *arrTemp = [NSMutableArray new];
    for(NSInteger i=0; i<arrLedgerData.count; i++){
        NSMutableDictionary *dic =[[NSMutableDictionary alloc] initWithDictionary:[arrLedgerData objectAtIndex:i]];
        NSDate *dateConverted = [APP_CONSTANTS.dateFormater dateFromString:[dic valueForKey:@"voucher_date"]];
        [dic setValue:dateConverted forKey:@"date"];
        [arrTemp addObject:dic];
    }
    
    NSSortDescriptor *descriptor=[[NSSortDescriptor alloc] initWithKey:@"date" ascending:NO];
    NSArray *descriptors=[NSArray arrayWithObject: descriptor];
    NSArray *sortedArray=[arrTemp sortedArrayUsingDescriptors:descriptors];
    
    arrLedgerData = sortedArray;
    lblRupees.text = [NSString stringWithFormat:@"₹ %@",[dictResponse safeValueeForKey:@"balance"]];
    [tblViewLedger reloadData];
    
}

#pragma mark - Helper Method

- (BOOL)hasTopNotch {
    if (@available(iOS 13.0, *)) {
        return [self keyWindow].safeAreaInsets.top > 20.0;
    }else{
        return [[[UIApplication sharedApplication] delegate] window].safeAreaInsets.top > 20.0;
    }
    return  NO;
}

- (UIWindow*)keyWindow {
    UIWindow        *foundWindow = nil;
    NSArray         *windows = [[UIApplication sharedApplication]windows];
    for (UIWindow   *window in windows) {
        if (window.isKeyWindow) {
            foundWindow = window;
            break;
        }
    }
    return foundWindow;
}

-(void)resignPicker{
    [self.view endEditing:YES];
}

@end

