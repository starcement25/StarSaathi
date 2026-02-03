//
//  PopDeliveryAddVC.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 22/09/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface PopDeliveryAddVC : UIViewController
@property(copy, nonatomic)NSArray *arrPickedItems;
@property(strong, nonatomic)NSString *strPaymentBy;
@property(assign, nonatomic)BOOL flagForPG; //Using to determing if user will goto payment gateway or not, if user purchasing free product then user will not goto payment gateway webview
@end

NS_ASSUME_NONNULL_END
