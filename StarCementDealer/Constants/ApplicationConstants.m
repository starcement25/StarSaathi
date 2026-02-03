//
//  ApplicationConstants.m
//  EO-GOALS
//
//  Created by Coral  on 19/05/16.
//  Copyright © 2016 Coral . All rights reserved.
//

#import "ApplicationConstants.h"

@implementation ApplicationConstants

static ApplicationConstants *objApp;
static dispatch_once_t onceApplication;

- (id)init
{
    self = [super init];
    if (self) {
        self.dateFormater   = [[NSDateFormatter alloc]init];
        self.dateFormater.locale = [NSLocale localeWithLocaleIdentifier:@"en_US_POSIX"];
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        self.db = [FMDatabase databaseWithPath:path];     
        //self.strDeviceId = [[[UIDevice currentDevice] identifierForVendor] UUIDString];
    }
    return self;
}

+(ApplicationConstants *)sharedInstance
{
    dispatch_once(&onceApplication, ^{
        objApp = [[ApplicationConstants alloc]init];
    });
    return objApp;
}

/** Clear the singletone class             */
+(void)clearInstance
{
    objApp = nil;
    onceApplication = 0;
}

#pragma mark - Customize UI
/** Customize label                         */
UIKIT_EXTERN UILabel *UDcreateLabel(CGRect rect ,NSString *strTitle, UIFont *font)
{
    UILabel *lblCustom = [[UILabel alloc]initWithFrame:rect];
    lblCustom.backgroundColor = [UIColor clearColor];
    lblCustom.font = font;
    lblCustom.numberOfLines = 0;
    lblCustom.textAlignment = NSTextAlignmentLeft;
    lblCustom.textColor = [UIColor blackColor];
    lblCustom.lineBreakMode = NSLineBreakByTruncatingTail;
    lblCustom.text = strTitle;
    return lblCustom;
}
/** Customize ImageView                     */
UIKIT_EXTERN UIImageView * UDcreateImageView(CGRect rect, NSString *strImage)
{
    UIImageView *imageVieCustom =[[UIImageView alloc]initWithFrame:rect];
    imageVieCustom.backgroundColor =[UIColor clearColor];
    if(strImage != nil)
        imageVieCustom.image = BUNDLE_IMAGE(strImage);
    return imageVieCustom;
}
+(void)setDateFormatOfApp:(NSString *)str
{
    APP_CONSTANTS.dateFormater.locale = [NSLocale localeWithLocaleIdentifier:@"en_US_POSIX"];
    [APP_CONSTANTS.dateFormater setDateFormat:str];
}
/** Customize Textfield                 */
UIKIT_EXTERN UITextField *UDcreateTextField(CGRect rect ,NSString *strPlaceHolder)
{
    UITextField *textFieldCustom =[[UITextField alloc]initWithFrame:rect];
    textFieldCustom.backgroundColor = [UIColor clearColor];
    textFieldCustom.placeholder = strPlaceHolder;
    textFieldCustom.clearButtonMode = UITextFieldViewModeWhileEditing;
    textFieldCustom.textAlignment = NSTextAlignmentLeft;
    textFieldCustom.textColor = [UIColor blackColor];
    textFieldCustom.font = Font_Bold(14.0f);
    textFieldCustom.contentVerticalAlignment = UIControlContentVerticalAlignmentCenter;
    return textFieldCustom;
}
/** Call Alertview                      */
UIKIT_EXTERN UIAlertView * UDShowAlertWithTitle(NSString *strTitle , NSString *strMessage)
{
    UIAlertView *alertValidation = [[UIAlertView alloc] initWithTitle:strTitle message:strMessage delegate:nil cancelButtonTitle:@"Ok" otherButtonTitles:nil];
    [alertValidation performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
    return alertValidation;
}
/** Call Toast Alertview                 */
UIKIT_EXTERN void UDShowToastAlertWithTitle(NSString *strMessage , int duration)
{
    UIAlertView *toast = [[UIAlertView alloc] initWithTitle:nil message:strMessage
                                                   delegate:nil cancelButtonTitle:nil otherButtonTitles:nil, nil];
    [toast performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
    dispatch_after(dispatch_time(DISPATCH_TIME_NOW,duration * NSEC_PER_SEC), dispatch_get_main_queue(), ^{
        [toast dismissWithClickedButtonIndex:0 animated:YES];
    });
}

UIKIT_EXTERN CGSize getDynamicHeight(UIFont *font, NSString *strText, CGSize sizeMax)
{
    return [strText boundingRectWithSize:sizeMax options:NSStringDrawingUsesFontLeading|NSStringDrawingUsesLineFragmentOrigin
                              attributes:@{NSFontAttributeName : font,NSParagraphStyleAttributeName:
                                               [NSParagraphStyle defaultParagraphStyle]} context:nil].size;
}
UIKIT_EXTERN void roundedImageView(UIImageView *imgProfilePic)
{
    UIBezierPath* ovalPath = [UIBezierPath bezierPathWithRoundedRect:imgProfilePic.bounds byRoundingCorners:UIRectCornerAllCorners cornerRadii:CGSizeMake((imgProfilePic.frame.size.width/2), (imgProfilePic.frame.size.height/2))];
    CAShapeLayer *maskLayer = [[CAShapeLayer alloc] init];
    maskLayer.frame = imgProfilePic.bounds;
    maskLayer.path = ovalPath.CGPath;
    imgProfilePic.layer.mask = maskLayer;
}
/** Customize Button                    */
UIKIT_EXTERN UIButton *UDcreateButton(CGRect rect, NSString *strImage)
{
    UIButton *btnCustom =[UIButton buttonWithType:UIButtonTypeCustom];
    btnCustom.frame = rect;
    btnCustom.backgroundColor = [UIColor clearColor];
    if(strImage.length)
        [btnCustom setImage: BUNDLE_IMAGE(strImage)forState:UIControlStateNormal];
    return btnCustom;
}


@end
