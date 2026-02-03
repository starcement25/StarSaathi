//
//  PopOrderHistoryCell.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 23/09/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface PopOrderHistoryCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UIView *viewBase;
@property (weak, nonatomic) IBOutlet UIImageView *imgViewProd;
@property (weak, nonatomic) IBOutlet UILabel *lblMainOrderId;
@property (weak, nonatomic) IBOutlet UILabel *lblOrderId;
@property (weak, nonatomic) IBOutlet UILabel *lblDate;
@property (weak, nonatomic) IBOutlet UILabel *lblProd;
@property (weak, nonatomic) IBOutlet UILabel *lblQty;
@property (weak, nonatomic) IBOutlet UILabel *lblTotalAmt;
@property (weak, nonatomic) IBOutlet UILabel *lblAddress;
@property (weak, nonatomic) IBOutlet UILabel *lblOrderStatus;

@end

NS_ASSUME_NONNULL_END
