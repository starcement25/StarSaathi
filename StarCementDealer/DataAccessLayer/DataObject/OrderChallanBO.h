//
//  OrderChallanBO.h
//  StarCementDealer
//
//  Created by Coral  on 18/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

@interface OrderChallanBO : NSObject

@property(strong, nonatomic) NSString *strChUid;
@property(strong, nonatomic) NSString *strApporderno;
@property(strong, nonatomic) NSString *strErporderno;
@property(strong, nonatomic) NSString *strErproderdt;
@property(strong, nonatomic) NSString *strChallanNo;
@property(strong, nonatomic) NSString *strChallanDate;
@property(strong, nonatomic) NSString *strProdCode;
@property(strong, nonatomic) NSString *strDnsProdCode;
@property(strong, nonatomic) NSString *strProdDisplayName;
@property(strong, nonatomic) NSString *strQuantity;
@property(strong, nonatomic) NSString *strChallanQty;
@property(strong, nonatomic) NSString *strCustCode;
@property(strong, nonatomic) NSString *strDnsCustCode;
@property(strong, nonatomic) NSString *strTruckNo;
@property(strong, nonatomic) NSString *strDriverNo;
@property(strong, nonatomic) NSString *strIsChallanImp;
@property(strong, nonatomic) NSString *strConfirmChallanMaterialReceived;
@property(strong, nonatomic) NSString *strChallanQuantityChecking;
@property(strong, nonatomic) NSString *strChallanQualityChecking;
@property(strong, nonatomic) NSString *strChallanRemarks;
@property(strong, nonatomic) NSString *strQualityNoOfDamagedBags;
@property(strong, nonatomic) NSString *strQuantityNoOfBags;
@property(strong, nonatomic) NSString *strStatus;
@property(strong, nonatomic) NSString *strTransporterName;
@property(strong, nonatomic) NSString *strColourCode;

@end
