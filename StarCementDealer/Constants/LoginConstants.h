//
//  LoginConstants.h
//  EO-GOALS
//
//  Created by Coral  on 19/05/16.
//  Copyright © 2016 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>
#import "UserInfoBO.h"

@interface LoginConstants : NSObject

@property (strong, nonatomic) UserInfoBO       *objUserInfo;
@property (assign, nonatomic) long long        intSessionID;

+(LoginConstants *)sharedLoginConstants;
+(void)clearLoginConstants;

@end

NS_INLINE LoginConstants  *LOGIN_CONSTANTS (){return [LoginConstants sharedLoginConstants];}
