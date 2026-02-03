//
//  UIAlertView+Category.h
//  GroupTaskManagement
//


#import <WebKit/WebKit.h>

@interface UIAlertView (Category)

@property (nonatomic ,copy) void (^alertRespond)(NSInteger index);

-(void)setAlertRespond:(void (^)(NSInteger index))alertRespond;
-(void (^)(NSInteger index))alertRespond;

@end


@implementation NSObject (AlertViewCategory)

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    if(alertView.alertRespond)
    {
        alertView.alertRespond(buttonIndex);
        alertView.alertRespond = nil;
    }
}

@end