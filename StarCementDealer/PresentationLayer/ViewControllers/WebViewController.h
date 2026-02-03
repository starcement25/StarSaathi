//
//  WebViewController.h
//  StarCementDealer
//
//  Created by Apple on 12/02/19.
//  Copyright © 2019 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@interface WebViewController : UIViewController
@property(strong, nonatomic)NSString *strWeblink;
@property(strong, nonatomic)NSString *strTitle;
@property(strong, nonatomic)NSString *strCategory;
@property(nonatomic,strong) NSDictionary *schemeData;
@end
