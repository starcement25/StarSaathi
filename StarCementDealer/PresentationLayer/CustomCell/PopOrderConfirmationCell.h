//
//  PopOrderConfirmationCell.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 22/09/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface PopOrderConfirmationCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblProdDesc;
@property (weak, nonatomic) IBOutlet UILabel *lblQty;
@property (weak, nonatomic) IBOutlet UILabel *lblMrp;
@property (weak, nonatomic) IBOutlet UILabel *lblGstPercent;
@property (weak, nonatomic) IBOutlet UILabel *lblPrice;
@property (weak, nonatomic) IBOutlet UILabel *lblGstAmt;
@property (weak, nonatomic) IBOutlet UILabel *lblTotal;

@end

NS_ASSUME_NONNULL_END
