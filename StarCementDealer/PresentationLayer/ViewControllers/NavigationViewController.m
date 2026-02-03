//
//  NavigationViewController.m
//  StarCementDealer
//
//  Created by Coral  on 16/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "NavigationViewController.h"

@interface NavigationViewController ()

@end

@implementation NavigationViewController

- (void)viewDidLoad
{
    [super viewDidLoad];
    self.frostedViewController.panGestureEnabled = NO;
    [self.view addGestureRecognizer:[[UIPanGestureRecognizer alloc] initWithTarget:self action:@selector(panGestureRecognized:)]];
}

#pragma mark -
#pragma mark Gesture recognizer

- (void)panGestureRecognized:(UIPanGestureRecognizer *)sender
{
    /*
    // Dismiss keyboard (optional)
    //
    [self.view endEditing:YES];
    [self.frostedViewController.view endEditing:YES];
    
    
    
    // Present the view controller
    //
    [self.frostedViewController panGestureRecognized:sender];
     */
}

@end
