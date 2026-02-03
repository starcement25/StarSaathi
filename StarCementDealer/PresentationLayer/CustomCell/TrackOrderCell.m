//
//  TrackOrderCell.m
//  StarCementDealer
//
//  Created by Coral  on 17/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "TrackOrderCell.h"

@implementation TrackOrderCell

- (void)awakeFromNib {
    [super awakeFromNib];
        
     self.selectionStyle = UITableViewCellSelectionStyleNone;
    // Initialization code
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
