//
//  RssdLedgerCell.h
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 23/02/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface RssdLedgerCell : UITableViewCell

@property (weak, nonatomic) IBOutlet UILabel *lblVoucherDate;
@property (weak, nonatomic) IBOutlet UILabel *lblVoucherNo;
@property (weak, nonatomic) IBOutlet UILabel *lblEntryDate;
@property (weak, nonatomic) IBOutlet UILabel *lblAmount;
@property (weak, nonatomic) IBOutlet UIButton *btnNarration;

@end

NS_ASSUME_NONNULL_END
