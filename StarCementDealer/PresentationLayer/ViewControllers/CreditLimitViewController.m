//
//  CreditLimitViewController.m
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 10/12/20.
//  Copyright © 2020 Coral . All rights reserved.
//

#import "CreditLimitViewController.h"
#import "DashboardViewController.h"

@interface CreditLimitViewController (){
    __weak IBOutlet UILabel *lblCreditLimit;
    __weak IBOutlet UILabel *lblPendingOrders;
    __weak IBOutlet UILabel *lblCreditBalance;
}

@end

@implementation CreditLimitViewController

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
    self.title = @"Credit Limit";
    
    UINavigationBar *bar = [self.navigationController navigationBar];
    [bar setBackgroundColor:UIColorFromRGB(0xEB2228)];
    
    CGFloat statusBarHeight;
    if (@available(iOS 11.0, *)) {
        statusBarHeight = UIApplication.sharedApplication.keyWindow.safeAreaInsets.top;
    } else {
        statusBarHeight = [UIApplication sharedApplication].statusBarFrame.size.height;
    }
    
    if (@available(iOS 13.0, *)) {
        UIView *statusBar = [[UIView alloc]initWithFrame:CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, statusBarHeight)] ;
        statusBar.backgroundColor = UIColorFromRGB(0xEB2228);
        [[UIApplication sharedApplication].keyWindow addSubview:statusBar];
    } else {
        // Fallback on earlier versions
    }
    
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"back_arrow"] style:UIBarButtonItemStylePlain target:self action:@selector(btnBackClicked:)];
    btnBack.tintColor = [UIColor whiteColor];
    self.navigationItem.leftBarButtonItem = btnBack;
}

-(void)loadData{
    [self getCreditLimit];
}

#pragma mark - IBAction's

-(void)btnBackClicked:(UIButton*)btn{
    DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
    [self.navigationController pushViewController:dvc animated:NO];
}

#pragma mark - Web Service

-(void)getCreditLimit{
    
    if (APP_DELEGATE.isServerReachable) {
        
//        NSString *strCustCode;
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
//        }else{
//            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
//            FMDatabase *db = [FMDatabase databaseWithPath:path];
//            if ([db open]) {
//                FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
//                while ([s next]) {
//                    strCustCode = [s stringForColumn:@"customer_code"];
//                }
//                [db close];
//            }
//        }
        
//        NSString *strCustCode;
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
//        }else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"sub dealer"]){
//            strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
//        }else{
//            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
//            FMDatabase *db = [FMDatabase databaseWithPath:path];
//            if ([db open]) {
//                FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
//                while ([s next]) {
//                    strCustCode = [s stringForColumn:@"customer_code"];
//                }
//                [db close];
//            }
//        }
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_ledger_balance_details_by_id.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    
                    lblCreditLimit.text = ([[dictResponse safeValueeForKey:@"credit_limit"] isEqualToString:@""]) ? @"₹00.00" : [NSString stringWithFormat:@"₹%@",[dictResponse safeValueeForKey:@"credit_limit"]];
                    
                    lblPendingOrders.text = ([[dictResponse safeValueeForKey:@"credit_days"] isEqualToString:@""]) ? @"0" : [dictResponse safeValueeForKey:@"credit_days"];
                    
                    lblCreditBalance.text = ([[dictResponse safeValueeForKey:@"current_balance"] isEqualToString:@""]) ? @"₹00.00" : [NSString stringWithFormat:@"₹%@",[dictResponse safeValueeForKey:@"current_balance"]];
                    
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

@end
