//
//  HelpContentViewController.h
//  StarCementDealer
//
//  Created by Apple on 04/09/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@interface HelpContentViewController : UIViewController
@property (weak, nonatomic) IBOutlet UIImageView *imgViewContent;
@property NSUInteger pageIndex;
@property NSString *titleText;
@property NSString *imageFile;
@end
