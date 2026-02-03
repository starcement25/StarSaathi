//
//  CALayer+XibConfiguration.h
//  GarbageCollector
//
//  Created by Coral  on 26/08/16.
//  Copyright © 2016 Coral . All rights reserved.
//

#import <QuartzCore/QuartzCore.h>
#import <WebKit/WebKit.h>

@interface CALayer(XibConfiguration)

// This assigns a CGColor to borderColor.
@property(nonatomic, assign) UIColor* borderUIColor;

@end