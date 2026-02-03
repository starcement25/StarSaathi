//
//  CustomTabButton.h
//  PagerWithTab
//
//  Created by Forcepower Infotech Pvt Ltd on 19/06/24.
//

#import <WebKit/WebKit.h>

@interface CustomTabButton : UIButton

@property (strong, nonatomic) UIView *bottomStrip;

- (void)setSelected:(BOOL)selected;

@end
