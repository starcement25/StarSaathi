#import <Foundation/Foundation.h>
#import "NSObject+Category.h"
#import "BlockAndProtocolConstants.h"

/** For Service */
NS_INLINE NSString *APP_SERVICE(NSString *strService) {
    return [NSString stringWithFormat:@"%@%@", MainService, strService];
}

typedef void(^ParserSuccess)(NSDictionary *dictParser);
typedef void(^ParserError)(NSString *strErrorMsg);

@interface BaseJSONParser : NSObject

/** Call the base service */
+ (void)baseServiceWithPostData:(NSString *)strUrl
                      withParam:(NSDictionary *)dictParam
                        success:(ParserSuccess)blockSuccess
                         failed:(ParserError)blockError;

+ (void)handleResponseData:(NSData *)responseData
                   success:(ParserSuccess)blockSuccess
                    failed:(ParserError)blockError;

+ (NSString *)jsonStringFromDict:(id)dictionaryOrArrayToOutput;

@end
