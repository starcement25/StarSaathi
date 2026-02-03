//
//  PaymentViewController.m
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 22/09/21.
//  Copyright © 2021 Coral . All rights reserved.
//

#import "PaymentViewController.h"

@interface PaymentViewController (){
    __weak IBOutlet UILabel *lblRupees;
    NSDictionary *dictLedger;
    //NSString *strCustCode;
}

@end

@implementation PaymentViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

- (void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [self.navigationController setNavigationBarHidden:NO animated:animated];
    self.navigationItem.hidesBackButton = YES;
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}


#pragma mark - Initialization Method

-(void)designView {
    UIView *viewRightBarButton = [[UIView alloc]initWithFrame:CGRectMake(0, 0, 40, 40)];
    UIImageView *imgViewDetails = [[UIImageView alloc]initWithFrame:CGRectMake(0, 0, 40, 22)];
    imgViewDetails.contentMode = UIViewContentModeScaleAspectFit;
    [imgViewDetails setImage:[UIImage imageNamed:@"details"]];
    UILabel *lblDetails = UDcreateLabel(CGRectMake(0, 22, 40, 18), @"Details", [UIFont systemFontOfSize:10]);
    lblDetails.textColor = [UIColor whiteColor];
    lblDetails.textAlignment = NSTextAlignmentCenter;
    
    [viewRightBarButton addSubview:imgViewDetails];
    [viewRightBarButton addSubview:lblDetails];
    
    UIBarButtonItem *barBtnRight = [[UIBarButtonItem alloc]initWithCustomView:viewRightBarButton];
    self.navigationItem.rightBarButtonItem = barBtnRight;
    
    UITapGestureRecognizer *tapGesture = [[UITapGestureRecognizer alloc]initWithTarget:self action:@selector(btnDetailsClicked:)];
    [viewRightBarButton addGestureRecognizer:tapGesture];
}

-(void)loadData {
    [self getLedgerDetails];
}

#pragma mark - IBAction's

- (void)btnDetailsClicked:(UITapGestureRecognizer *)gesture {
    NSString *strUrl = [[dictLedger safeValueeForKey:@"ledger_balance_data"] safeValueeForKey:@"link"];
    [[UIApplication sharedApplication] openURL:[NSURL URLWithString:strUrl] options:@{} completionHandler:nil];
}

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:true];
}

- (IBAction)btnMakePaymentClicked:(UIButton *)sender {
    if ([APP_DELEGATE.strBranchWisePGRollOut isEqualToString:@"ACTIVE"]) {
        [self performSegueWithIdentifier:@"paymentToMakePayment" sender:self];
    }else
        UDShowAlertWithTitle(kAppName, @"Coming Soon"); 
}

#pragma mark - Web Service Method

-(void)getLedgerDetails{
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_ledger_by_id.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    dictLedger = dictResponse;
                    [self populateData:dictResponse];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - Set Data Method

-(void)populateData:(NSDictionary*)dictResponse {
    lblRupees.text = [NSString stringWithFormat:@"₹ %@",[[dictResponse safeValueeForKey:@"ledger_balance_data"] safeValueeForKey:@"balance"]];    
}




@end
