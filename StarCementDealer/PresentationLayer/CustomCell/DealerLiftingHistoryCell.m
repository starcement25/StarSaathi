//
//  DealerLiftingHistoryCell.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 25/05/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "DealerLiftingHistoryCell.h"

@implementation DealerLiftingHistoryCell

- (void)awakeFromNib {
    [super awakeFromNib];
    // Initialization code
    
    self.viewBase.layer.cornerRadius = 5.0f;
    self.viewBase.layer.masksToBounds = true;
    
    self.btnReject.layer.cornerRadius = 5.0f;
    self.btnReject.layer.masksToBounds = true;
    self.btnReject.layer.borderColor = [UIColor lightGrayColor].CGColor;
    self.btnReject.layer.borderWidth = 0.8;
    
    self.btnApprove.layer.cornerRadius = 5.0f;
    self.btnApprove.layer.masksToBounds = true;
    self.btnApprove.layer.borderColor = [UIColor lightGrayColor].CGColor;
    self.btnApprove.layer.borderWidth = 0.8;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
