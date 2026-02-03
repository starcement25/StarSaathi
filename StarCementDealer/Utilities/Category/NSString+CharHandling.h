//
//  NSString+CharHandling.h
//  StarCementDealer
//
//  Created by Coral  on 21/08/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSString (CharHandling)
+(NSString*)handleSpecialSymbol:(NSString*)str; // like $

- (NSString *)urlencode;
@end
