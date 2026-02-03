//
//  PopOrderPaymentVC.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 22/09/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "PopOrderPaymentVC.h"
#import "PopDeliveryAddVC.h"

@interface PopOrderPaymentVC (){
    __weak IBOutlet UITextField *txtFieldAmount;
    IBOutletCollection(UIButton) NSArray *btnCollPayOption;
    NSString *strPaymentBy;
}


@end

@implementation PopOrderPaymentVC

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
    strPaymentBy = @"netbanking";
    txtFieldAmount.text = self.strTotalAmt;
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:true];
}

- (IBAction)btnContinueClicked:(id)sender {
    if (APP_DELEGATE.isServerReachable) {
        if (![strPaymentBy length]){
            UDShowToastAlertWithTitle(@"Please select any payment method", 2);
            return;
        }
        [self performSegueWithIdentifier:@"popPaymentToDeliveryAddress" sender:self];
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
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"popPaymentToDeliveryAddress"]) {
        PopDeliveryAddVC *pdavc = segue.destinationViewController;
        pdavc.arrPickedItems = self.arrPickedItems;
        pdavc.strPaymentBy = strPaymentBy;
        pdavc.flagForPG = true;
    }
}


@end
