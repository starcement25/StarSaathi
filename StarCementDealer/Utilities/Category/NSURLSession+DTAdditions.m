//
//  NSURLSession+DTAdditions.m
//  RPGL
//
//  Created by Sanjeet Kumar on 07/04/21.
//  Copyright © 2021 Coral . All rights reserved.
//

#import "NSURLSession+DTAdditions.h"
//#import <AppKit/AppKit.h>

@implementation NSURLSession (DTAdditions)

+ (NSData *)requestSynchronousData:(NSURLRequest *)request
{
    __block NSData *data = nil;
    dispatch_semaphore_t semaphore = dispatch_semaphore_create(0);
    NSURLSession *session = [NSURLSession sharedSession];
    NSURLSessionDataTask *dataTask = [session dataTaskWithRequest:request completionHandler:^(NSData *taskData, NSURLResponse *response, NSError *error) {
        data = taskData;
        if (!data) {
            NSLog(@"%@", error);
        }
        dispatch_semaphore_signal(semaphore);
        
    }];
    [dataTask resume];
    dispatch_semaphore_wait(semaphore, DISPATCH_TIME_FOREVER);
    return data;
}

+ (NSData *)requestSynchronousDataWithURLString:(NSString *)requestString
{
    NSURL *url = [NSURL URLWithString:requestString];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    return [self requestSynchronousData:request];
}

+ (NSDictionary *)requestSynchronousJSON:(NSURLRequest *)request
{
    NSData *data = [self requestSynchronousData:request];
    NSError *e = nil;
    NSDictionary *jsonData = [NSJSONSerialization JSONObjectWithData:data options:NSJSONReadingMutableContainers error:&e];
    return jsonData;
}

+ (NSDictionary *)requestSynchronousJSONWithURLString:(NSString *)requestString
{
    NSURL *url = [NSURL URLWithString:requestString];
    NSMutableURLRequest *theRequest = [NSMutableURLRequest requestWithURL:url
                                                              cachePolicy:NSURLRequestUseProtocolCachePolicy
                                                          timeoutInterval:50];
    theRequest.HTTPMethod = @"GET";
    [theRequest setValue:@"application/json" forHTTPHeaderField:@"Content-Type"];
    return [self requestSynchronousJSON:theRequest];
}

//著作权归作者所有。
//商业转载请联系作者获得授权,非商业转载请注明出处。
//原文: https://robinchao.github.io/synchronous-nsurlsession-in-obj-c/

@end
