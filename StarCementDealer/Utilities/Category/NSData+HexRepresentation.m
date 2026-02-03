//
//  NSData+HexRepresentation.m
//  StarCementDealer
//
//  Created by Coral  on 04/06/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "NSData+HexRepresentation.h"

@implementation NSData (HexRepresentation)

- (NSString *)hexString {
    const unsigned char *bytes = (const unsigned char *)self.bytes;
    NSMutableString *hex = [NSMutableString new];
    for (NSInteger i = 0; i < self.length; i++) {
        [hex appendFormat:@"%02x", bytes[i]];
    }
    return [hex copy];
}

@end
