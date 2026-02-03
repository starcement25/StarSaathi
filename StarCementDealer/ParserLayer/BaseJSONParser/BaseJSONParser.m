//
//  BaseJSONParser.m
//  EoKolkata
//

#import "BaseJSONParser.h"

@implementation BaseJSONParser

+ (void)baseServiceWithPostData:(NSString *)strUrl
                     withParam:(NSDictionary *)dictParam
                       success:(void(^)(NSDictionary *dictParser))blockSuccess
                        failed:(void(^)(NSString *strErrorMsg))blockError
{
    NSString *encodedUrl = [strUrl stringByAddingPercentEncodingWithAllowedCharacters:[NSCharacterSet URLQueryAllowedCharacterSet]];
    AppLog(@"Request of %@ service is::%@", encodedUrl, [BaseJSONParser stringFromDict:dictParam]);
    
    NSURL *url = [NSURL URLWithString:encodedUrl];
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:url];
    request.HTTPMethod = @"POST";
    request.timeoutInterval = 30;
    
    if (dictParam) {
        NSError *error;
        NSData *bodyData = [NSJSONSerialization dataWithJSONObject:dictParam options:0 error:&error];
        if (!error) {
            request.HTTPBody = bodyData;
            [request setValue:@"application/json" forHTTPHeaderField:@"Content-Type"];
        } else {
            blockError(@"Invalid parameters");
            return;
        }
    }
    
    NSURLSession *session = [NSURLSession sharedSession];
    NSURLSessionDataTask *task = [session dataTaskWithRequest:request
                                            completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
        if (error) {
            AppLog(@"Base error is ::%@", error.localizedDescription);
            dispatch_async(dispatch_get_main_queue(), ^{
                blockError(error.localizedDescription);
            });
            return;
        }
        
        NSString *responseString = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
        AppLog(@"Response of %@ service is :: %@", encodedUrl, responseString);
        
        [BaseJSONParser handleResponseData:data success:^(NSDictionary *dictParser) {
            dispatch_async(dispatch_get_main_queue(), ^{
                blockSuccess(dictParser);
            });
        } failed:^(NSString *strErrorMsg) {
            dispatch_async(dispatch_get_main_queue(), ^{
                blockError(strErrorMsg);
            });
        }];
    }];
    
    [task resume];
}

+ (void)handleResponseData:(NSData *)responseData
                   success:(ParserSuccess)blockSuccess
                    failed:(ParserError)blockError
{
    NSError *jsonParserError;
    NSDictionary *jsonData = [NSJSONSerialization JSONObjectWithData:responseData
                                                             options:NSJSONReadingMutableLeaves
                                                               error:&jsonParserError];
    if (jsonParserError) {
        blockError(@"Error parsing response");
    } else {
        blockSuccess(jsonData);
    }
}

+ (NSString *)jsonsStringFromDict:(id)dictionaryOrArrayToOutput
{
    NSError *error;
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:dictionaryOrArrayToOutput
                                                       options:NSJSONWritingPrettyPrinted
                                                         error:&error];
    if (!jsonData) {
        AppLog(@"Got an error: %@", error);
        return @"";
    }
    return [[NSString alloc] initWithData:jsonData encoding:NSUTF8StringEncoding];
}

+ (NSString *)stringFromDict:(NSDictionary *)dict
{
    NSMutableArray *arr = [NSMutableArray array];
    [dict enumerateKeysAndObjectsUsingBlock:^(id key, id obj, BOOL *stop) {
        [arr addObject:[NSString stringWithFormat:@"%@=%@", key, obj]];
    }];
    return [arr componentsJoinedByString:@"&"];
}

@end
