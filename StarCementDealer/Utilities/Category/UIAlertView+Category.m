//
//  UIAlertView+Category.m
//  GroupTaskManagement
//


#import "UIAlertView+Category.h"
#import <objc/runtime.h>

@implementation UIAlertView (Category)



-(void)setAlertRespond:(void (^)(NSInteger index))alertRespo
{
    objc_setAssociatedObject(self, @selector(alertRespond), alertRespo, OBJC_ASSOCIATION_RETAIN_NONATOMIC);
}

-(void (^)(NSInteger index))alertRespond
{
  return  objc_getAssociatedObject(self, @selector(alertRespond));
   // //(self, @selector(alertRespond), alertRespond, OBJC_ASSOCIATION_RETAIN_NONATOMIC);
}

@end
