//
//  RateDealerViewController.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 15/01/25.
//  Copyright © 2025 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface RateDealerViewController : UIViewController
@property (nonatomic, copy) NSDictionary *dictDealer; // Ensures immutability
@property (nonatomic, copy) void (^reloadAPIBlock)(void);
@end

NS_ASSUME_NONNULL_END
