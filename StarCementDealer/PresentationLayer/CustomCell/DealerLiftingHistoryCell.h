//
//  DealerLiftingHistoryCell.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 25/05/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface DealerLiftingHistoryCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UIView *viewBase;
@property (weak, nonatomic) IBOutlet UILabel *lblSubDealerName;
@property (weak, nonatomic) IBOutlet UILabel *lblProdName;
@property (weak, nonatomic) IBOutlet UILabel *lblQty;
@property (weak, nonatomic) IBOutlet UILabel *lblDate;
@property (weak, nonatomic) IBOutlet UILabel *lblChallanNumber;
@property (weak, nonatomic) IBOutlet UILabel *lblStatus;
@property (weak, nonatomic) IBOutlet UILabel *lblRejectionDate;
@property (weak, nonatomic) IBOutlet UILabel *lblReasonForRejection;
@property (weak, nonatomic) IBOutlet UIButton *btnReject;
@property (weak, nonatomic) IBOutlet UIButton *btnApprove;
@property (weak, nonatomic) IBOutlet NSLayoutConstraint *constraintRejectionDateHeight;
@property (weak, nonatomic) IBOutlet NSLayoutConstraint *constraintReasonForRejectionHeight;
@property (weak, nonatomic) IBOutlet NSLayoutConstraint *constraintBtnRejectHeight;
@property (weak, nonatomic) IBOutlet NSLayoutConstraint *constraintBtnApproveHeight;

@end

NS_ASSUME_NONNULL_END
