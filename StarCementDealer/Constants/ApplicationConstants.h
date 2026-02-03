//
//  ApplicationConstants.h
//  EO-GOALS
//
//  Created by Coral  on 19/05/16.
//  Copyright © 2016 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>
#import "AppDelegate.h"
#import "FontAndColorConstants.h"
#import "StringConstants.h"
#import "LoginConstants.h"
#import "LogicConstants.h"
#import "FMDB.h"


#define APP_DELEGATE ((AppDelegate *)[[UIApplication sharedApplication]delegate])

#define APP_ARC_SAFE_RELEASE(xx)    if(xx)    {        xx = nil;    }

#define APP_SAFE_REMOVE(xx)         if(xx)    { [xx removeFromSuperview]; xx = nil;}

#define APP_IntToString(val)     [NSString stringWithFormat:@"%d",val]

#define DEBUG_MODE      0
#if (DEBUG_MODE)
#define AppLog(fmt, ...) NSLog((@"%s [Line %d] " fmt),__PRETTY_FUNCTION__, __LINE__, ##__VA_ARGS__)
#else
#define AppLog(x, ...)
#endif

NS_INLINE UIImage *BUNDLE_IMAGE(NSString *imageName)
{ return [UIImage imageWithContentsOfFile:[[NSBundle mainBundle]pathForResource:imageName ofType:nil]]; }

typedef NS_ENUM(NSInteger, enumDeviceType){
    deviceTypeiPhone4,
    deviceTypeiPhone5,
    deviceTypeiPhone6,
    deviceTypeiPhone6Plus,
    deviceTypeiPad
};


@interface ApplicationConstants : NSObject

@property (assign, nonatomic) CGFloat           fltStatusBarHeight;
@property (assign, nonatomic) CGFloat           fltAppHeight;
@property (assign, nonatomic) CGFloat           fltAppWidth;
@property (assign, nonatomic) CGFloat           fltExtraHeight;
@property (assign, nonatomic) CGFloat           fltExtraWidth;
@property (assign, nonatomic) BOOL              isiPad;
@property (assign, nonatomic) BOOL              isMenuVisiable;
@property (strong, nonatomic) NSDateFormatter   *dateFormater;
@property (assign, nonatomic) enumDeviceType    deviceType;
@property (strong, nonatomic) NSString          *strMonth;
@property (strong, nonatomic) NSString          *strYear;
@property (strong, nonatomic) NSString          *strStatus;
@property (strong ,nonatomic) NSString          *strCurrentVersion;
@property (strong ,nonatomic) NSString          *strOldVersion;
@property (strong ,nonatomic) NSString          *strDeviceId;
@property (strong, nonatomic) NSString          *strCustCode;
@property (strong, nonatomic) FMDatabase        *db;
@property (assign, nonatomic) float             fltAvailableAllocationQty;


UIKIT_EXTERN UILabel          * UDcreateLabel(CGRect rect ,NSString *strTitle, UIFont *font);
/** Customize ImageView             */
UIKIT_EXTERN UIImageView      * UDcreateImageView(CGRect rect, NSString *strImage);
/** Customize Textfield             */
UIKIT_EXTERN UITextField      * UDcreateTextField(CGRect rect, NSString *strPlaceHolder);
/** Customize Button                */
UIKIT_EXTERN UIButton         * UDcreateButton(CGRect rect ,NSString *strImage);
/** Call Alertview                  */
UIKIT_EXTERN UIAlertView      * UDShowAlertWithTitle(NSString *strTitle , NSString *strMessage);
UIKIT_EXTERN void               UDShowToastAlertWithTitle(NSString *strMessage , int duration);

UIKIT_EXTERN CGSize getDynamicHeight(UIFont *font, NSString *strText, CGSize sizeMax);

UIKIT_EXTERN void expandButton(UIButton *btn, float ratio);
UIKIT_EXTERN void roundedImageView(UIImageView *imgProfilePic);


/** Call Toast Alertview                    */
UIKIT_EXTERN void UDShowToastAlertWithTitle(NSString *strMessage , int duration);
/** Assign the singletone class     */
+(ApplicationConstants *)sharedInstance;
/** Clear the singletone class      */
+(void)clearInstance;

+(void)setDateFormatOfApp:(NSString *)str;

@end

#define APP_CONSTANTS ([ApplicationConstants sharedInstance])
