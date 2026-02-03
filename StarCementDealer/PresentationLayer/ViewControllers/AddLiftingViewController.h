//
//  AddLiftingViewController.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 24/05/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@protocol AddLiftingViewControllerDelegate <NSObject>
-(void)reloadLiftingHistory;
@end

NS_ASSUME_NONNULL_BEGIN

@interface AddLiftingViewController : UIViewController
@property (nonatomic, weak) id<AddLiftingViewControllerDelegate> delegate;
@end

NS_ASSUME_NONNULL_END
