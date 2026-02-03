//
//  DealerVisitListCell.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 15/01/25.
//  Copyright © 2025 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface DealerVisitListCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblEmpName;
@property (weak, nonatomic) IBOutlet UILabel *lblDatetime;

@end

NS_ASSUME_NONNULL_END
