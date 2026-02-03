//
//  MakePaymentViewController.m
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 20/11/20.
//  Copyright © 2020 Coral . All rights reserved.
//

#import "MakePaymentViewController.h"
#import "PaymentSummaryViewController.h"

@interface MakePaymentViewController (){
    __weak IBOutlet UITextField *txtFieldAmount;
    IBOutletCollection(UIButton) NSArray *btnCollPayOption;
    NSString *strPaymentBy;
    IBOutlet UIView *viewPaymentCharges;
}

@end

@implementation MakePaymentViewController

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
    
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:true];
}

- (IBAction)btnContinueClicked:(id)sender {
    if (APP_DELEGATE.isServerReachable) {
        
        if (![[txtFieldAmount.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] length]) {
            UDShowToastAlertWithTitle(@"Please enter amount", 2);
            return;
        }else if (![strPaymentBy length]){
            UDShowToastAlertWithTitle(@"Please select any payment method", 2);
            return;
        }
        
        [self performSegueWithIdentifier:@"makePaymentToPaymentSummary" sender:self];
        
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

- (IBAction)btnPaymentOptionClicked:(UIButton *)sender {
    for (UIButton *btnPaymentMode in btnCollPayOption) {
        [btnPaymentMode setSelected:false];
    }
    [sender setSelected:true];
    
    strPaymentBy = [[sender.titleLabel.text stringByReplacingOccurrencesOfString:@" " withString:@""] lowercaseString];
    AppLog(@"-->>%@",strPaymentBy);
    
//    if ([sender.titleLabel.text isEqualToString:@"Credit Card"]) {
//        strPaymentBy = @"creditcard";
//    }else if ([sender.titleLabel.text isEqualToString:@"Net Banking"]){
//        strPaymentBy = @"netbanking";
//    }else if ([sender.titleLabel.text isEqualToString:@"UPI"]){
//        strPaymentBy = @"upi";
//    }else if ([sender.titleLabel.text isEqualToString:@"Debit Card"]){
//        strPaymentBy = @"debitcard";
//    }
}

- (IBAction)btnClosePaymentCharges:(UIButton *)sender {
    [sender.superview.superview removeFromSuperview];
}

- (IBAction)btnPaymentInfo:(UIButton *)sender {
    //[self.view endEditing:true];
    CGRect navbarFrame = self.navigationController.navigationBar.frame;
    //float topWidth  = navbarFrame.size.width;
    float topHeight = navbarFrame.size.height + navbarFrame.origin.y;
    
    viewPaymentCharges.frame = CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, APP_CONSTANTS.fltAppHeight - topHeight);
    [self.view addSubview:viewPaymentCharges];
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"makePaymentToPaymentSummary"]) {
        PaymentSummaryViewController *psvc = [segue destinationViewController];
        psvc.strAmount = txtFieldAmount.text;
        psvc.strPaymentMethod = strPaymentBy;
    }
}


@end
