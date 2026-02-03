//
//  OrderInvoiceBO.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 26/10/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

NS_ASSUME_NONNULL_BEGIN

@interface OrderInvoiceBO : NSObject

@property(strong, nonatomic) NSString *strChUid;
@property(strong, nonatomic) NSString *strApporderno;
@property(strong, nonatomic) NSString *strErporderno;
@property(strong, nonatomic) NSString *strChallanNo;
@property(strong, nonatomic) NSString *strInvoiceNo;
@property(strong, nonatomic) NSString *strInvoiceDate;
@property(strong, nonatomic) NSString *strProdDisplayName;
@property(strong, nonatomic) NSString *strInvoiceQty;
@property(strong, nonatomic) NSString *strCustomerCode;
@property(strong, nonatomic) NSString *strDriverNumber;
@property(strong, nonatomic) NSString *strTruckNo;
@property(strong, nonatomic) NSString *strDestination;

@end

NS_ASSUME_NONNULL_END
