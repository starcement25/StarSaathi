//
//  SimpleGraphView.h
//  StarCementDealer
//
//  Created by SBINFO on 27/11/25.
//  Copyright © 2025 Coral . All rights reserved.
//

#import <UIKit/UIKit.h>

@interface SimpleGraphView : UIView

@property (nonatomic, strong) NSArray<NSString *> *dates;
@property (nonatomic, strong) NSArray<NSNumber *> *orderQty;
@property (nonatomic, strong) NSArray *yAxisValues;

@end
