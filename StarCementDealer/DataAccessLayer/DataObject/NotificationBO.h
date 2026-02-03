//
//  NotificationBO.h
//  StarCementDealer
//
//  Created by Apple on 07/02/19.
//  Copyright © 2019 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NotificationBO : NSObject
@property(strong, nonatomic)NSString *strNotificationId;
@property(strong, nonatomic)NSString *strNotificationTitle;
@property(strong, nonatomic)NSString *strNotificationMsg;
@property(strong, nonatomic)NSString *strNotificationImgLink;
@property(strong, nonatomic)NSString *strNotificationDatetime;
@property(strong, nonatomic)NSString *strStatus;
@property(strong, nonatomic)NSString *strNotificationType;
@end
