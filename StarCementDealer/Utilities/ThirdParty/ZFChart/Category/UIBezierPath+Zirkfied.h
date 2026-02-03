//
//  UIBezierPath+Zirkfied.h
//  ZFChartView
//
//  Created by apple on 16/4/6.
//  Copyright © 2016年 apple. All rights reserved.
//

#import <WebKit/WebKit.h>

@interface UIBezierPath (Zirkfied)

- (UIBezierPath *)smoothedPathWithGranularity:(NSInteger)granularity;

@end
