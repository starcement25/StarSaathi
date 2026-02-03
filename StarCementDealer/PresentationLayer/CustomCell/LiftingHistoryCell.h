//
//  LiftingHistoryCell.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 25/05/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface LiftingHistoryCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UIView *viewBase;
@property (weak, nonatomic) IBOutlet UILabel *lblProdName;
@property (weak, nonatomic) IBOutlet UILabel *lblQty;
@property (weak, nonatomic) IBOutlet UILabel *lblDate;
@property (weak, nonatomic) IBOutlet UILabel *lblChallanNumber;
@property (weak, nonatomic) IBOutlet UILabel *lblStatus;
@property (weak, nonatomic) IBOutlet UILabel *lblReasonForRejection;
@property (weak, nonatomic) IBOutlet UILabel *lblLinkedDealerName;
@property (weak, nonatomic) IBOutlet UILabel *lblApprovedDate;

@property (weak, nonatomic) IBOutlet NSLayoutConstraint *constraintApprovedDateHeight;
@property (weak, nonatomic) IBOutlet NSLayoutConstraint *constraintReasonForRejectionHeight;

@end

NS_ASSUME_NONNULL_END
