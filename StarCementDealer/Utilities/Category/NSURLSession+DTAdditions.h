//
//  NSURLSession+DTAdditions.h
//  RPGL
//
//  Created by Sanjeet Kumar on 07/04/21.
//  Copyright © 2021 Coral . All rights reserved.
//

//#import <AppKit/AppKit.h>
#import <Foundation/Foundation.h>

NS_ASSUME_NONNULL_BEGIN

@interface NSURLSession (DTAdditions)

+ (NSData *)requestSynchronousData:(NSURLRequest *)request;
+ (NSData *)requestSynchronousDataWithURLString:(NSString *)requestString;
+ (NSDictionary *)requestSynchronousJSON:(NSURLRequest *)request;
+ (NSDictionary *)requestSynchronousJSONWithURLString:(NSString *)requestString;

@end

NS_ASSUME_NONNULL_END
