//
//  BranchSchemePDFBO.h
//  StarCementDealer
//
//  Created by Apple on 11/04/19.
//  Copyright © 2019 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

@interface BranchSchemePDFBO : NSObject
@property(strong, nonatomic)NSString *strBranchCode;
@property(strong, nonatomic)NSString *strPDFFileName;
@property(strong, nonatomic)NSString *strAcedns;

@property(nonatomic,strong) NSDictionary *schemeObject;
@end
