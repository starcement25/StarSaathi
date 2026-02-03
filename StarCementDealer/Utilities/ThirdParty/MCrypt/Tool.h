//
//  Tool.h
//  EncyDcry
//
//  Created by Coral  on 14/08/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>
#import "CommonCrypto/CommonCrypto.h"

@interface Tool : NSObject

+ (NSString*) crypt:(NSString*)recource;
+ (NSString*) decrypt:(NSString*)recource;

@end
