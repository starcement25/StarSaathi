//
//  CustomTabButton.m
//  PagerWithTab
//
//  Created by Forcepower Infotech Pvt Ltd on 19/06/24.
//

#import "CustomTabButton.h"

@implementation CustomTabButton

- (instancetype)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        [self setupBottomStrip];
    }
    return self;
}

- (void)setupBottomStrip {
    self.bottomStrip = [[UIView alloc] initWithFrame:CGRectMake(0, 47, self.bounds.size.width, 3)];
    self.bottomStrip.backgroundColor = [UIColor clearColor]; // Default color is clear
    [self addSubview:self.bottomStrip];
}

- (void)setSelected:(BOOL)selected {
    [super setSelected:selected];
    if (selected) {
        //self.bottomStrip.backgroundColor = [UIColor blueColor]; // Color of the strip when selected
        self.bottomStrip.backgroundColor = UIColorFromRGB(0xEC2427);
    } else {
        self.bottomStrip.backgroundColor = [UIColor clearColor]; // Color of the strip when not selected
    }
}

@end
