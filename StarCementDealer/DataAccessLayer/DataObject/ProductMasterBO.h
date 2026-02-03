//
//  ProductMasterBO.h
//  StarCementDealer
//
//  Created by Coral  on 09/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

@interface ProductMasterBO : NSObject

@property(strong, nonatomic) NSString *strBranchCode;
@property(strong, nonatomic) NSString *strProdCode;
@property(strong, nonatomic) NSString *strDnsProdCode;
@property(strong, nonatomic) NSString *strProductGroupCode;
@property(strong, nonatomic) NSString *strProdSubGroupCode;
@property(strong, nonatomic) NSString *strProdBrandCode;
@property(strong, nonatomic) NSString *strProdDesc;
@property(strong, nonatomic) NSString *strClStk;
@property(strong, nonatomic) NSString *strUOM1;
@property(strong, nonatomic) NSString *strUOM2;
@property(strong, nonatomic) NSString *strUOM3;
@property(strong, nonatomic) NSString *strAcedns;
@property(strong, nonatomic) NSString *strBlackList;
@property(strong, nonatomic) NSString *strVerticalValue;
@property(strong, nonatomic) NSString *strFreightCost;
@property(strong, nonatomic) NSString *strFocus;
@property(strong, nonatomic) NSString *strWeightage;
@property(strong, nonatomic) NSString *strVat;
@property(strong, nonatomic) NSString *strAddlVat;
@property(strong, nonatomic) NSString *strConversionFactor;
@property(strong, nonatomic) NSString *strConversionFactorTwo;
@property(strong, nonatomic) NSString *strSecondaryUnit;
@property(strong, nonatomic) NSString *strPackSize;
@property(strong, nonatomic) NSString *strTD;
@property(strong, nonatomic) NSString *strDownloadTime;
@property(strong, nonatomic) NSString *strDownloadTimeClStk;
@property(strong, nonatomic) NSString *strQuantity; // Filled by user

@end
