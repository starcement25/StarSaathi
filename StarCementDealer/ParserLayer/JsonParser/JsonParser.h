//
//  JsonParser.h
//  StarCementDealer
//
//  Created by Coral  on 05/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

@interface JsonParser : NSObject

+(void)callAPIWithURL:(NSString*)strUrl parameters:(NSDictionary*)dictParam completion:(void (^)(NSDictionary *dict))completionBlock;

@end
