//
//  DeviceId.m
//  StarCementDealer
//

#import "DeviceId.h"
#import <Security/Security.h>

@implementation DeviceId

+ (NSString *)GetDeviceID {
    NSString *udidString = [self objectForKey:@"deviceID"];
    if (!udidString) {
        CFUUIDRef cfuuid = CFUUIDCreate(kCFAllocatorDefault);
        udidString = (NSString *)CFBridgingRelease(CFUUIDCreateString(kCFAllocatorDefault, cfuuid));
        CFRelease(cfuuid);
        [self setObject:udidString forKey:@"deviceID"];
    }
    return udidString;
}

+ (void)setObject:(NSString *)object forKey:(NSString *)key {
    NSData *valueData = [object dataUsingEncoding:NSUTF8StringEncoding];
    
    NSDictionary *query = @{
        (__bridge id)kSecClass: (__bridge id)kSecClassGenericPassword,
        (__bridge id)kSecAttrService: @"LIB",
        (__bridge id)kSecAttrAccount: key
    };
    
    // Delete existing item if any
    SecItemDelete((__bridge CFDictionaryRef)query);
    
    NSMutableDictionary *attributes = [query mutableCopy];
    attributes[(__bridge id)kSecValueData] = valueData;
    
    OSStatus status = SecItemAdd((__bridge CFDictionaryRef)attributes, NULL);
    if (status != errSecSuccess) {
        NSLog(@"Keychain set error: %d", (int)status);
    }
}

+ (NSString *)objectForKey:(NSString *)key {
    NSDictionary *query = @{
        (__bridge id)kSecClass: (__bridge id)kSecClassGenericPassword,
        (__bridge id)kSecAttrService: @"LIB",
        (__bridge id)kSecAttrAccount: key,
        (__bridge id)kSecReturnData: @YES,
        (__bridge id)kSecMatchLimit: (__bridge id)kSecMatchLimitOne
    };
    
    CFTypeRef result = NULL;
    OSStatus status = SecItemCopyMatching((__bridge CFDictionaryRef)query, &result);
    
    if (status == errSecSuccess && result != NULL) {
        NSData *data = (__bridge_transfer NSData *)result;
        NSString *object = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
        return object;
    } else {
        return nil;
    }
}

@end
