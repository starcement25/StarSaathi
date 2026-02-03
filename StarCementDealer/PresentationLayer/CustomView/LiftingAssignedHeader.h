//
//  LiftingAssignedHeader.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 07/06/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface LiftingAssignedHeader : UITableViewHeaderFooterView
@property (weak, nonatomic) IBOutlet UILabel *lblOrderId;
@property (weak, nonatomic) IBOutlet UILabel *lblStatus;
@property (weak, nonatomic) IBOutlet UILabel *lblProdName;
@property (weak, nonatomic) IBOutlet UILabel *lblOrderDate;
@property (weak, nonatomic) IBOutlet UILabel *lblDestination;
@property (weak, nonatomic) IBOutlet UILabel *lblFreight;
@property (weak, nonatomic) IBOutlet UILabel *lblQty;
@property (weak, nonatomic) IBOutlet UIButton *btnAllocate;

@end

NS_ASSUME_NONNULL_END
