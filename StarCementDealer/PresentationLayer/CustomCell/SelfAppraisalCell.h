//
//  SelfAppraisalCell.h
//  StarCementDealer
//
//  Created by Coral  on 03/08/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@interface SelfAppraisalCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblMonth;
@property (weak, nonatomic) IBOutlet UILabel *lblTarget;
@property (weak, nonatomic) IBOutlet UILabel *lblAchievement;

@end
