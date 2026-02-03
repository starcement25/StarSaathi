//
//  NotificationCell.h
//  StarCementDealer
//
//  Created by Apple on 06/02/19.
//  Copyright © 2019 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@interface NotificationCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblTitle;
@property (weak, nonatomic) IBOutlet UILabel *lblDescription;
@property (weak, nonatomic) IBOutlet UIImageView *imgViewNotification;

@end
