//
//  NSObject+Category.h
//  LeadCapture
//

#import <Foundation/Foundation.h>
#import <WebKit/WebKit.h>

@interface NSObject (Category)
-(id)safeValueeForKey:(NSString *)strKey;
@end

@interface NSString (Category)
//Remove Null value From String
-(NSString *)removeNull;
//-(NSString *)decodedString;
@end


@interface UIButton (Category)

-(void)buttonWithMargin:(float)margin;
@end
