//
//  PopOrderPaymentVC.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 22/09/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface PopOrderPaymentVC : UIViewController
@property(strong, nonatomic)NSString *strTotalAmt;
@property(copy, nonatomic)NSArray *arrPickedItems;
@end

NS_ASSUME_NONNULL_END
