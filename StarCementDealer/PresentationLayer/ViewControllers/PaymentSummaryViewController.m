//
//  PaymentSummaryViewController.m
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 23/11/20.
//  Copyright © 2020 Coral . All rights reserved.
//

#import "PaymentSummaryViewController.h"
#import "WebViewController.h"

@interface PaymentSummaryViewController (){
    
    __weak IBOutlet UILabel *lblAmount;
    __weak IBOutlet UILabel *lblProcFee;
    __weak IBOutlet UILabel *lblGst;
    __weak IBOutlet UILabel *lblTotalAmt;
    
    float fltTotalPayableAmt;
}

@end

@implementation PaymentSummaryViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    
}

-(void)loadData {
    
    float fltAmount = [self.strAmount floatValue];
    float fltProcFee = 00.00;
    float fltGst = 00.00;
    
    if ([_strPaymentMethod isEqualToString:@"creditcard"]) {
        
        fltProcFee = fltAmount * 0.95 ;
        fltProcFee = fltProcFee / 100.0;
        fltGst = (fltProcFee * 18) / 100;
        
    }else if ([_strPaymentMethod isEqualToString:@"netbanking"]){
        
        fltProcFee = 10.00;
        fltGst = (fltProcFee * 18) / 100;
        
    }else if ([_strPaymentMethod isEqualToString:@"upi"]){
        
        fltProcFee = 00.00;
        fltGst = 00.00;
        
    }else if ([_strPaymentMethod isEqualToString:@"debitcard"]){
        
        float fltVal = [_strAmount floatValue];
        if (fltVal <= 2000) {
            fltProcFee = 00.00;
            fltGst = 00.00;
        }else{
            fltProcFee = fltAmount * 0.85 ;
            fltProcFee = fltProcFee / 100.0;
            fltGst = (fltProcFee * 18) / 100;
        }
    }
    
    
    lblAmount.text = [NSString stringWithFormat:@"Rs. %.2f",fltAmount];
    lblProcFee.text = [NSString stringWithFormat:@"Rs. %.2f",fltProcFee];
    lblGst.text = [NSString stringWithFormat:@"Rs. %.2f",fltGst];
    
    fltTotalPayableAmt = fltAmount + fltProcFee + fltGst;
    
    lblTotalAmt.text = [NSString stringWithFormat:@"Rs. %.2f",fltTotalPayableAmt];
    
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:true];
}

- (IBAction)btnProceedClicked:(UIButton *)sender {
    
    if (APP_DELEGATE.isServerReachable) {
        
//        NSString *strCustCode;
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
        
//        NSString *strCustCode;
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
        
        NSDictionary *dictMakeOrder = [[NSMutableDictionary alloc]init];
        [dictMakeOrder setValue:APP_CONSTANTS.strCustCode forKey:@"customer_code"];
        [dictMakeOrder setValue:[NSString stringWithFormat:@"%.2f",fltTotalPayableAmt] forKey:@"the_amount"];
        [dictMakeOrder setValue:_strPaymentMethod forKey:@"payment_by"];
        
        [SVProgressHUD show];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_pay_ledger_amount_v2.php" parameters:dictMakeOrder completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
                    NSString *strPaymentLink = [dictResponse safeValueeForKey:@"the_payment_link"];
                    [self performSegueWithIdentifier:@"paymentSummaryToWebview" sender:strPaymentLink];
                }else
                    UDShowToastAlertWithTitle([dictResponse safeValueeForKey:@"process_message"], 2);
                
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"paymentSummaryToWebview"]) {
        WebViewController *wvc = segue.destinationViewController;
        NSString *strPaymentLink = (NSString*)sender;
        wvc.strWeblink = strPaymentLink;
        //wvc.strWeblink = [NSString stringWithFormat:@"https://starsaathi.com/SAP/ccavenue_pg/make_ledger_payment_with_ccavenue.php?order_id=%@",strOrderId];
        wvc.strTitle = @"Ledger";
        wvc.title = @"PAYMENT";
    }
}

@end
