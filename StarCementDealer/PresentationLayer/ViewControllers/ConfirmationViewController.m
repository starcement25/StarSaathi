//
//  ConfirmationViewController.m
//  StarCementDealer
//
//  Created by Apple on 04/09/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "ConfirmationViewController.h"
#import "DashboardViewController.h"

@interface ConfirmationViewController ()

@end

@implementation ConfirmationViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView{
    self.navigationItem.hidesBackButton = YES;
}

-(void)loadData{
    
}

#pragma mark - IBAction's

- (IBAction)btnOkClicked:(UIButton *)sender {
    for (UIViewController *controller in self.navigationController.viewControllers) {
        if ([controller isKindOfClass:[DashboardViewController class]]) {
            [self.navigationController popToViewController:controller animated:YES];
            return;
        }
    }
}

@end
