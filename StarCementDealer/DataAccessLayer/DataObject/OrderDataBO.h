//
//  OrderDataBO.h
//  StarCementDealer
//
//  Created by Coral  on 18/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

@interface OrderDataBO : NSObject

@property(strong, nonatomic) NSString *strApporderno;
@property(strong, nonatomic) NSString *strErporderno;
@property(strong, nonatomic) NSString *strErporderdt;
@property(strong, nonatomic) NSString *strIsdoimp;
@property(strong, nonatomic) NSString *strOrderFor;
@property(strong, nonatomic) NSString *strCustomerCode;
@property(strong, nonatomic) NSString *strDnsCustomerCode;
@property(strong, nonatomic) NSString *strStatus;
@property(strong, nonatomic) NSString *strProdCode;
@property(strong, nonatomic) NSString *strDnsProdCode;
@property(strong, nonatomic) NSString *strProdDisplayName;
@property(strong, nonatomic) NSString *strQty;
@property(strong, nonatomic) NSString *strOrderFullDatetime;
@property(strong, nonatomic) NSString *strDestAddress;
@property(strong, nonatomic) NSString *strFreight;
@property(strong, nonatomic) NSString *strIsConfirmedMaterialReceived;
@property(strong, nonatomic) NSString *strQuantityChecking;
@property(strong, nonatomic) NSString *strQualityChecking;
@property(strong, nonatomic) NSString *strRemarks;

@end
