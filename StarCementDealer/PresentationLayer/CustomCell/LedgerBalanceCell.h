//
//  LedgerBalanceCell.h
//  StarCementDealer
//
//  Created by Apple on 09/04/19.
//  Copyright © 2019 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@interface LedgerBalanceCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblDate;
@property (weak, nonatomic) IBOutlet UILabel *lblAmountDR;
@property (weak, nonatomic) IBOutlet UILabel *lblAmountCR;
@property (weak, nonatomic) IBOutlet UIButton *btnConfirm;
@property (weak, nonatomic) IBOutlet UIButton *btnReject;

@end
