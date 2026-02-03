//
//  CALayer+XibConfiguration.m
//  GarbageCollector
//
//  Created by Coral  on 26/08/16.
//  Copyright © 2016 Coral . All rights reserved.
//

#import "CALayer+XibConfiguration.h"

@implementation CALayer (XibConfiguration)

-(void)setBorderUIColor:(UIColor*)color
{
    self.borderColor = color.CGColor;
}

-(UIColor*)borderUIColor
{
    return [UIColor colorWithCGColor:self.borderColor];
}

@end
