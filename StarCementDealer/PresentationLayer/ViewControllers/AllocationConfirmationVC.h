//
//  AllocationConfirmationVC.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 20/06/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface AllocationConfirmationVC : UIViewController
@property(strong, nonatomic) NSDictionary *dictOrder;
@property(strong, nonatomic) NSArray *arrSelectedSubDealers;
@end

NS_ASSUME_NONNULL_END
