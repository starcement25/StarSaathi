//
//  XMLParser.m
//  StarCementDealer
//
//  Created by Coral  on 04/06/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "XMLParser.h"

@implementation XMLParser

+ (NSURLSession *)session
{
    static NSURLSession *session = nil;
    static dispatch_once_t onceToken;
    dispatch_once(&onceToken, ^{
        
        NSURLSessionConfiguration *configuration = [NSURLSessionConfiguration defaultSessionConfiguration];
        [configuration setHTTPMaximumConnectionsPerHost:1];
        session = [NSURLSession sessionWithConfiguration:configuration];
        
    });
    return session;
}

+(void)downloadDataFromURL:(NSURL *)url withCompletionHandler:(void (^)(NSData *))completionHandler{
    // Instantiate a session configuration object.
    NSURLSessionConfiguration *configuration = [NSURLSessionConfiguration defaultSessionConfiguration];
    
    // Instantiate a session object.
    NSURLSession *session = [NSURLSession sessionWithConfiguration:configuration];
    
    // Create a data task object to perform the data downloading.
    NSURLSessionDataTask *task = [session dataTaskWithURL:url completionHandler:^(NSData *data, NSURLResponse *response, NSError *error) {
        
        if (error != nil) {
            // If any error occurs then just display its description on the console.
            NSLog(@"%@", [error localizedDescription]);
            [self showAlert:error.localizedDescription];
            [SVProgressHUD dismiss];
        }
        else{
            // If no error occurs, check the HTTP status code.
            NSInteger HTTPStatusCode = [(NSHTTPURLResponse *)response statusCode];
            
            // If it's other than 200, then show it on the console.
            if (HTTPStatusCode != 200) {
                NSLog(@"HTTP status code = %ld", (long)HTTPStatusCode);
            }
            
            // Call the completion handler with the returned data on the main thread.
            [[NSOperationQueue mainQueue] addOperationWithBlock:^{
                completionHandler(data);
            }];
        }
    }];
    
    // Resume the task.
    [task resume];
}

+(void)callAPIWithURL:(NSString*)strUrl parameters:(NSDictionary*)dictParam completion:(void (^)(NSData *))completionBlock{
    
    AppLog(@"Hit Url : %@",strUrl);
    
    
    NSMutableURLRequest *req = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString:strUrl]];
    [req setHTTPMethod:@"POST"];
    NSData *postData = [[XMLParser stringFromDict:dictParam] dataUsingEncoding:NSASCIIStringEncoding allowLossyConversion:NO];
    [req setHTTPBody:postData];
    
    
    
    //NSURLSession *session = [NSURLSession sharedSession];
    NSURLSessionDataTask *task = [[[self class] session] dataTaskWithRequest:req
                                            completionHandler:^(NSData *data, NSURLResponse *response, NSError *error) {
                                                
                                                if (error != nil) {
                                                    // If any error occurs then just display its description on the console.
                                                    NSLog(@"%@", [error localizedDescription]);
                                                    [self showAlert:error.localizedDescription];
                                                    [SVProgressHUD dismiss];
                                                }
                                                else{
                                                    // If no error occurs, check the HTTP status code.
                                                    NSInteger HTTPStatusCode = [(NSHTTPURLResponse *)response statusCode];
                                                    
                                                    // If it's other than 200, then show it on the console.
                                                    if (HTTPStatusCode != 200) {
                                                        NSLog(@"HTTP status code = %ld", (long)HTTPStatusCode);
                                                    }
                                                    
                                                    // Call the completion handler with the returned data on the main thread.
                                                    [[NSOperationQueue mainQueue] addOperationWithBlock:^{
                                                        completionBlock(data);
                                                    }];
                                                }
                                            }];
    
    [task resume];
    
}

+(void)callServiceWithPostData:(NSString *)strUrl withParam:(NSDictionary *)dictParam
                       success:(void(^)(NSData *))blockSuccess failed:(void(^)(NSString *strErrorMsg))blockError{
    
    //NSLog(@"Hit Url : %@",strUrl);
    
    NSMutableURLRequest *req = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString:strUrl]];
    [req setHTTPMethod:@"POST"];
    NSData *postData = [[XMLParser stringFromDict:dictParam] dataUsingEncoding:NSASCIIStringEncoding allowLossyConversion:NO];
    [req setHTTPBody:postData];
    
    NSURLSessionDataTask *task = [[[self class] session] dataTaskWithRequest:req
                                                           completionHandler:^(NSData *data, NSURLResponse *response, NSError *error) {
                                                               
                                                               if (error != nil) {
                                                                   // If any error occurs then just display its description on the console.
                                                                   [[NSOperationQueue mainQueue] addOperationWithBlock:^{
                                                                       blockError([error localizedDescription]);
                                                                   }];                                                                   
                                                                   
//                                                                   NSLog(@"%@", [error localizedDescription]);
//                                                                   [self showAlert:error.localizedDescription];
//                                                                   [SVProgressHUD dismiss];
                                                               }
                                                               else{
                                                                   // If no error occurs, check the HTTP status code.
                                                                   NSInteger HTTPStatusCode = [(NSHTTPURLResponse *)response statusCode];
                                                                   
                                                                   // If it's other than 200, then show it on the console.
                                                                   if (HTTPStatusCode != 200) {
                                                                       NSLog(@"HTTP status code = %ld", (long)HTTPStatusCode);
                                                                   }
                                                                   
                                                                   // Call the completion handler with the returned data on the main thread.
                                                                   [[NSOperationQueue mainQueue] addOperationWithBlock:^{
                                                                       //completionBlock(data);
                                                                       blockSuccess(data);
                                                                   }];
                                                               }
                                                           }];
    
    [task resume];
}


+(NSString *)stringFromDict:(NSDictionary *)dict
{
    NSMutableArray *arr = [[NSMutableArray alloc]init];
    [dict enumerateKeysAndObjectsUsingBlock:^(NSString *key, NSString *obj, BOOL *stop) {
        [arr addObject:[NSString stringWithFormat:@"%@=%@", key, obj]];
    }];
    AppLog(@"-->>%@",[arr componentsJoinedByString:@"&"]);
    return [arr componentsJoinedByString:@"&"];
}

+(void)showAlert:(NSString*)strMsg{
    [[NSOperationQueue mainQueue] addOperationWithBlock:^{
        UDShowToastAlertWithTitle(strMsg, 2);
    }];    
}

@end
