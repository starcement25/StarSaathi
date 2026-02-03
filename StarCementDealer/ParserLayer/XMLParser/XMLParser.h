//
//  XMLParser.h
//  StarCementDealer
//
//  Created by Coral  on 04/06/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

@interface XMLParser : NSObject

+(void)downloadDataFromURL:(NSURL *)url withCompletionHandler:(void(^)(NSData *data))completionHandler;
+(void)callAPIWithURL:(NSString*)strUrl parameters:(NSDictionary*)dictParam completion:(void (^)(NSData *))completionBlock;
+(NSString *)stringFromDict:(NSDictionary *)dict;
+(void)showAlert:(NSString*)strMsg;
+(void)callServiceWithPostData:(NSString *)strUrl withParam:(NSDictionary *)dictParam
                       success:(void(^)(NSData *))blockSuccess failed:(void(^)(NSString *strErrorMsg))blockError;

@end
