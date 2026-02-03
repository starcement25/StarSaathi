//
//  LogicConstants.h
//  EO-GOALS
//
//  Created by Coral  on 19/05/16.
//  Copyright © 2016 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

#pragma mark-Validation

FOUNDATION_EXTERN BOOL          validateWebsite(NSString *strWeb);
FOUNDATION_EXTERN BOOL          validateEmail(NSString *email);
FOUNDATION_EXTERN BOOL          validateCharacterOfEmail(NSString *strEmail);
FOUNDATION_EXTERN BOOL          validateNumber(NSString *strMobile);
FOUNDATION_EXTERN NSInteger     getIndex(NSString *strKey);

FOUNDATION_EXTERN NSString*     convertDateFormat(NSString *strDate);
FOUNDATION_EXTERN NSString*     convertDateFormat1(NSString *strDate);
FOUNDATION_EXTERN NSString*     convertDateFormat2(NSString *strDate);
FOUNDATION_EXTERN NSString*     convertDateFormat3(NSString *strDate);

FOUNDATION_EXTERN NSString*     convertTimeFormat(NSString *strTime);
FOUNDATION_EXTERN NSString*     convertTimeFormat1(NSString *strTime);
FOUNDATION_EXTERN NSString*     getTimestamp();
FOUNDATION_EXTERN NSString*     getCurrentDateTime();
FOUNDATION_EXTERN NSString*     getCurrentDateTime1();

FOUNDATION_EXTERN BOOL          checkUserType(NSString *strUserType);


