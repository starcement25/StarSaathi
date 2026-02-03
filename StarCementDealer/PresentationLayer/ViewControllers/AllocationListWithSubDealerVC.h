//
//  AllocationListWithSubDealerVC.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 20/06/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface AllocationListWithSubDealerVC : UIViewController
@property(strong, nonatomic) NSDictionary *dictOrder;
@property(strong, nonatomic) NSDictionary *dictOrderFiltered;
@property(strong, nonatomic) NSArray *arrSelectedSubDealers;
@property(assign, nonatomic) int idx;
@end

NS_ASSUME_NONNULL_END
