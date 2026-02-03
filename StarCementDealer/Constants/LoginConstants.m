//
//  LoginConstants.m
//  EO-GOALS
//
//  Created by Coral  on 19/05/16.
//  Copyright © 2016 Coral . All rights reserved.
//

#import "LoginConstants.h"

static LoginConstants *objLoginConstants = nil;
static dispatch_once_t onceToken = 0;

@implementation LoginConstants

+(LoginConstants *)sharedLoginConstants
{
    dispatch_once(&onceToken, ^{
        objLoginConstants = [[LoginConstants alloc]init];
    });
    return objLoginConstants;
}

+(void)clearLoginConstants
{
    objLoginConstants = nil;
    onceToken         = 0;
};

@end
