//
//  JsonParser.m
//  StarCementDealer
//
//  Created by Coral  on 05/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "JsonParser.h"

@implementation JsonParser

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

+(void)callAPIWithURL:(NSString*)strUrl parameters:(NSDictionary*)dictParam completion:(void (^)(NSDictionary *dict))completionBlock{
    
    AppLog(@"-->>%@",strUrl);
    
    NSMutableURLRequest *req = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString:strUrl]];
    [req setHTTPMethod:@"POST"];
    NSData *postData = [[JsonParser stringFromDict:dictParam] dataUsingEncoding:NSASCIIStringEncoding allowLossyConversion:NO];
    [req setHTTPBody:postData];
    //NSURLSession *session = [NSURLSession sharedSession];
    
    NSURLSessionDataTask *task = [[[self class] session] dataTaskWithRequest:req
                                            completionHandler:^(NSData *data, NSURLResponse *response, NSError *error) {
                                                
                                                if(error){
                                                    [self showAlert:error.localizedDescription];
                                                    [SVProgressHUD dismiss];
                                                }else{
                                                    NSError *error;
                                                    
                                                    NSString *strResponse = [[NSString alloc]initWithData:data encoding:NSUTF8StringEncoding];
                                                    AppLog(@"-->>%@",strResponse);
                                                    
                                                    NSDictionary* dictResponse = [NSJSONSerialization JSONObjectWithData:data
                                                                                                                 options:kNilOptions
                                                                                                                   error:&error];
                                                    dispatch_async(dispatch_get_main_queue(), ^{
                                                        completionBlock(dictResponse);
                                                    });                                                    
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
    return [arr componentsJoinedByString:@"&"];
}

+(void)showAlert:(NSString*)strMsg{
    dispatch_async(dispatch_get_main_queue(), ^{
        UDShowToastAlertWithTitle(strMsg, 2);
    });
    
}


@end
