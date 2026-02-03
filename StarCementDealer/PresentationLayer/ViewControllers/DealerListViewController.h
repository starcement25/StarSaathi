//
//  DealerListViewController.h
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 30/03/20.
//  Copyright © 2020 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@protocol DealerListControllerDelegate <NSObject>
@optional
//- (void)dataFromControllerName:(NSString *)strName Address:(NSString*)strAddress Phone:(NSString*)strPhone;
//- (void)dataFromControllerCustCode:(NSString *)strCustomerCode;
- (void)dataFromControllerName:(NSString *)strName CustomerCode:(NSString*)strCustomerCode SapCode:(NSString*)strSapCode;

@end

@interface DealerListViewController : UIViewController
@property(strong, nonatomic)NSString *strPreviousViewidentifier;
@property (nonatomic, weak) id<DealerListControllerDelegate> delegate;
@end

NS_ASSUME_NONNULL_END
