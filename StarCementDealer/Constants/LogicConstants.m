//
//  LogicConstants.m
//  EO-GOALS
//
//  Created by Coral  on 19/05/16.
//  Copyright © 2016 Coral . All rights reserved.
//

#import "LogicConstants.h"

FOUNDATION_EXTERN BOOL validateWebsite(NSString *strWeb)
{
    NSString *theURL = @"[\\w\\d\\-_]+\\.\\w{2,3}(\\.\\w{2})?(/(?<=/)(?:[\\w\\d\\-./_]+)?)?";
    NSString *theURLW = @"((?:http|https)://)?(?:www\\.)?[\\w\\d\\-_]+\\.\\w{2,3}(\\.\\w{2})?(/(?<=/)(?:[\\w\\d\\-./_]+)?)?";
    NSPredicate *urlTest    = [NSPredicate predicateWithFormat:@"SELF MATCHES %@ || SELF MATCHES %@", theURL, theURLW];
    return ([urlTest evaluateWithObject:strWeb]);// || [urlTestW evaluateWithObject:strWeb]);
}

FOUNDATION_EXTERN BOOL validateEmail(NSString *email)
{
    NSString *emailRegex = @"[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}";
    NSPredicate *emailTest = [NSPredicate predicateWithFormat:@"SELF MATCHES %@", emailRegex];
    return [emailTest evaluateWithObject:email];
}

FOUNDATION_EXTERN BOOL validateCharacterOfEmail(NSString *strEmail)
{
    NSString *nameEx =@"[A-Za-z0-9@$&*._]";
    NSPredicate *nameExPredicate = [NSPredicate predicateWithFormat:@"self matches[cd] %@", nameEx];
    return [nameExPredicate evaluateWithObject:strEmail];
}

FOUNDATION_EXTERN BOOL validateNumber(NSString *strMobile)
{
    NSString *regexString  = @"^[6-9]\\d{9}$";
    NSPredicate *pred = [NSPredicate predicateWithFormat:@"self matches[cd] %@", regexString];
    return [pred evaluateWithObject:strMobile];
}



FOUNDATION_EXTERN NSString* convertDateFormat(NSString *strDate)
{
    APP_CONSTANTS.dateFormater.dateFormat = @"yyyy-MM-dd";
    NSDate *date = [APP_CONSTANTS.dateFormater dateFromString:strDate];
    // Convert date object into desired format
    [APP_CONSTANTS.dateFormater setDateFormat:@"dd/MMM/yyyy"];
    //return  [APP_CONSTANTS.dateFormater stringFromDate:date];
    id idValue = [APP_CONSTANTS.dateFormater stringFromDate:date];
    return (idValue == [NSNull null]) ? idValue : strDate;
}

FOUNDATION_EXTERN NSString* convertDateFormat1(NSString *strDate)
{
    APP_CONSTANTS.dateFormater.dateFormat = @"MM/dd/yyyy hh:mm:ss aaa";
    NSDate *date = [APP_CONSTANTS.dateFormater dateFromString:strDate];
    // Convert date object into desired format
    [APP_CONSTANTS.dateFormater setDateFormat:@"MM/dd/yyyy"];
    //return  [APP_CONSTANTS.dateFormater stringFromDate:date];
    id idValue = [APP_CONSTANTS.dateFormater stringFromDate:date];
    return (idValue == [NSNull null]) ? idValue : strDate;
}

FOUNDATION_EXTERN NSString* convertDateFormat2(NSString *strDate)
{
    APP_CONSTANTS.dateFormater.dateFormat = @"MM/dd/yyyy";
    NSDate *date = [APP_CONSTANTS.dateFormater dateFromString:strDate];
    // Convert date object into desired format
    [APP_CONSTANTS.dateFormater setDateFormat:@"MMM dd, yyyy"];
    //return  [APP_CONSTANTS.dateFormater stringFromDate:date];
    id idValue = [APP_CONSTANTS.dateFormater stringFromDate:date];
    return (idValue == [NSNull null]) ? strDate : idValue;
}

FOUNDATION_EXTERN NSString* convertDateFormat3(NSString *strDate)
{
    APP_CONSTANTS.dateFormater.dateFormat = @"dd-MM-yyyy";
    NSDate *date = [APP_CONSTANTS.dateFormater dateFromString:strDate];
    // Convert date object into desired format
    [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
    //return  [APP_CONSTANTS.dateFormater stringFromDate:date];
    id idValue = [APP_CONSTANTS.dateFormater stringFromDate:date];
    return (idValue == [NSNull null]) ? strDate : idValue;
}

FOUNDATION_EXTERN NSString* convertTimeFormat(NSString *strTime)
{
    APP_CONSTANTS.dateFormater.dateFormat = @"HH:mm";
    NSDate *time = [APP_CONSTANTS.dateFormater dateFromString:strTime];
    // Convert date object into desired format
    [APP_CONSTANTS.dateFormater setDateFormat:@"hh:mm a"];
    //return  [APP_CONSTANTS.dateFormater stringFromDate:time];
    id idValue = [APP_CONSTANTS.dateFormater stringFromDate:time];
    return (idValue == [NSNull null]) ? strTime : idValue;
}

FOUNDATION_EXTERN NSString*     convertTimeFormat1(NSString *strTime)
{
    APP_CONSTANTS.dateFormater.dateFormat = @"hh:mm a";
    NSDate *time = [APP_CONSTANTS.dateFormater dateFromString:strTime];
    // Convert date object into desired format
    [APP_CONSTANTS.dateFormater setDateFormat:@"hh:mm a"];
    //return  [APP_CONSTANTS.dateFormater stringFromDate:time];
    id idValue = [APP_CONSTANTS.dateFormater stringFromDate:time];
    return (idValue == [NSNull null]) ? strTime : idValue;
}

FOUNDATION_EXTERN NSString*     getTimestamp()
{
    APP_CONSTANTS.dateFormater.dateFormat = @"yyyyMMddmmss";
    return [APP_CONSTANTS.dateFormater stringFromDate:[NSDate date]];
}

FOUNDATION_EXTERN NSString*     getCurrentDateTime()
{
    APP_CONSTANTS.dateFormater.dateFormat = @"yyyy-MM-dd hh:mm:ss";
    return [APP_CONSTANTS.dateFormater stringFromDate:[NSDate date]];
}

FOUNDATION_EXTERN NSString*     getCurrentDateTime1()
{
    APP_CONSTANTS.dateFormater.dateFormat = @"yyyy-MM-dd€HH:mm:ss";
    return [APP_CONSTANTS.dateFormater stringFromDate:[NSDate date]];
}

FOUNDATION_EXTERN BOOL checkUserType(NSString *strUserType) {
    
//    if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] caseInsensitiveCompare:strUserType] == NSOrderedSame) {
//        return true;
//    } else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] caseInsensitiveCompare:strUserType] == NSOrderedSame) {
//        return true;
//    }else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] caseInsensitiveCompare:strUserType] == NSOrderedSame) {
//        return true;
//    }else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] caseInsensitiveCompare:strUserType] == NSOrderedSame) {
//        return true;
//    }else
//        return false;
    
    if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] caseInsensitiveCompare:strUserType] == NSOrderedSame) {
        return true;
    }else
        return false;
}




