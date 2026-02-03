//
//  DeviceId.h
//  StarCementDealer
//
//  Created by Apple on 03/09/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

@interface DeviceId : NSObject
+ (NSString *)GetDeviceID;
+(void) setObject:(NSString*) object forKey:(NSString*) key;
+(NSString*) objectForKey:(NSString*) key;
@end
