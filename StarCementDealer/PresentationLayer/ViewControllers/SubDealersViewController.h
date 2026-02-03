//
//  SubDealersViewController.h
//  StarCementDealer
//
//  Created by Coral  on 05/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@protocol SubDealerControllerDelegate <NSObject>
@optional
- (void)dataFromControllerName:(NSString *)strName Address:(NSString*)strAddress Phone:(NSString*)strPhone SubDealerId:(NSString*)strCustomerCode;
- (void)dataFromControllerCustCode:(NSString *)strCustomerCode;
- (void)noDealerFound;
-(void)setDestinationAddress;

@end

@interface SubDealersViewController : UIViewController
@property (nonatomic, copy) NSString *data;
@property (nonatomic, weak) id<SubDealerControllerDelegate> delegate;
@property (nonatomic, copy) NSString *strType; // Dealer or SubDealer

@end
