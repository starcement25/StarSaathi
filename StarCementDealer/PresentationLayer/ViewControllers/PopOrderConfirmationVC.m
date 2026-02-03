//
//  PopOrderConfirmationVC.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 21/09/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "PopOrderConfirmationVC.h"
#import "PopOrderConfirmationCell.h"
#import "PopOrderPaymentVC.h"
#import "PopDeliveryAddVC.h"

@interface PopOrderConfirmationVC ()<UITableViewDelegate, UITableViewDataSource>{
    __weak IBOutlet UITableView *tblViewItems;
    __weak IBOutlet UILabel *lblTotalAmt;
    float fltTotalAmt;
}

@end

@implementation PopOrderConfirmationVC

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    tblViewItems.separatorColor = [UIColor clearColor];
    [tblViewItems registerNib:[UINib nibWithNibName:@"PopOrderConfirmationCell" bundle:nil] forCellReuseIdentifier:@"cell"];
}

-(void)loadData {
    AppLog(@"-->>%@",self.arrPickedItems);
    
    fltTotalAmt = 0.0;
    for (NSDictionary *dictItems in self.arrPickedItems) {
        float fltPricePerPiece = [[dictItems safeValueeForKey:@"price_per_piece"] floatValue];
        float fltInputQty = [[dictItems safeValueeForKey:@"input_value"] floatValue];
        float fltPrice = fltPricePerPiece * fltInputQty;
        float fltGstDec = [[dictItems safeValueeForKey:@"GST_rate"] floatValue] / 100;
        float fltGstAmt = fltPrice * fltGstDec;
        fltTotalAmt += fltPrice + fltGstAmt;
    }
    lblTotalAmt.text = [NSString stringWithFormat:@"₹ %.2f",fltTotalAmt];
    
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(id)sender {
    [self.navigationController popViewControllerAnimated:true];
}

- (IBAction)btnContinueClicked:(id)sender {
    
    // Check if any dictionary has "Y" for the key "payment_gateway"
    BOOL containsValue = arrayContainsValueForKey(_arrPickedItems, @"payment_gateway", @"Y");
    if (containsValue) {
        AppLog(@"Payment gateway.");
        [self performSegueWithIdentifier:@"popOrderConfirmToPayment" sender:self];
    } else {
        AppLog(@"Free Product.");
        [self performSegueWithIdentifier:@"popOrderConfirmToDeliveryAddress" sender:self];
    }
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"popOrderConfirmToPayment"]) {
        PopOrderPaymentVC *popvc = segue.destinationViewController;
        popvc.strTotalAmt = [NSString stringWithFormat:@"%.2f",fltTotalAmt];
        popvc.arrPickedItems = self.arrPickedItems;
    }else if ([segue.identifier isEqualToString:@"popOrderConfirmToDeliveryAddress"]) {
        PopDeliveryAddVC *pdavc = segue.destinationViewController;
        pdavc.flagForPG = false;
        pdavc.strPaymentBy = @"payment pending";
        pdavc.arrPickedItems = self.arrPickedItems;
    }
}

#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return self.arrPickedItems.count;
}

- (PopOrderConfirmationCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    static NSString *cellIdentifier = @"cell";
    PopOrderConfirmationCell *cell = [tblViewItems dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    NSDictionary *dictItems = self.arrPickedItems[indexPath.row];
    cell.lblProdDesc.text = [dictItems safeValueeForKey:@"prod_desc"];
    cell.lblQty.text = [NSString stringWithFormat:@"Qty : %@ (Pcs)",[dictItems safeValueeForKey:@"input_value"]];
    cell.lblMrp.text = [NSString stringWithFormat:@"Mrp : %@",[dictItems safeValueeForKey:@"price_per_piece"]];
    cell.lblGstPercent.text = [NSString stringWithFormat:@"Gst : %@%%",[dictItems safeValueeForKey:@"GST_rate"]];
    
    float fltPricePerPiece = [[dictItems safeValueeForKey:@"price_per_piece"] floatValue];
    float fltInputQty = [[dictItems safeValueeForKey:@"input_value"] floatValue];
    float fltPrice = fltPricePerPiece * fltInputQty;
    float fltGstDec = [[dictItems safeValueeForKey:@"GST_rate"] floatValue] / 100;
    float fltGstAmt = fltPrice * fltGstDec;
    float fltTotal = fltPrice + fltGstAmt;
    
    cell.lblPrice.text = [NSString stringWithFormat:@"Price : %.2f",fltPrice];
    cell.lblGstAmt.text = [NSString stringWithFormat:@"Gst : %.2f",fltGstAmt];
    cell.lblTotal.text = [NSString stringWithFormat:@"Total : %.2f",fltTotal];
    
    return cell;
}

#pragma mark - Helper Method

BOOL arrayContainsValueForKey(NSArray *array, NSString *key, NSString *value) {
    for (NSDictionary *dict in array) {
        if ([dict[key] isEqualToString:value]) {
            return YES; // Stop as soon as the value is found
        }
    }
    return NO;
}

@end
