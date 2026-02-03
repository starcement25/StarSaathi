//
//  LiftingAssignedCell.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 07/06/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface LiftingAssignedCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblChallanNo;
@property (weak, nonatomic) IBOutlet UILabel *lblDate;
@property (weak, nonatomic) IBOutlet UILabel *lblQty;
@property (weak, nonatomic) IBOutlet UIButton *btnAllocate;

@end

NS_ASSUME_NONNULL_END
