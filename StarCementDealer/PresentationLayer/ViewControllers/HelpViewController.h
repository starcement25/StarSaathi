//
//  HelpViewController.h
//  StarCementDealer
//
//  Created by Apple on 04/09/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>
#import "HelpContentViewController.h"

@interface HelpViewController : UIViewController<UIPageViewControllerDataSource>

@property (nonatomic,strong) UIPageViewController *PageViewController;
@property (nonatomic,strong) NSArray *arrPageTitles;
@property (nonatomic,strong) NSArray *arrPageImages;

- (HelpContentViewController *)viewControllerAtIndex:(NSUInteger)index;

@end
