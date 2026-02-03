//
//  DealerVisitListVC.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 15/01/25.
//  Copyright © 2025 Coral . All rights reserved.
//

#import "DealerVisitListVC.h"
#import "DashboardViewController.h"
#import "DealerVisitListCell.h"
#import "RateDealerViewController.h"

@interface DealerVisitListVC ()<UITableViewDelegate, UITableViewDataSource>{
    __weak IBOutlet UITableView *tblViewDealer;
    NSArray *arrDealerList;
}

@end

@implementation DealerVisitListVC

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    self.title = @"Dealer List";
    
    [tblViewDealer registerNib:[UINib nibWithNibName:@"DealerVisitListCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewDealer.separatorColor = [UIColor clearColor];
    
    UINavigationBar *bar = [self.navigationController navigationBar];
    [bar setBackgroundColor:UIColorFromRGB(0xEB2228)];
    
    if (@available(iOS 13.0, *)) {
        UIView *statusBar = [[UIView alloc]initWithFrame:[UIApplication sharedApplication].keyWindow.windowScene.statusBarManager.statusBarFrame] ;
        statusBar.backgroundColor = UIColorFromRGB(0xEB2228);
        [[UIApplication sharedApplication].keyWindow addSubview:statusBar];
    } else {
        // Fallback on earlier versions
    }
   
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"back_arrow"] style:UIBarButtonItemStylePlain target:self action:@selector(btnBackClicked:)];
    btnBack.tintColor = [UIColor whiteColor];
    self.navigationItem.leftBarButtonItem = btnBack;
}

-(void)loadData {
    [self getDealerVisitList];
}

#pragma mark - Web Service

-(void)getDealerVisitList {
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"kUserLoginId"] forKey:@"customer_code"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/dealer-site-visit-list.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    arrDealerList = [dictResponse safeValueeForKey:@"sales_team_visit_data"];
                    [tblViewDealer reloadData];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}


#pragma mark - IBAction's

-(void)btnBackClicked:(UIButton*)btn{
    DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
    [self.navigationController pushViewController:dvc animated:NO];
}

- (void)reloadAPI {
    // Call your API reload logic here
    NSLog(@"API reloaded from RateDealerViewController");
    [self getDealerVisitList];
}

#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return arrDealerList.count;
}

- (DealerVisitListCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    static NSString *cellIdentifier = @"cell";
    DealerVisitListCell *cell = [tblViewDealer dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    NSDictionary *dictDealer = [arrDealerList objectAtIndex:indexPath.row];
    cell.lblEmpName.text = [dictDealer safeValueeForKey:@"emp_name"];
    cell.lblDatetime.text = [dictDealer safeValueeForKey:@"visit_datetime"];
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    NSDictionary *dictDealer = [arrDealerList objectAtIndex:indexPath.row];
    RateDealerViewController *rdvc = [self.storyboard instantiateViewControllerWithIdentifier:@"rateDealerVC"];
    rdvc.reloadAPIBlock = ^{
        [self reloadAPI];
    };
    rdvc.dictDealer = dictDealer;
    [self.navigationController pushViewController:rdvc animated:true];
}


@end
