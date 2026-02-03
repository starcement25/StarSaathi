//
//  LiftingHistoryCell.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 25/05/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "LiftingHistoryCell.h"

@implementation LiftingHistoryCell

- (void)awakeFromNib {
    [super awakeFromNib];
    // Initialization code
    self.viewBase.layer.cornerRadius = 5.0f;
    self.viewBase.layer.masksToBounds = true;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];
    // Configure the view for the selected state
}

@end
