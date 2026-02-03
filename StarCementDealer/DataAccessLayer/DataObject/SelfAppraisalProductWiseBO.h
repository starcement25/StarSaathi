//
//  SelfAppraisalProductWiseBO.h
//  StarCementDealer
//
//  Created by Coral  on 02/08/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

@interface SelfAppraisalProductWiseBO : NSObject
@property(strong, nonatomic) NSString *strProdCode;
@property(strong, nonatomic) NSString *strProdDesc;
@property(strong, nonatomic) NSString *strEmpCode;
@property(strong, nonatomic) NSString *strMonth;
@property(strong, nonatomic) NSString *strTarget;
@property(strong, nonatomic) NSString *strAchievement;
@property(strong, nonatomic) NSString *strPrevYearTarget;
@property(strong, nonatomic) NSString *strPrevYearAchievement;
@end
