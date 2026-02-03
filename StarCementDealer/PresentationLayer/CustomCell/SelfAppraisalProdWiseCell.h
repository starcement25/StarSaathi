//
//  SelfAppraisalProdWiseCell.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 06/07/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface SelfAppraisalProdWiseCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblMonth;
@property (weak, nonatomic) IBOutlet UILabel *lblType;
@property (weak, nonatomic) IBOutlet UILabel *lblTarget;
@property (weak, nonatomic) IBOutlet UILabel *lblAchievement;
@end

NS_ASSUME_NONNULL_END
