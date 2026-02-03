//
//  OrderConfirmationViewController.h
//  StarCementDealer
//
//  Created by Coral  on 04/08/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@interface OrderConfirmationViewController : UIViewController

@property(strong, nonatomic)NSMutableArray *arrProductList;

@property(strong, nonatomic)NSString *strName;
@property(strong, nonatomic)NSString *strAddress;
@property(strong, nonatomic)NSString *strDestinationCode;
@property(strong, nonatomic)NSString *strDestinationName;


@property(strong, nonatomic)NSString *strFrieghtAddress;
@property(strong, nonatomic)NSString *strFrieght; //EX OR FOR

@property(strong, nonatomic)NSString *strPhoneNumber;

@property(strong, nonatomic)NSString *strDumpStatus;
@property(strong, nonatomic)NSString *strDumpAddress;

@property(strong, nonatomic)NSString *strDealerTruckStatus;
@property(strong, nonatomic)NSString *strSubDealerId;
@property(strong, nonatomic)NSString *strDumpCode;
@property(strong, nonatomic)NSString *strOrderForType;

@property(strong, nonatomic)NSString *strDeliveryPoint;
@property(strong, nonatomic)NSString *strDeliveryRemarks;




@end
