//
//  LedgerCell.h
//  StarCementDealer
//
//  Created by Coral  on 04/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@interface LedgerCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblVoucherDebit;
@property (weak, nonatomic) IBOutlet UILabel *lblVoucherNo;
@property (weak, nonatomic) IBOutlet UILabel *lblQuantity;
@property (weak, nonatomic) IBOutlet UILabel *lblAmountDr;
@property (weak, nonatomic) IBOutlet UILabel *lblAmountCr;
@property (weak, nonatomic) IBOutlet UIButton *btnNarration;

@end
