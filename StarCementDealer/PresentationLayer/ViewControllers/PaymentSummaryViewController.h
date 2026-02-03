//
//  PaymentSummaryViewController.h
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 23/11/20.
//  Copyright © 2020 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface PaymentSummaryViewController : UIViewController
@property(strong, nonatomic) NSString *strAmount;
@property(strong, nonatomic) NSString *strPaymentMethod;
@end

NS_ASSUME_NONNULL_END
