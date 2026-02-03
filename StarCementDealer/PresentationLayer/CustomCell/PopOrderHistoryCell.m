//
//  PopOrderHistoryCell.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 23/09/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "PopOrderHistoryCell.h"

@implementation PopOrderHistoryCell

- (void)awakeFromNib {
    [super awakeFromNib];
    self.viewBase.layer.cornerRadius = 5.0;
    self.viewBase.layer.masksToBounds = false;
    
    self.viewBase.layer.shadowRadius = 2.0f;
    self.viewBase.layer.shadowOffset = CGSizeMake(0, 0);
    self.viewBase.layer.shadowColor = [UIColor blackColor].CGColor;
    self.viewBase.layer.shadowOpacity = 0.2;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
