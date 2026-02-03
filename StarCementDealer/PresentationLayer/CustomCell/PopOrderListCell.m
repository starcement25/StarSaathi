//
//  PopOrderListCell.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 20/09/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "PopOrderListCell.h"

@implementation PopOrderListCell

- (void)awakeFromNib {
    [super awakeFromNib];
    
    self.viewBaseImage.layer.cornerRadius = 5.0;
    self.viewBaseImage.layer.masksToBounds = false;
    
    self.viewBaseImage.layer.shadowRadius = 2.0f;
    self.viewBaseImage.layer.shadowOffset = CGSizeMake(0, 0);
    self.viewBaseImage.layer.shadowColor = [UIColor blackColor].CGColor;
    self.viewBaseImage.layer.shadowOpacity = 0.2;
}

@end
