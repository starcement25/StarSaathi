//
//  NSObject+Category.m
//  LeadCapture
//


#import "NSObject+Category.h"
#import <WebKit/WebKit.h>


@implementation NSObject (Category)

-(id)safeValueeForKey:(NSString *)strKey
{    
    if([self isKindOfClass:[NSDictionary class]])
    {
        id idValue = [self valueForKey:strKey];
        if(idValue ==[NSNull null])
            return @"";
        return idValue;
    }
    else if([self isKindOfClass:[NSArray class]])
     return @"";

    return @"";
}


@end

@implementation NSString (Category)

//Remove Null value From String
-(NSString *)removeNull
{
    return [self stringByReplacingOccurrencesOfString:@"(null)" withString:@""];
}

//-(NSString *)decodedString
//{
//    return [[self stringByReplacingOccurrencesOfString:@"+" withString:@" "]
//            stringByReplacingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
//    
//}

@end

@implementation UIButton (Category)

-(void)buttonWithMargin:(float)margin
{
    float padding = (margin/2);
    self.contentHorizontalAlignment = UIControlContentHorizontalAlignmentCenter;
    self.contentVerticalAlignment   = UIControlContentVerticalAlignmentCenter;
    self.imageEdgeInsets            = UIEdgeInsetsMake(0.0f,  -padding, 0.0f, padding );
    self.titleEdgeInsets            = UIEdgeInsetsMake(0.0f,  padding, 0.0f, -padding );
    self.titleLabel.textColor       =[UIColor whiteColor];
}
@end

