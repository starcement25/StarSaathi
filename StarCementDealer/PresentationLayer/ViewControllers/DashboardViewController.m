//
//  DashboardViewController.m
//  StarCementDealer
//
//  Created by Coral  on 04/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "DashboardViewController.h"
#import "SliderCell.h"
#import <UIKit/UIKit.h>
#import "REFrostedViewController.h"
#import <MessageUI/MessageUI.h>
#import "Tool.h"
#import "XMLParser.h"
#import "XMLReader.h"
#import "DeviceId.h"
#import "SplashViewController.h"
#import "NSString+CharHandling.h"
#import "UIBarButtonItem+Badge.h"
#import <StoreKit/StoreKit.h>
#import "DealerListViewController.h"
#import "DashboardMenuCell.h"
#import "WebViewController.h"
#import "RewardsViewController.h"

@interface DashboardViewController ()<UICollectionViewDelegate, UICollectionViewDataSource, UICollectionViewDelegateFlowLayout, MFMailComposeViewControllerDelegate, DealerListControllerDelegate,UIGestureRecognizerDelegate>{
    NSArray *arrSlider;
    NSArray *arrGameSlider;
    __weak IBOutlet UICollectionView *collViewSlider;
    __weak IBOutlet UIImageView *imgViewLogo;
    __weak IBOutlet UICollectionView *collViewMenu;
    __weak IBOutlet UIPageControl *pageControlSlider;
    UIBarButtonItem *barButtonNotification;
    NSArray *arrMenuImage;
    NSArray *arrMenuTitle;
    int intCounterSlider;
    __weak IBOutlet NSLayoutConstraint *constraintCollViewMenuHeight;
    __weak IBOutlet UICollectionView *collViewGameBanner;
    __weak IBOutlet UILabel *lblOutstandingBal;
    __weak IBOutlet UILabel *lblCreditLimit;
    __weak IBOutlet UIStackView *stackViewCreditLimit;
    __weak IBOutlet NSLayoutConstraint *constraintViewCreditLimitHeight;
    __weak IBOutlet UIView *viewCreditLimit;
    __weak IBOutlet NSLayoutConstraint *conViewSclHeight;
    __weak IBOutlet UILabel *lblScl;
    __weak IBOutlet UILabel *lblScnel;
    __weak IBOutlet UILabel *lblSlidingText;
    __weak IBOutlet UIView *viewSlidingTextSpace;
    NSString *strWebLink;
    NSUserDefaults *defaults;
    
    //-----------------------------------------------
    // For sync
    NSString *strDataDownloadTime;
    int      counter;
    //-----------------------------------------------
}

@end

@implementation DashboardViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view.
    
    defaults = [NSUserDefaults standardUserDefaults];
    [self designView];
    [self loadData];
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [self.navigationController setNavigationBarHidden:NO animated:animated];
    self.navigationItem.hidesBackButton = YES;
    
    AppLog(@"-->>%@",[defaults valueForKey:@"user_type"]);
    
    if (checkUserType(kbroker) == true) {
        APP_CONSTANTS.strCustCode = [defaults valueForKey:@"selected_cust_code"];
    }else{
        APP_CONSTANTS.strCustCode = [defaults valueForKey:@"emp_code"];
    }
    
    APP_DELEGATE.strDeviceAppVersion = [[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleShortVersionString"];
    if (APP_CONSTANTS.strCustCode.length) {
        // Uncomment below lines to disable force logout or uncomment below lines before release to app-store 
        [self updateDeviceToken];
    }
    barButtonNotification.badgeValue = [self notificationCount];
}

- (void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
    constraintCollViewMenuHeight.constant = collViewMenu.contentSize.height;

    [self startScrollingText];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)startScrollingText {
    NSString *text = lblSlidingText.text;
    if (text.length == 0) return;

    // Calculate the size of the text
    UIFont *font = lblSlidingText.font;
    CGSize textSize = [text sizeWithAttributes:@{NSFontAttributeName: font}];

    // Set the label's width to the full text width
    CGRect labelFrame = lblSlidingText.frame;
    labelFrame.size.width = textSize.width;
    lblSlidingText.frame = labelFrame;

    // Reset label position to the right (off-screen)
    labelFrame.origin.x = viewSlidingTextSpace.frame.size.width;
    lblSlidingText.frame = labelFrame;

    // Animate the label to the left
    [UIView animateWithDuration:(textSize.width / 20.0)
                          delay:0.0
                        options:UIViewAnimationOptionCurveLinear
                     animations:^{
        CGRect newFrame = lblSlidingText.frame;
        newFrame.origin.x = -textSize.width;
        lblSlidingText.frame = newFrame;
    }
                     completion:^(BOOL finished) {
        // Reset the label position to the right after animation completes
        [self startScrollingText];
    }];
}


#pragma mark - Initialization method

-(void)designView{
    
    pageControlSlider.hidden = true;
    
    UIImage *imgNotification = [UIImage imageNamed:@"notification"];
    UIButton *btnNotification = [UIButton buttonWithType:UIButtonTypeCustom];
    btnNotification.frame = CGRectMake(0,0,imgNotification.size.width, imgNotification.size.height);
    [btnNotification addTarget:self action:@selector(btnNotificationClicked:) forControlEvents:UIControlEventTouchDown];
    [btnNotification setBackgroundImage:imgNotification forState:UIControlStateNormal];
    
    barButtonNotification = [[UIBarButtonItem alloc]initWithCustomView:btnNotification];
    barButtonNotification.tintColor = [UIColor whiteColor];
    barButtonNotification.badgeValue = [self notificationCount];
    barButtonNotification.badgeBGColor = [UIColor blackColor];
    
    UIBarButtonItem *barButtonRefresh = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"refresh"] style:UIBarButtonItemStylePlain target:self action:@selector(btnSyncClicked:)];
    barButtonRefresh.tintColor = [UIColor whiteColor];
    
    UIBarButtonItem *barButtonFilter = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"filter"] style:UIBarButtonItemStylePlain target:self action:@selector(btnFilterClicked:)];
    barButtonFilter.tintColor = [UIColor whiteColor];
    
    if (checkUserType(kbroker) == true) {
        self.navigationItem.rightBarButtonItems = @[barButtonFilter,barButtonRefresh,barButtonNotification];
    }else if (checkUserType(kSubDealer) == true){
        
    }else{
        self.navigationItem.rightBarButtonItems=@[barButtonRefresh,barButtonNotification];
    }
    
    [collViewSlider registerNib:[UINib nibWithNibName:@"SliderCell" bundle:nil] forCellWithReuseIdentifier:@"cell"];
    [collViewMenu registerNib:[UINib nibWithNibName:@"DashboardMenuCell" bundle:nil] forCellWithReuseIdentifier:@"cell"];
    [collViewGameBanner registerNib:[UINib nibWithNibName:@"SliderCell" bundle:nil] forCellWithReuseIdentifier:@"cell"];
}

-(void)loadData{
    
    [self loadSlider];
    [self wsCheckAppVersion];
    [self wsDealerWiseCreditLimit:[defaults valueForKey:@"kDealerId"]];
    
    if (checkUserType(kSubDealer) == true || checkUserType(kRssd) == true) {
        
        stackViewCreditLimit.hidden = true;
        constraintViewCreditLimitHeight.constant = 0;
        
        viewCreditLimit.hidden = true;
        conViewSclHeight.constant = 0;
        
        arrMenuImage = @[@"trackorder", @"ledger", @"performance", @"product_performance", @"scheme", @"retailor_lifting", @"pop_ios", @"mason_lifting", @"rssd_allocation_history", @"rewards_ios", @"greetings", @"new_order_enq"];
        arrMenuTitle = @[@"TRACK ORDER", @"LEDGER", @"PERFORMANCE",@"PRODUCT WISE PERFORMANCE", @"SCHEME", @"RETAILER LIFTING",@"POP ORDER", @"MASON LIFTING APPROVAL", @"RSAR LIFTING ALLOCATION", @"REWARDS", @"GREETINGS", @"NEW ORDER ENQUIRY"];
        
    }else if (checkUserType(kbroker) == true) {
        
        //arrMenuImage = @[@"order", @"trackorder", @"ledger", @"performance", @"product_performance", @"scheme", @"tour", @"Engagements", @"rewards_ios", @"retailor_lifting", @"pop_ios", @"mason_lifting",@"invoice", @"ageing", @"greetings", @"order_enq"];
        //arrMenuTitle = @[@"ORDER", @"TRACK ORDER", @"LEDGER", @"PERFORMANCE", @"PRODUCT WISE PERFORMANCE", @"SCHEME", @"TOUR", @"ENGAGEMENTS", @"REWARDS", @"RETAILER LIFTING", @"POP ORDER", @"MASON LIFTING APPROVAL",@"PENDING INVOICES", @"AGEING", @"GREETINGS", @"ORDER ENQUIRY"];
        
        arrMenuImage = @[@"order", @"trackorder", @"ledger", @"performance", @"product_performance", @"scheme", @"tour", @"Engagements", @"rewards_ios", @"pop_ios", @"mason_lifting",@"invoice", @"ageing", @"greetings", @"order_enq"];
        arrMenuTitle = @[@"ORDER", @"TRACK ORDER", @"LEDGER", @"PERFORMANCE", @"PRODUCT WISE PERFORMANCE", @"SCHEME", @"TOUR", @"ENGAGEMENTS", @"REWARDS", @"POP ORDER", @"MASON LIFTING APPROVAL",@"PENDING INVOICES", @"AGEING", @"GREETINGS", @"ORDER ENQUIRY"];
        
    }else{
        
        arrMenuImage = @[@"order", @"trackorder", @"ledger", @"performance", @"product_performance", @"scheme", @"tour", @"Engagements", @"rewards_ios", @"retailor_lifting", @"pop_ios", @"mason_lifting",@"invoice", @"ageing", @"greetings", @"order_enq"];
        arrMenuTitle = @[@"ORDER", @"TRACK ORDER", @"LEDGER", @"PERFORMANCE", @"PRODUCT WISE PERFORMANCE", @"SCHEME", @"TOUR", @"ENGAGEMENTS", @"REWARDS", @"RETAILER LIFTING", @"POP ORDER", @"MASON LIFTING APPROVAL", @"PENDING INVOICES", @"AGEING", @"GREETINGS", @"ORDER ENQUIRY"];     
    }
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        BOOL success =  [db executeUpdate:@"DELETE FROM notification"];
        if (success) {
            [self getNotification:@""];
        }
        [db close];
    }
    
    if (checkUserType(kbroker) == true) {
        if (([[defaults valueForKey:@"selected_cust_code"] length] != 0)) {
            self.title = [defaults valueForKey:@"selected_cust_name"];
        }else{
            [self performSegueWithIdentifier:@"dashboardToDealerList" sender:self];
        }
    }else if (checkUserType(kDealer) == true){
        if (![[defaults valueForKey:@"kServeyFormSubmitted"] isEqualToString:@"YES"]) {
            [self performSegueWithIdentifier:@"dashboardToTdsForm" sender:self];
        }
    }
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"dashboardToDealerList"]) {
        DealerListViewController *dlvc = segue.destinationViewController;
        dlvc.delegate = self;
    }else if ([segue.identifier isEqualToString:@"dashboardToWebview"]) {
        WebViewController *wvc = segue.destinationViewController;
        wvc.strTitle = (NSString*)sender;
        wvc.strWeblink = strWebLink;
    }else if ([segue.identifier isEqualToString:@"dashboardToRewards"]) {
        RewardsViewController *rvc = segue.destinationViewController;
        rvc.strRewardLink = (NSString*)sender;
    }
}

#pragma mark - DelaerListViewController Delegate

- (void)dataFromControllerName:(NSString *)strName CustomerCode:(NSString*)strCustomerCode SapCode:(NSString*)strSapCode{
    AppLog(@"-->>%@",strName);
    AppLog(@"-->>%@",strCustomerCode);
    self.title = strName;
    [defaults setValue:strCustomerCode forKey:@"selected_cust_code"];
    [defaults setValue:strName forKey:@"selected_cust_name"];
    [defaults setValue:strSapCode forKey:@"kDealerId"];
    [defaults synchronize];
    
    if (checkUserType(kbroker) == true) {
        APP_CONSTANTS.strCustCode = strCustomerCode;
    }else{
        APP_CONSTANTS.strCustCode = [defaults valueForKey:@"emp_code"];
    }
    
    [self wsDetinationMaster:strCustomerCode];
    [self wsDumpMaster:strCustomerCode];
    [self wsDealerWiseCreditLimit:strSapCode];
}

#pragma mark - UICollectionView Delegate and DataSource

- (NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section{
    if (collectionView == collViewSlider) {
        return arrSlider.count;
    }else if (collectionView == collViewGameBanner){
        return arrGameSlider.count;
    }else {
        return arrMenuImage.count;
    }
}

// The cell that is returned must be retrieved from a call to -dequeueReusableCellWithReuseIdentifier:forIndexPath:
- (UICollectionViewCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath{
    
    static NSString *cellIdentifier = @"cell";
    
    if (collectionView == collViewSlider) {
        SliderCell *cell = [collViewSlider dequeueReusableCellWithReuseIdentifier:cellIdentifier forIndexPath:indexPath];
        NSURL *url = [NSURL URLWithString:arrSlider[indexPath.row]];
        dispatch_async(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
            NSData *data = [NSData dataWithContentsOfURL:url];
            UIImage *image = [UIImage imageWithData:data];
            dispatch_async(dispatch_get_main_queue(), ^{
                cell.imgViewSlider.image = image;
            });
        });
        return cell;
    }else if (collectionView == collViewGameBanner) {
        SliderCell *cell = [collViewSlider dequeueReusableCellWithReuseIdentifier:cellIdentifier forIndexPath:indexPath];
        NSURL *url = [NSURL URLWithString:arrGameSlider[indexPath.row][@"banner_link"]];
        dispatch_async(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
            NSData *data = [NSData dataWithContentsOfURL:url];
            UIImage *image = [UIImage imageWithData:data];
            dispatch_async(dispatch_get_main_queue(), ^{
                cell.imgViewSlider.image = image;
            });
        });

        return cell;
    }else{
        DashboardMenuCell *cell = [collViewMenu dequeueReusableCellWithReuseIdentifier:cellIdentifier forIndexPath:indexPath];
        cell.imgViewMenu.image = [UIImage imageNamed:arrMenuImage[indexPath.row]];
        cell.lblMenu.text = arrMenuTitle[indexPath.row];
        return cell;
    }
}

- (void)collectionView:(UICollectionView *)collectionView didSelectItemAtIndexPath:(NSIndexPath *)indexPath {
    if (collectionView == collViewMenu) {
        
        NSString *strMenu = arrMenuTitle[indexPath.row];
        [self wsSaveAppUsage:strMenu];
        
        if ([strMenu isEqualToString:@"ORDER"]) {
            [self performSegueWithIdentifier:@"dashboardToSelectProduct" sender:self];
        }else if ([strMenu isEqualToString:@"TRACK ORDER"]){
            
            if (checkUserType(kSubDealer) == true || checkUserType(kRssd) == true) {
                [self performSegueWithIdentifier:@"dashboardToTrackOrdersRssd" sender:self];
            }else{
                [self performSegueWithIdentifier:@"dashboardToTrackOrders" sender:self];
            }
            
        }else if ([strMenu isEqualToString:@"LEDGER"]){
            
            if (checkUserType(kSubDealer) == true || checkUserType(kRssd) == true) {
                [self performSegueWithIdentifier:@"dashboardToLedgerRssd" sender:self];
            }else{
                [self performSegueWithIdentifier:@"dashboardToLedger" sender:self];
            }
            
        }else if ([strMenu isEqualToString:@"PERFORMANCE"]){
            [self performSegueWithIdentifier:@"dashboardToPerformanceGraph" sender:self];
        }else if ([strMenu isEqualToString:@"PRODUCT WISE PERFORMANCE"]){
            [self performSegueWithIdentifier:@"dashboardToPerformance" sender:self];
        }else if ([strMenu isEqualToString:@"PAYMENT"]){
            [self performSegueWithIdentifier:@"dashboardToPayments" sender:self];
        }else if ([strMenu isEqualToString:@"SCHEME"]){
            [self performSegueWithIdentifier:@"dashboardToScheme" sender:self];
        }else if ([strMenu isEqualToString:@"TOUR"]){
            [self tourApi];
        }else if ([strMenu isEqualToString:@"ENGAGEMENTS"]){
            [self gameAuthorization];
        }else if ([strMenu isEqualToString:@"RETAILER LIFTING"]){
            
            AppLog(@"-->>%@", [[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"]);
            if (checkUserType(kDealer) == true || checkUserType(kbroker) == true) {
                [self performSegueWithIdentifier:@"dashboardToDealerLiftingHistory" sender:self];
            }else{
                [self performSegueWithIdentifier:@"dashboardToLiftingHistory" sender:self];
            }
            
        }else if ([strMenu isEqualToString:@"REWARDS"]){
            [self getRewards];
            // [self showRewardsPopup];
        }else if ([strMenu isEqualToString:@"POP ORDER"]){
            [self performSegueWithIdentifier:@"dashboardToPopOrder" sender:self];
        }else if ([strMenu isEqualToString:@"MASON LIFTING APPROVAL"]){
            
            NSString *strDealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
            if (strDealerId == nil) {
                UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil message:@"Something went wrong, Please login again." preferredStyle:UIAlertControllerStyleAlert];
                [alert addAction:[UIAlertAction actionWithTitle:@"Ok" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
                    [self logout];
                }]];
                [self presentViewController:alert animated:true completion:nil];
                return;
            }
            
            strWebLink = [NSString stringWithFormat:@"https://starlinkinfluencers.in/web/public/dealer/authenticate?authkey=$2y$10$wAa61qlCON2lRFMsRxobGeynsxG5M/CPHB.Vxt21DLY4dnbmaN9a6&sapcode=%@",[[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"]];
            [self performSegueWithIdentifier:@"dashboardToWebview" sender:@"MASON LIFTING APPROVAL"];
            
        }else if ([strMenu isEqualToString:@"GREETINGS"]){
            [[UIApplication sharedApplication] openURL:[NSURL URLWithString:@"https://greetings.starcement.co.in/"] options:@{} completionHandler:nil];
        }else if ([strMenu isEqualToString:@"PENDING INVOICES"]){
            [self performSegueWithIdentifier:@"dashboardToPendingInvoices" sender:self];
        }else if ([strMenu isEqualToString:@"AGEING"]){
            [self performSegueWithIdentifier:@"dashboardToAgeing" sender:self];
        }else if ([strMenu isEqualToString:@"RSAR LIFTING ALLOCATION"]){
            [self performSegueWithIdentifier:@"rssdDashboardToAllocationHistory" sender:self];
        }else if ([strMenu isEqualToString:@"NEW ORDER ENQUIRY"]){
            [self performSegueWithIdentifier:@"dashboardToNewOrderEnquiry" sender:self];
        }else if ([strMenu isEqualToString:@"ORDER ENQUIRY"]){
            [self performSegueWithIdentifier:@"dashboardToOrderEnquiry" sender:self];
        }
        
    }else if (collectionView == collViewGameBanner) {
        [self gameAuthorization];
    }
}

- (void)scrollViewDidEndDecelerating:(UIScrollView *)scrollView {
    if (scrollView == collViewSlider) {
        pageControlSlider.currentPage = scrollView.contentOffset.x / scrollView.frame.size.width;
    }
}


#pragma mark - UICollectionViewDelegateFlowLayout

-(CGSize) collectionView:(UICollectionView *)collectionView layout:(UICollectionViewLayout *)collectionViewLayout sizeForItemAtIndexPath:(NSIndexPath *)indexPath {
    if (collectionView == collViewSlider) {
        return CGSizeMake(self.view.frame.size.width, 150);
    }else if (collectionView == collViewGameBanner) {
        return CGSizeMake(self.view.frame.size.width, 160);
    }else {
        //return CGSizeMake((self.view.frame.size.width / 2) - 1 , collViewMenu.frame.size.height / 3);
        return CGSizeMake((self.view.frame.size.width / 2) - 1 , 140);
    }
}

#pragma mark - IBAction's

- (IBAction)btnFbClicked:(id)sender {
    [[UIApplication sharedApplication] openURL:[NSURL URLWithString:@"https://m.facebook.com/starcements/"] options:@{} completionHandler:nil];
}

- (IBAction)btnYoutubeClicked:(UIButton *)sender {
    [[UIApplication sharedApplication] openURL:[NSURL URLWithString:@"https://www.youtube.com/channel/UCuKSCQask__yLwCWzLd5uSw"] options:@{} completionHandler:nil];
}

- (IBAction)btnWebClicked:(UIButton *)sender {
    [[UIApplication sharedApplication] openURL:[NSURL URLWithString:@"http://starcement.co.in/"] options:@{} completionHandler:nil];
}

- (IBAction)btnDashboardClicked:(UIButton *)sender {
    [[UIApplication sharedApplication] openURL:[NSURL URLWithString:@"https://starsaathi.com/SAP/dashboard/"] options:@{} completionHandler:nil];
}

#pragma mark - Navigation Item Action

- (IBAction)showMenu:(UIBarButtonItem *)sender {
    
    [self.view endEditing:YES];
    [self.frostedViewController.view endEditing:YES];
    
    [self.frostedViewController presentMenuViewController];
}

- (IBAction)btnCallClicked:(UIBarButtonItem *)sender {
    /*
     NSString *phoneNumber = [@"tel://" stringByAppendingString:@"180034534500"];
     [[UIApplication sharedApplication] openURL:[NSURL URLWithString:phoneNumber]];
     */
}

- (IBAction)btnEmailClicked:(UIBarButtonItem *)sender {
    
    /*
     
     // Check if your app support the email.
     if ([MFMailComposeViewController canSendMail]) {
     // Email Subject
     NSString *emailTitle = @"";
     // Email Content
     NSString *messageBody = @"";
     // To address
     NSArray *toRecipents = [NSArray arrayWithObject:@"customercare@starcement.co.in"];
     
     MFMailComposeViewController *mc = [[MFMailComposeViewController alloc] init];
     mc.mailComposeDelegate = self;
     [mc setSubject:emailTitle];
     [mc setMessageBody:messageBody isHTML:NO];
     [mc setToRecipients:toRecipents];
     
     // Present mail view controller on screen
     [self presentViewController:mc animated:YES completion:NULL];
     }
     
     */
}

- (void)btnNotificationClicked:(UIButton *)sender {
    AppLog(@"Hello");
    [self performSegueWithIdentifier:@"dashboardToNotification" sender:self];
}

- (void)btnSyncClicked:(UIBarButtonItem *)sender {
    
    [self removeDatabase];
    [self createAndCheckDatabase];
    [self downloadTableStructureWithBlock:^{
        AppLog(@"Table Structure is ready, Insert data now");
        
        if (APP_DELEGATE.isServerReachable) {
            
            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
            FMDatabase *db = [FMDatabase databaseWithPath:path];
            if ([db open]) {
                FMResultSet *s = [db executeQuery:@"SELECT last_download_time FROM data_download_log where table_name = 'download_dictionary'"];
                while ([s next]) {
                    strDataDownloadTime = [s stringForColumn:@"last_download_time"];
                    strDataDownloadTime = [NSString handleSpecialSymbol:strDataDownloadTime];
                }
                [db close];
            }
            
            counter = 0;
            
            NSMutableDictionary *dictDD = [[NSMutableDictionary alloc]init];
            [dictDD setValue:@"START"                                     forKey:@"nick_name"];
            [dictDD setValue:APP_CONSTANTS.strCustCode                    forKey:@"emp_code"];
            [dictDD setValue:@"no"                                        forKey:@"incremental_download"];
            [dictDD setValue:strDataDownloadTime                          forKey:@"last_update_time"];
            [dictDD setValue:[DeviceId GetDeviceID]                       forKey:@"device_id"];
            [dictDD setValue:[defaults valueForKey:@"user_type"]          forKey:@"user_tyoe"];
            
            [SVProgressHUD show];
            [XMLParser callAPIWithURL:@"https://starsaathi.com/SAP/datadownloaddictionary_v2-7.0.4.php" parameters:dictDD completion:^(NSData *data){
                [SVProgressHUD dismiss];
                if ( data ){
                    
                    AppLog(@"-->>%@",[[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding]);
                    
                    NSArray   *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
                    NSString  *documentsDirectory = [paths objectAtIndex:0];
                    NSString  *filePath = [NSString stringWithFormat:@"%@/%@", documentsDirectory,@"datadownloaddictionary.txt"];
                    [data writeToFile:filePath atomically:YES];
                    
                    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
                    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
                    
                    AppLog(@"-->>%@",arrContent);
                    AppLog(@"\n Result = %@",stringContent);
                    
                    NSArray *arrTable = @[@"menu_details",
                                          @"user_details",
                                          @"order_details",
                                          @"customer_master",
                                          @"emp_master",
                                          @"menu_access",
                                          @"self_appraisal_product_wise",
                                          @"product_master",
                                          @"destination_master",
                                          @"product_details",
                                          @"branch_master",
                                          @"branch_schemes_PDF",
                                          @"branch_dump"];
                    
                    NSMutableDictionary *dictUrl = [[NSMutableDictionary alloc]init];
                    [dictUrl setValue:@"menu-details-incremental_v2-8.0.2.php"                  forKey:@"menu_details"];
                    [dictUrl setValue:@"user-details-incremental_v2-6.0.7.php"                  forKey:@"user_details"];
                    [dictUrl setValue:@"order-form-details-incremental-7.1.0.php"               forKey:@"order_details"];
                    [dictUrl setValue:@"customer-master-audit-txt-incremental_v2-7.0.11.php"    forKey:@"customer_master"];
                    [dictUrl setValue:@"emp-master-txt-6.0.4.php"                               forKey:@"emp_master"];
                    [dictUrl setValue:@"menu-access-txt_v2-6.0.1.php"                           forKey:@"menu_access"];
                    [dictUrl setValue:@"product-wise-target-achievement-txt_v2-6.0.2.php"       forKey:@"self_appraisal_product_wise"];
                    [dictUrl setValue:@"branchwise-product-data-download-v2.php"                forKey:@"product_master"];
                    [dictUrl setValue:@"destination-master-txt_v2-6.0.4.php"                    forKey:@"destination_master"];
                    [dictUrl setValue:@"setup-product-details-incremental-6.0.7.php"            forKey:@"product_details"];
                    [dictUrl setValue:@"branch-master-txt_v2-6.0.3.php"                         forKey:@"branch_master"];
                    [dictUrl setValue:@"branchwise-scheme-download-txt_v2-6.0.1.php"            forKey:@"branch_schemes_PDF"];
                    [dictUrl setValue:@"branch-dump-master-txt_v2-6.0.3.php"                    forKey:@"branch_dump"];
                    
                    NSString *strDateTime = [arrContent objectAtIndex:0];
                    if (strDateTime.length > 3) {
                        [SVProgressHUD show];
                        [arrContent enumerateObjectsUsingBlock:^(NSString *strTable, NSUInteger idx, BOOL *stop){
                            if (idx == 0 || ![arrTable containsObject:strTable]) {
                                return ;
                            }else{
                                [self insertDataUsingTableName:strTable remainingUrl:[dictUrl safeValueeForKey:strTable]];
                            }
                        }];
                    }
                }
            }];
            
            
        }else
            UDShowToastAlertWithTitle(kNoInternet, 2);
        
    }];
}

- (void)btnFilterClicked:(UIBarButtonItem *)sender {
    [self performSegueWithIdentifier:@"dashboardToDealerList" sender:self];
}

#pragma mark - MFMailComposeViewControllerDelegate

- (void) mailComposeController:(MFMailComposeViewController *)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError *)error {
    switch (result)
    {
        case MFMailComposeResultCancelled:
            AppLog(@"Mail cancelled");
            break;
        case MFMailComposeResultSaved:
            AppLog(@"Mail saved");
            break;
        case MFMailComposeResultSent:
            AppLog(@"Mail sent");
            break;
        case MFMailComposeResultFailed:
            AppLog(@"Mail sent failure: %@", [error localizedDescription]);
            break;
        default:
            break;
    }
    
    // Close the Mail Interface
    [self dismissViewControllerAnimated:YES completion:NULL];
}

#pragma mark - Web Service Method

-(void)tourApi {
    if (APP_DELEGATE.isServerReachable) {
        [SVProgressHUD showWithStatus:@"Loading..."];
        
        NSMutableDictionary *dict  = [[NSMutableDictionary alloc]init];
        [dict setValue:[defaults valueForKey:@"emp_code"] forKey:@"emp_code"];
        [dict setValue:[defaults valueForKey:@"user_type"] forKey:@"user_type"];
        
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/dealer-wise-tour-data-download.php" parameters:dict completion:^(NSDictionary *dictResponse){
            [SVProgressHUD dismiss];
            if ([[dictResponse safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
                strWebLink = [[[dictResponse safeValueeForKey:@"tour_data"] objectAtIndex:0] safeValueeForKey:@"tour_link"];
                [self performSegueWithIdentifier:@"dashboardToWebview" sender:@"Tour"];
            }else
                UDShowToastAlertWithTitle(@"Coming soon!", 2);
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)gameAuthorization {
    if (APP_DELEGATE.isServerReachable) {
        [SVProgressHUD showWithStatus:@"Loading..."];
        
        NSMutableDictionary *dict  = [[NSMutableDictionary alloc]init];
        [dict setValue:[defaults valueForKey:@"phone_number"] forKey:@"mobileNumber"];
        [dict setValue:[self randomAlphanumericStringWithLength:16] forKey:@"sessionToken"];
        
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/game_authorization.php" parameters:dict completion:^(NSDictionary *dictResponse){
            [SVProgressHUD dismiss];
            
            if ([[dictResponse safeValueeForKey:@"process_status"] isEqualToString:@"NO"]) {
                UDShowToastAlertWithTitle([dictResponse safeValueeForKey:@"process_message"], 2);
            }else{
                strWebLink = [[dictResponse safeValueeForKey:@"data"] safeValueeForKey:@"launchURL"];
                [self performSegueWithIdentifier:@"dashboardToWebview" sender:@"Engagements"];
            }
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)loadGameBanner {
    
    if (APP_DELEGATE.isServerReachable) {
        [SVProgressHUD showWithStatus:@"Loading..."];
        
        NSMutableDictionary *dict  = [[NSMutableDictionary alloc]init];
        [dict setValue:[defaults valueForKey:@"emp_code"] forKey:@"emp_code"];
        [dict setValue:[defaults valueForKey:@"user_type"] forKey:@"user_type"];
        
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/branchwise-game-banner-download.php" parameters:dict completion:^(NSDictionary *dictResponse){
            [SVProgressHUD dismiss];
            if ([[dictResponse safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
                arrGameSlider = [dictResponse safeValueeForKey:@"banner_data"];
                [collViewGameBanner reloadData];
            }else
                UDShowToastAlertWithTitle([dictResponse safeValueeForKey:@"process_message"], 2);
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
}

-(void)loadSlider{
    
    if (APP_DELEGATE.isServerReachable) {
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_slider.php" parameters:nil completion:^(NSDictionary *dictResponse){
            [SVProgressHUD dismiss];
            if ([[dictResponse safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
                arrSlider = [dictResponse safeValueeForKey:@"start_slider_data"];
                if (arrSlider.count > 0) {
                    pageControlSlider.numberOfPages = arrSlider.count;
                    pageControlSlider.currentPage = 0;
                    pageControlSlider.hidden = false;
                    imgViewLogo.hidden = true;
                    NSTimer *timer = [NSTimer scheduledTimerWithTimeInterval:2.0 target:self selector:@selector(changeSliderImage) userInfo:nil repeats:YES];
                    [[NSRunLoop currentRunLoop] addTimer:timer forMode:NSRunLoopCommonModes];
                }
                [collViewSlider reloadData];
            }else
                UDShowToastAlertWithTitle([dictResponse safeValueeForKey:@"process_message"], 2);
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
}

-(void)updateDeviceToken {
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict  = [[NSMutableDictionary alloc]init];
        [dict setValue:[DeviceId GetDeviceID]       forKey:@"deviceId"];
        [dict setValue:[defaults valueForKey:@"emp_code"] forKey:@"emp_code"];
        [dict setValue:APP_DELEGATE.strDeviceToken  forKey:@"registrationid"];
        [dict setValue:@"START"                     forKey:@"nick_name"];
        [dict setValue:@"IOS"                       forKey:@"device_type"];
        [dict setValue:APP_DELEGATE.strDeviceAppVersion  forKey:@"app_version"];
        [dict setValue:[defaults valueForKey:@"user_type"] forKey:@"user_type"];
        
        
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/updateRegistrationId_v2-6.0.0.php" parameters:dict completion:^(NSDictionary *dictResponse){
            
            APP_DELEGATE.strBranchWisePGRollOut = [dictResponse valueForKey:@"branch_wise_pg_rollout"];
            
            if ([[dictResponse valueForKey:@"force_logout"] isEqualToString:@"YES"]) {
                
                NSString *strUrl = [NSString stringWithFormat:@"https://starsaathi.com/SAP/acedns_star_clear_allocation_by_id.php?the_id=%@",[defaults valueForKey:@"emp_code"]];
                
                [XMLParser callServiceWithPostData:strUrl withParam:nil success:^(NSData *data){
                    NSError *jsonParserError;
                    NSDictionary *jsonData = [NSJSONSerialization JSONObjectWithData:data
                                                                             options:NSJSONReadingMutableLeaves error:&jsonParserError];
                    if ([[jsonData valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                        [self logout];
                    }else{
                        UDShowToastAlertWithTitle([jsonData safeValueeForKey:@"process_message"], 2);
                    }
                    
                } failed:^(NSString *strErrMsg){
                    UDShowToastAlertWithTitle(strErrMsg, 2);
                }];
            }
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
}

-(void)wsCheckAppVersion {
    if (APP_DELEGATE.isServerReachable) {
        
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/show_latest_app_version_v1.php" parameters:nil completion:^(NSDictionary *dictResponse){
            if ([[dictResponse safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
                APP_DELEGATE.strServerAppVersion = [dictResponse safeValueeForKey:@"ios_app_version"];
                APP_DELEGATE.strDateForNewOrderEnq = [dictResponse safeValueeForKey:@"current_date"];
                
                NSLog(@"-->>Sanjeet : %@",APP_DELEGATE.strDateForNewOrderEnq);
                
                float fltAppVersion = [APP_DELEGATE.strDeviceAppVersion floatValue];
                float fltServerVersion = [APP_DELEGATE.strServerAppVersion floatValue];
                
                if (fltServerVersion > fltAppVersion) {
                    dispatch_async(dispatch_get_main_queue(), ^{
                        
                        UIAlertController *alertUpdate = [UIAlertController alertControllerWithTitle:@"Star Saathi" message:@"Update Application" preferredStyle:UIAlertControllerStyleAlert];
                        [alertUpdate addAction:[UIAlertAction actionWithTitle:@"OK" style:UIAlertActionStyleDefault handler:^(UIAlertAction *action){
                            NSString *strUrl = [NSString stringWithFormat:@"https://itunes.apple.com/in/app/star-saathi/id%@?mt=8",@"6754075343"];
                            UIApplication *application = [UIApplication sharedApplication];
                            [application openURL:[NSURL URLWithString:strUrl] options:@{} completionHandler:nil];
                        }]];
                        [self presentViewController:alertUpdate animated:YES completion:nil];
                        
                    });
                }
                
            }
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)wsDetinationMaster:(NSString*)strCustomerCode {
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    
    NSString *strLastUpdateTime;
    if ([db open]) {
        
        [db executeUpdate:@"DELETE from destination_master"];
        
        FMResultSet *s = [db executeQuery:@"SELECT last_download_time FROM data_download_log where table_name = 'destination_master'"];
        while ([s next]) {
            strLastUpdateTime = [s stringForColumn:@"last_download_time"];
            strLastUpdateTime = [NSString handleSpecialSymbol:strLastUpdateTime]; // Handlin euro sign
        }
        
        FMResultSet *set = [db executeQuery:@"SELECT last_download_time FROM data_download_log where table_name = 'download_dictionary'"];
        while ([set next]) {
            strDataDownloadTime = [set stringForColumn:@"last_download_time"];
            strDataDownloadTime = [NSString handleSpecialSymbol:strDataDownloadTime];
        }
        
        [db close];
    }
    
    NSMutableDictionary *dictParam = [[NSMutableDictionary alloc]init];
    [dictParam setValue:@"START"                                forKey:@"nick_name"];
    [dictParam setValue:strCustomerCode                         forKey:@"emp_code"];
    [dictParam setValue:@"no"                                   forKey:@"incremental_download"];
    [dictParam setValue:strLastUpdateTime                       forKey:@"last_update_time"];
    [dictParam setValue:strDataDownloadTime                     forKey:@"data_download_time"];
    
    if (checkUserType(kbroker) == true) {
        [dictParam setValue:[defaults valueForKey:@"dns_broker_id"] forKey:@"broker_id"];
    }
    
    
    [SVProgressHUD show];
    NSString *strFinalUrl = @"https://starsaathi.com/SAP/destination-master-txt_v2-6.0.4.php";
    [XMLParser callServiceWithPostData:strFinalUrl withParam:dictParam success:^(NSData *data){
        [SVProgressHUD dismiss];
        if (data) {
            NSArray  *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
            NSString *documentsDirectory = [paths objectAtIndex:0];
            NSString *fileName = [NSString stringWithFormat:@"%@.txt",@"destination_master"];
            NSString *filePath = [NSString stringWithFormat:@"%@/%@", documentsDirectory,fileName];
            [data writeToFile:filePath atomically:YES];
            [self insertDestinationMasterWithFilePath:filePath];
        }else{
            //AppLog(@"->aabrakadaabra : %@",strTblName);
        }
    }failed:^(NSString *strErrorMsg){
        AppLog(@"Send to the login page.");
        [SVProgressHUD dismiss];
        UDShowToastAlertWithTitle(strErrorMsg, 2);
        [self.navigationController popViewControllerAnimated:YES];
    }];
}

-(void)wsDumpMaster:(NSString*)strCustomerCode {
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    
    NSString *strLastUpdateTime;
    if ([db open]) {
        
        [db executeUpdate:@"DELETE from branch_dump"];
        
        FMResultSet *s = [db executeQuery:@"SELECT last_download_time FROM data_download_log where table_name = 'branch_dump'"];
        while ([s next]) {
            strLastUpdateTime = [s stringForColumn:@"last_download_time"];
            strLastUpdateTime = [NSString handleSpecialSymbol:strLastUpdateTime]; // Handlin euro sign
        }
        
        FMResultSet *set = [db executeQuery:@"SELECT last_download_time FROM data_download_log where table_name = 'download_dictionary'"];
        while ([set next]) {
            strDataDownloadTime = [set stringForColumn:@"last_download_time"];
            strDataDownloadTime = [NSString handleSpecialSymbol:strDataDownloadTime];
        }
        
        [db close];
    }
    
    NSMutableDictionary *dictParam = [[NSMutableDictionary alloc]init];
    [dictParam setValue:@"START"                                forKey:@"nick_name"];
    [dictParam setValue:strCustomerCode                         forKey:@"emp_code"];
    [dictParam setValue:@"no"                                   forKey:@"incremental_download"];
    [dictParam setValue:strLastUpdateTime                       forKey:@"last_update_time"];
    [dictParam setValue:strDataDownloadTime                     forKey:@"data_download_time"];
    
    [SVProgressHUD show];
    NSString *strFinalUrl = @"https://starsaathi.com/SAP/branch-dump-master-txt_v2-6.0.3.php";
    [XMLParser callServiceWithPostData:strFinalUrl withParam:dictParam success:^(NSData *data){
        [SVProgressHUD dismiss];
        if (data) {
            NSArray  *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
            NSString *documentsDirectory = [paths objectAtIndex:0];
            NSString *fileName = [NSString stringWithFormat:@"%@.txt",@"branch_dump"];
            NSString *filePath = [NSString stringWithFormat:@"%@/%@", documentsDirectory,fileName];
            [data writeToFile:filePath atomically:YES];
            [self insertBranchDumpWithFilePath:filePath];
        }else{
            //AppLog(@"->aabrakadaabra : %@",strTblName);
        }
    }failed:^(NSString *strErrorMsg){
        AppLog(@"Send to the login page.");
        [SVProgressHUD dismiss];
        UDShowToastAlertWithTitle(strErrorMsg, 2);
        [self.navigationController popViewControllerAnimated:YES];
    }];
}

-(void)getNotification:(NSString*)strDatetime{
    if (APP_DELEGATE.isServerReachable) {
        
        NSString *strBranchCode;
        if ([APP_CONSTANTS.db open]) {
            strBranchCode = [APP_CONSTANTS.db stringForQuery:@"SELECT branch_code FROM customer_master where customer_code = ?",APP_CONSTANTS.strCustCode];
            [APP_CONSTANTS.db close];
        }
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode    forKey:@"the_id"];
        [dict setValue:strBranchCode                forKey:@"the_branch_code"];
        
        //[SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_show_notifications.php" parameters:dict completion:^(NSDictionary *dictResponse){
            //[SVProgressHUD dismiss];
            if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                [self insertNotification:[dictResponse safeValueeForKey:@"notification_data"] dateTime:@"curr_date_time"];
            }
        }];
        
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

- (void)showRewardsPopup {
    // 🔹 Add loader view on top of everything
    [SVProgressHUD show];
    
    // Disable user interaction while loading
    self.view.userInteractionEnabled = NO;

    NSURL *url = [NSURL URLWithString:@"https://dev.starstellar.com/terms_api.php"];
    NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithURL:url
                                                             completionHandler:^(NSData *data, NSURLResponse *response, NSError *error) {
        dispatch_async(dispatch_get_main_queue(), ^{
            // ✅ Always stop loader (success or failure)
             [SVProgressHUD dismiss];
        });

        if (error || !data) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showToastMessage:@"Failed to load terms."];
            });
            return;
        }

        NSError *jsonError;
        NSDictionary *json = [NSJSONSerialization JSONObjectWithData:data options:0 error:&jsonError];
        if (jsonError || ![json isKindOfClass:[NSDictionary class]]) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showToastMessage:@"Invalid response."];
            });
            return;
        }

        NSString *status = json[@"status"];
        NSString *content = json[@"content"];
        NSString *link = json[@"link"];

        if (![status isEqualToString:@"success"] || content.length == 0) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showToastMessage:@"No terms available."];
            });
            return;
        }

        // ✅ Show popup after hiding loader
        dispatch_async(dispatch_get_main_queue(), ^{
            [self presentRewardsPopupWithContent:content link:link];
        });
    }];
    [task resume];
}

- (void)presentRewardsPopupWithContent:(NSString *)content link:(NSString *)link {
    // Background overlay
    UIView *backgroundView = [[UIView alloc] initWithFrame:self.view.bounds];
    backgroundView.backgroundColor = [[UIColor blackColor] colorWithAlphaComponent:0.6];
    backgroundView.tag = 999;
    [self.view addSubview:backgroundView];
    
    // Add tap gesture to detect outside tap
    UITapGestureRecognizer *tapGesture = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(dismissPopupOnOutsideTap:)];
    tapGesture.delegate = self; // <-- important
    [backgroundView addGestureRecognizer:tapGesture];
    
    // Popup container
    UIView *popupView = [[UIView alloc] initWithFrame:CGRectMake(30, 0, self.view.frame.size.width - 60, 320)];
    popupView.backgroundColor = [UIColor whiteColor];
    popupView.layer.cornerRadius = 12;
    popupView.center = self.view.center;
    popupView.tag = 1001;
    [backgroundView addSubview:popupView];
    
    // Title
    UILabel *titleLabel = [[UILabel alloc] initWithFrame:CGRectMake(16, 20, popupView.frame.size.width - 32, 24)];
    titleLabel.text = @"Terms & Condition";
    titleLabel.textAlignment = NSTextAlignmentCenter;
    titleLabel.font = [UIFont boldSystemFontOfSize:18];
    [popupView addSubview:titleLabel];
    
    // Message
    UILabel *messageLabel = [[UILabel alloc] initWithFrame:CGRectMake(16, CGRectGetMaxY(titleLabel.frame) + 12, popupView.frame.size.width - 32, 80)];
    messageLabel.text = content;
    messageLabel.textAlignment = NSTextAlignmentCenter;
    messageLabel.numberOfLines = 5;
    messageLabel.font = [UIFont systemFontOfSize:15];
    [popupView addSubview:messageLabel];
    
    // "Read More" button
    UIButton *readMoreButton = [UIButton buttonWithType:UIButtonTypeSystem];
    readMoreButton.frame = CGRectMake(0, CGRectGetMaxY(messageLabel.frame) + 5, popupView.frame.size.width, 20);
    [readMoreButton setTitle:@"Read More" forState:UIControlStateNormal];
    [readMoreButton setTitleColor:[UIColor systemBlueColor] forState:UIControlStateNormal];
    readMoreButton.titleLabel.font = [UIFont systemFontOfSize:14 weight:UIFontWeightMedium];
    [readMoreButton addTarget:self action:@selector(openTermsLink:) forControlEvents:UIControlEventTouchUpInside];
    readMoreButton.tag = 1234;
    readMoreButton.accessibilityHint = link;
    [popupView addSubview:readMoreButton];
    
    // Checkbox
    UIButton *checkbox = [UIButton buttonWithType:UIButtonTypeCustom];
    checkbox.frame = CGRectMake(30, CGRectGetMaxY(readMoreButton.frame) + 15, 24, 24);
    [checkbox setImage:[UIImage imageNamed:@"uncheck"] forState:UIControlStateNormal];
    [checkbox setImage:[UIImage imageNamed:@"check"] forState:UIControlStateSelected];
    [checkbox addTarget:self action:@selector(toggleCheckbox:) forControlEvents:UIControlEventTouchUpInside];
    [popupView addSubview:checkbox];
    
    // Label next to checkbox
    UILabel *acceptLabel = [[UILabel alloc] initWithFrame:CGRectMake(CGRectGetMaxX(checkbox.frame) + 10, checkbox.frame.origin.y, popupView.frame.size.width - 80, 24)];
    acceptLabel.text = @"Accept";
    acceptLabel.font = [UIFont systemFontOfSize:14];
    [popupView addSubview:acceptLabel];
    
    // Submit button
    UIButton *submitButton = [UIButton buttonWithType:UIButtonTypeSystem];
    submitButton.frame = CGRectMake(40, CGRectGetMaxY(checkbox.frame) + 20, popupView.frame.size.width - 80, 44);
    [submitButton setTitle:@"Submit" forState:UIControlStateNormal];
    submitButton.backgroundColor =[UIColor colorWithDynamicProvider:^UIColor * (UITraitCollection *traitCollection) {
        if (traitCollection.userInterfaceStyle == UIUserInterfaceStyleDark) {
            return [UIColor colorWithRed:1.0 green:0.25 blue:0.25 alpha:1.0];
        } else {
            return [UIColor colorWithRed:0.878 green:0.0 blue:0.0 alpha:1.0];
        }
    }];
    [submitButton setTitleColor:[UIColor whiteColor] forState:UIControlStateNormal];
    submitButton.layer.cornerRadius = 8;
    submitButton.titleLabel.font = [UIFont boldSystemFontOfSize:16];
    [submitButton addTarget:self action:@selector(rewardsSubmitTapped:) forControlEvents:UIControlEventTouchUpInside];
    [popupView addSubview:submitButton];
}

- (BOOL)gestureRecognizer:(UIGestureRecognizer *)gestureRecognizer shouldReceiveTouch:(UITouch *)touch {
    UIView *backgroundView = gestureRecognizer.view;
    UIView *popupView = [backgroundView viewWithTag:1001];
    
    CGPoint touchLocation = [touch locationInView:backgroundView];
    
    if (CGRectContainsPoint(popupView.frame, touchLocation)) {
        return NO; // ignore taps inside popup
    }
    return YES; // allow taps outside popup
}

- (void)dismissPopupOnOutsideTap:(UITapGestureRecognizer *)gesture {
    UIView *backgroundView = gesture.view;
    [backgroundView removeFromSuperview];
}

- (void)openTermsLink:(UIButton *)sender {
    NSString *urlString = sender.accessibilityHint;
    if (urlString.length > 0) {
        NSURL *url = [NSURL URLWithString:urlString];
        if (url && [[UIApplication sharedApplication] canOpenURL:url]) {
            [[UIApplication sharedApplication] openURL:url options:@{} completionHandler:nil];
        }
    }
}

- (void)toggleCheckbox:(UIButton *)sender {
    sender.selected = !sender.selected;
}

- (void)rewardsSubmitTapped:(UIButton *)sender {
    UIView *backgroundView = [self.view viewWithTag:999];
    
    // Find the checkbox inside popup
    UIButton *checkbox = nil;
    for (UIView *subview in backgroundView.subviews.firstObject.subviews) {
        if ([subview isKindOfClass:[UIButton class]] && subview.frame.size.width == 24) {
            checkbox = (UIButton *)subview;
            break;
        }
    }
    
    // Check selection
    if (checkbox && !checkbox.isSelected) {
        [self showToastMessage:@"Please accept the terms before submitting."];
        return;
    }
    
    // Remove popup
    [backgroundView removeFromSuperview];
    
    // ✅ Proceed to rewards if accepted
    [self getRewards];
}

- (void)showToastMessage:(NSString *)message {
    UILabel *toastLabel = [[UILabel alloc] initWithFrame:CGRectZero];
    toastLabel.text = message;
    toastLabel.textColor = [UIColor whiteColor];
    toastLabel.backgroundColor = [[UIColor blackColor] colorWithAlphaComponent:0.7];
    toastLabel.textAlignment = NSTextAlignmentCenter;
    toastLabel.font = [UIFont systemFontOfSize:14 weight:UIFontWeightMedium];
    toastLabel.numberOfLines = 0;
    toastLabel.layer.cornerRadius = 8;
    toastLabel.clipsToBounds = YES;
    
    CGFloat maxWidth = self.view.frame.size.width - 60;
    CGSize textSize = [toastLabel sizeThatFits:CGSizeMake(maxWidth, CGFLOAT_MAX)];
    toastLabel.frame = CGRectMake(30,
                                  self.view.frame.size.height - 120,
                                  self.view.frame.size.width - 60,
                                  textSize.height + 16);
    
    [self.view addSubview:toastLabel];
    toastLabel.alpha = 0;
    
    [UIView animateWithDuration:0.3 animations:^{
        toastLabel.alpha = 1;
    } completion:^(BOOL finished) {
        [UIView animateWithDuration:0.3 delay:2.0 options:UIViewAnimationOptionCurveEaseOut animations:^{
            toastLabel.alpha = 0;
        } completion:^(BOOL finished) {
            [toastLabel removeFromSuperview];
        }];
    }];
}

-(void)getRewards{
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"] forKey:@"emp_code"];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"user_type"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/dealer-wise-rewards_test.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    NSString *strRewardLink = [[[dictResponse safeValueeForKey:@"reward_data"] objectAtIndex:0] safeValueeForKey:@"reward_link"];
                    strRewardLink = [NSString handleSpecialSymbol:strRewardLink];
                    [self performSegueWithIdentifier:@"dashboardToRewards" sender:strRewardLink];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

- (void)downloadTableStructureWithBlock:(void (^)(void))complitionHandler {
    
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"https://starsaathi.com/SAP/user-details-incremental-6.0.0.php?nick_name=%@&mode=%@", @"START", @"SETUP"]];
    [SVProgressHUD show];
    [XMLParser downloadDataFromURL:url withCompletionHandler:^(NSData *data){
        [SVProgressHUD dismiss];
        if (data != nil) {
            NSError *parseError = nil;
            NSDictionary *xmlDictionary = [XMLReader dictionaryForXMLData:data error:&parseError];
            AppLog(@"-->>%@",xmlDictionary);
        }
        
        NSURL *urlTblStructure = [NSURL URLWithString:[NSString stringWithFormat:@"https://starsaathi.com/SAP/table-structure-details-6.0.2.php?nick_name=%@&mode=%@&device_id=%@emp_code=%@", @"START", @"INSTALL", [DeviceId GetDeviceID], @""]];
        [SVProgressHUD show];
        [XMLParser downloadDataFromURL:urlTblStructure withCompletionHandler:^(NSData *data){
            [SVProgressHUD dismiss];
            if (data != nil) {
                NSError *parseError = nil;
                NSDictionary *xmlDictionary = [XMLReader dictionaryForXMLData:data error:&parseError];
                AppLog(@"%@",xmlDictionary);
                NSDictionary *dict = [XMLReader dictionaryForXMLData:data
                                                             options:XMLReaderOptionsProcessNamespaces
                                                               error:&parseError];
                
                NSArray *arr = dict[@"recordset"][@"data"];
                AppLog(@"%@",arr);
                
                NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
                FMDatabase *db = [FMDatabase databaseWithPath:path];
                
                for (NSDictionary *dictt in arr) {
                    AppLog(@"-->>%@",dictt[@"table_structure"][@"text"]);
                    NSString *strQuery = dictt[@"table_structure"][@"text"];
                    if ([db open]) {
                        BOOL success = [db executeStatements:strQuery];
                        AppLog(@"-->>%d",success);
                    }
                }
                
                [defaults setBool:YES forKey:@"tablestructure"];
                [defaults synchronize];
                
                complitionHandler();
            }
        }];
    }];
}

-(void)wsDealerWiseCreditLimit:(NSString*)strSapCode {
    if (APP_DELEGATE.isServerReachable) {
        
        APP_CONSTANTS.dateFormater.dateFormat = @"yyyy-MM-dd";
        NSDate *date = [NSDate date];
        NSString *strTodayDate = [APP_CONSTANTS.dateFormater stringFromDate:date];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:strSapCode forKey:@"customer_code"];
        [dict setValue:@"2022-07-31" forKey:@"from_date"];
        [dict setValue:strTodayDate  forKey:@"to_date"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/dealerwise-credit-limit-s-deposit-v2.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    lblOutstandingBal.text = [NSString stringWithFormat:@"₹%@",[dictResponse safeValueeForKey:@"credit_expose"]];
                    lblCreditLimit.text = [NSString stringWithFormat:@"₹%@",[dictResponse safeValueeForKey:@"credit_limit"]];
                    lblScl.text = [NSString stringWithFormat:@"₹%@",[dictResponse safeValueeForKey:@"Lcamt_1010"]];
                    lblScnel.text = [NSString stringWithFormat:@"₹%@",[dictResponse safeValueeForKey:@"Lcamt_1017"]];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)wsSaveAppUsage:(NSString*)strMenuName{
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[defaults valueForKey:@"kUserLoginId"] forKey:@"customer_id"];
        [dict setValue:strMenuName forKey:@"webservice_name"];
        
        //[SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/save_app_usage_details.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                //[SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    AppLog(@"-->>%@",strMenuName);
                    AppLog(@"-->>%@",[dictResponse safeValueeForKey:@"process_message"]);
                }else{
                    AppLog(@"-->>%@",[dictResponse safeValueeForKey:@"process_message"]);
                }
            });
        }];
    }else{
        AppLog(@"-->>%@",kNoInternet);
    }
}

#pragma mark - Database Method

-(void)insertNotification:(NSArray*)arrNotification dateTime:(NSString*)strDatetime{
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        
        for (NSDictionary *dict in arrNotification) {
            
            BOOL success = [db executeUpdate:@"INSERT INTO notification(notification_id, notification_title, notification_message, notification_image_link, notification_datetime, status, notification_file_type) VALUES(?, ?, ?, ?, ?, ?, ?)",
                            [dict safeValueeForKey:@"nid"],
                            [dict safeValueeForKey:@"m_title"],
                            [dict safeValueeForKey:@"m_message"],
                            [dict safeValueeForKey:@"m_image_link"],
                            [dict safeValueeForKey:@"n_date_time"],
                            [dict safeValueeForKey:@"the_noti_sts"],
                            [dict safeValueeForKey:@"m_file_type"]];
            
            if (!success) {
                AppLog(@"error = %@", [db lastErrorMessage]);
            }
        }
        [db close];
        barButtonNotification.badgeValue = [self notificationCount];
    }
}

- (void)createAndCheckDatabase {
    
    BOOL success;
    NSError *error;
    
    NSFileManager *fileManager = [NSFileManager defaultManager];
    
    NSString *documentsDirectory = [NSHomeDirectory() stringByAppendingPathComponent:@"Documents"];
    NSString *filePath = [documentsDirectory stringByAppendingPathComponent:@"StarSaathi.db"];
    
    success = [fileManager fileExistsAtPath:filePath];
    if (!success) {
        NSString *path = [[NSBundle mainBundle] pathForResource:@"StarSaathi" ofType:@"db"];
        success = [fileManager copyItemAtPath:path toPath:filePath error:&error];
        
        if (success) {
            AppLog(@"Database Created");
        }
    }
    
}

-(void)removeDatabase {
    BOOL success;
    NSError *error;
    
    NSFileManager *fileManager = [NSFileManager defaultManager];
    
    NSString *documentsDirectory = [NSHomeDirectory() stringByAppendingPathComponent:@"Documents"];
    NSString *filePath = [documentsDirectory stringByAppendingPathComponent:@"StarSaathi.db"];
    
    success = [fileManager fileExistsAtPath:filePath];
    if (success) {
        success = [fileManager removeItemAtPath:filePath error:&error];
    }
    
    if (success) {
        AppLog(@"Database Removed");
    }
}

-(void)insertDataUsingTableName:(NSString*)strTblName remainingUrl:(NSString*)strUrl{
    
    if (checkUserType(kbroker) == true) {
        if ([strTblName isEqualToString:@"destination_master"]) {
            return;
        }
    }
    
    
    
    NSArray *arrXMLDataTable = @[@"menu_details",@"user_details",@"order_details",@"product_details"];
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    
    NSString *strLastUpdateTime;
    if ([db open]) {
        
        NSString *strQuery = [NSString stringWithFormat:@"SELECT last_download_time FROM data_download_log where table_name = '%@'",strTblName];
        
        FMResultSet *s = [db executeQuery:strQuery];
        while ([s next]) {
            strLastUpdateTime = [s stringForColumn:@"last_download_time"];
            strLastUpdateTime = [NSString handleSpecialSymbol:strLastUpdateTime]; // Handlin euro sign
        }
        [db close];
    }
    
    NSMutableDictionary *dictParam = [[NSMutableDictionary alloc]init];
    
    NSString *strEmpCode = [defaults valueForKey:@"emp_code"];
    
    if ([strTblName isEqualToString:@"menu_details"] || [strTblName isEqualToString:@"user_details"] || [strTblName isEqualToString:@"order_details"] || [strTblName isEqualToString:@"product_details"]) {
        
        if ([strTblName isEqualToString:@"menu_details"] || [strTblName isEqualToString:@"user_details"]) {
            [dictParam setValue:[defaults valueForKey:@"user_type"] forKey:@"user_type"];
        }
        
        [dictParam setValue:@"START"                                forKey:@"nick_name"];
        [dictParam setValue:@"SETUP"                                forKey:@"mode"];
        [dictParam setValue:strEmpCode                              forKey:@"emp_code"];
        [dictParam setValue:@"no"                                   forKey:@"incremental_download"];
        [dictParam setValue:strLastUpdateTime                       forKey:@"last_update_time"];
        
    }else if ([strTblName isEqualToString:@"customer_master"] || [strTblName isEqualToString:@"product_master"] || [strTblName isEqualToString:@"emp_master"] || [strTblName isEqualToString:@"destination_master"] || [strTblName isEqualToString:@"branch_master"] || [strTblName isEqualToString:@"branch_dump"]){
        
        if ([strTblName isEqualToString:@"customer_master"] || [strTblName isEqualToString:@"product_master"] || [strTblName isEqualToString:@"branch_master"]) {
            [dictParam setValue:[defaults valueForKey:@"user_type"] forKey:@"user_type"];
        }
        
        
        
        if ([strTblName isEqualToString:@"destination_master"] && checkUserType(kbroker) == true) {
            [dictParam setValue:[defaults valueForKey:@"dns_broker_id"] forKey:@"broker_id"];
        }
        
        [dictParam setValue:@"START"                                forKey:@"nick_name"];
        [dictParam setValue:strEmpCode                              forKey:@"emp_code"];
        [dictParam setValue:@"no"                                   forKey:@"incremental_download"];
        [dictParam setValue:strLastUpdateTime                       forKey:@"last_update_time"];
        [dictParam setValue:strDataDownloadTime                     forKey:@"data_download_time"];
        
    }else if ([strTblName isEqualToString:@"menu_access"] || [strTblName isEqualToString:@"self_appraisal_product_wise"]){
        
        [dictParam setValue:[defaults valueForKey:@"user_type"]     forKey:@"user_type"];
        [dictParam setValue:@"START"                                forKey:@"nick_name"];
        [dictParam setValue:strEmpCode                              forKey:@"emp_code"];
        
    }else if ([strTblName isEqualToString:@"branch_schemes_PDF"]){
        
        [dictParam setValue:[defaults valueForKey:@"user_type"]     forKey:@"user_type"];
        [dictParam setValue:@"START"                                forKey:@"nick_name"];
        [dictParam setValue:strEmpCode                              forKey:@"emp_code"];
        [dictParam setValue:strLastUpdateTime                       forKey:@"last_update_time"];
        
    }
    
    NSString *strFinalUrl = [NSString stringWithFormat:@"%@%@" ,@"https://starsaathi.com/SAP/", strUrl];
    [XMLParser callServiceWithPostData:strFinalUrl withParam:dictParam success:^(NSData *data){
        
        
        AppLog(@"-->>Downloaded File : %@",strTblName);
        counter += 1;
        BOOL flag = [arrXMLDataTable containsObject:strTblName];
        
        if (flag) {
            if (data != nil) {
                NSError *parseError = nil;
                NSString *strXml = [[NSString alloc]initWithData:data encoding:NSUTF8StringEncoding];
                AppLog(@"XML Log : -->> %@",strXml);
                NSDictionary *xmlDictionary = [XMLReader dictionaryForXMLData:data error:&parseError];
                
                if ([strTblName isEqualToString:@"menu_details"]) {
                    [self insertMenuDetails:xmlDictionary];
                }else if ([strTblName isEqualToString:@"user_details"]){
                    [self insertUserDetails:xmlDictionary];
                }else if ([strTblName isEqualToString:@"product_details"]){
                    [self insertProductDetails:xmlDictionary];
                }else{
                    [self insertOrderFormDetails:xmlDictionary];
                }
                
                AppLog(@"-->>%@",xmlDictionary);
            }
        }else{
            AppLog(@"aabrakadaabra : %@",strTblName);
            if (data) {
                NSArray  *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
                NSString *documentsDirectory = [paths objectAtIndex:0];
                NSString *fileName = [NSString stringWithFormat:@"%@.txt",strTblName];
                NSString *filePath = [NSString stringWithFormat:@"%@/%@", documentsDirectory,fileName];
                [data writeToFile:filePath atomically:YES];
                
                if ([strTblName isEqualToString:@"customer_master"]){
                    [self insertCustomerMasterWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"product_master"]){
                    [self insertProductMasterWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"emp_master"]){
                    [self insertEmployeeMasterWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"menu_access"]){
                    [self insertMenuAccessWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"destination_master"]){
                    [self insertDestinationMasterWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"branch_master"]){
                    [self insertBranchMasterWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"branch_schemes_PDF"]){
                    [self insertBranchSchemePDFWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"self_appraisal_product_wise"]){
                    [self insertSelfAppraisalProductWiseWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"branch_dump"]){
                    [self insertBranchDumpWithFilePath:filePath];
                }
                
            }else{
                AppLog(@"->aabrakadaabra : %@",strTblName);
            }
        }
        
        if (counter == 8) {
            [SVProgressHUD dismiss];
            AppLog(@"All data has been downloaded.");
            AppLog(@"Send to the dashboard page.");
            
            [defaults setBool:YES forKey:@"datadownloaded"];
            [defaults synchronize];
            UDShowToastAlertWithTitle(@"Data sync completed.", 2);
        }
        
    }failed:^(NSString *strErrorMsg){
        AppLog(@"Send to the login page.");
        [SVProgressHUD dismiss];
        UDShowToastAlertWithTitle(strErrorMsg, 2);
        [self.navigationController popViewControllerAnimated:YES];
    }];
    
    
}

-(void)insertMenuDetails:(NSDictionary*)dict{
    
    NSDictionary *dictMD = [[dict safeValueeForKey:@"recordset"] safeValueeForKey:@"data"];
    AppLog(@"-->>%@",[[dictMD safeValueeForKey:@"menu_id"] safeValueeForKey:@"text"]);
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        NSString *strInsertMenuDetails = [NSString stringWithFormat:@"INSERT INTO menu_details VALUES('%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@')",
                                          [[dictMD safeValueeForKey:@"menu_id"]                       safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"user_id"]                       safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"attendance"]                    safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"route_plan"]                    safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"order"]                         safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"collection"]                    safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"stk_audit"]                     safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"business_prospect"]             safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"tour_exp"]                      safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"capture_image"]                 safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"notes_and_info"]                safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"activity_report"]               safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"loyalty"]                       safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"mis_report"]                    safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"delete_transaction"]            safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"loading_freight"]               safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"sauda_allocation"]              safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"survey"]                        safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"product_promotion"]             safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"replacement"]                   safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"market_feedback"]               safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"sauda_allocation_app"]          safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"pending_contract"]              safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"sauda_mis"]                     safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"order_status"]                  safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"checkout"]                      safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"sauda_outstanding"]             safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"sale_performance"]              safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"check_in_out"]                  safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"outstanding"]                   safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"outstanding_ageing"]            safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"target_achievement"]            safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"wholesaler_info"]               safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"self_appraisal"]                safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"yellow_card"]                   safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"catalogue"]                     safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"catalogue_url"]                 safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"tele_tran"]                     safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"TD_allocation_app"]             safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"catalogue_dependency"]          safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"TD_allocation_vertical"]        safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"run_time_TD_approval_vertical"] safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"quotation"]                     safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"CRM_app"]                       safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"ISP"]                           safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"monthly_report_mail"]           safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"retailer_app"]                  safeValueeForKey:@"text"]];
        
        [db executeStatements:strInsertMenuDetails];
        [db close];
    }
}

-(void)insertUserDetails:(NSDictionary*)dict{
    
    NSDictionary *dictUD = [[dict safeValueeForKey:@"recordset"] safeValueeForKey:@"data"];
    AppLog(@"-->>%@",[[dictUD safeValueeForKey:@"user_id"] safeValueeForKey:@"text"]);
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        NSString *strInsertUserDetails = [NSString stringWithFormat:@"INSERT INTO user_details VALUES('%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@')",
                                          [[dictUD safeValueeForKey:@"user_id"]                             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"name"]                                safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"address"]                             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"phone_no"]                            safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"email"]                               safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"license_key"]                         safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"no_users"]                            safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"nick_name"]                           safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"no_of_branches"]                      safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"image"]                               safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"email_hierarchywise"]                 safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"vertical_fields"]                     safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"vertical_fields_value"]               safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"previous_stock"]                      safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"multiple_prospect"]                   safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"multiple_prospect_value"]             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"stock_audit_scan"]                    safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"stock_audit_rate"]                    safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"location_drag_drop"]                  safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"tour_plan_daywise"]                   safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"check_in_out_typeval"]                safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"FCM"]                                 safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"minimum_stock"]                       safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"stk_audit_unit"]                      safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"stk_audit_irrespective_routeplan"]    safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"stk_audit_cust_type"]                 safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"notes_info_hint_remarks"]             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"notes_info_upload_photo"]             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"country"]                             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"time_zone"]                           safeValueeForKey:@"text"]];
        
        [db executeStatements:strInsertUserDetails];
        [db close];
    }
    
}

-(void)insertOrderFormDetails:(NSDictionary*)dict{
    
    NSDictionary *dictOFD = [[dict safeValueeForKey:@"recordset"] safeValueeForKey:@"data"];
    AppLog(@"-->>%@",[[dictOFD safeValueeForKey:@"order_form_id"] safeValueeForKey:@"text"]);
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        NSString *strInsertOrderFormDetails = [NSString stringWithFormat:@"INSERT INTO order_form_details VALUES('%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@')",
                                               [[dictOFD safeValueeForKey:@"order_form_id"]                          safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"user_id"]                                safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"credit_limit"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"cl_stk"]                                 safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"mrp_input_dropdown"]                     safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"mrp"]                                    safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD"]                                     safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_type"]                                safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"tagged_customer_for_business_prospect"]  safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"sale_rate"]                              safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"sale_rate_input_dropdown"]               safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"attached_printer"]                       safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"printer_mandatory"]                      safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"payment_type"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"tag_distributor"]                        safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"sale"]                                   safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"instruction"]                            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"VAT"]                                    safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"VAT_details"]                            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"branch_rds_transfer"]                    safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"amount"]                                 safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"VAT_type"]                               safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_calc"]                                safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_trans_type"]                          safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"VAT_calc_on"]                            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_validation"]                          safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_calc_basedon"]                        safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"premium"]                                safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"previous_order"]                         safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer_OTP"]                       safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"customer_information_check"]             safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer_route_creation"]            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"order_type"]                             safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"freight_component"]                      safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"tax_type"]                               safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"destination"]                            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"input_screen_normal"]                    safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"input_screen_special"]                   safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer_details"]                   safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer_trade_nontrade"]            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"printer_type"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"printer_menu"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"hint_remarks"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"hint_remarks_val"]                       safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer_image_creation"]            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"distributor_route_emp_relation"]         safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"multiple_UOM"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"input_screen_planwise"]                  safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_type_input_dropdown"]                 safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"input_screen_planwise_filter1wise"]      safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"input_screen_price_validation"]          safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"sauda_sale_rate_input_dropdown"]         safeValueeForKey:@"text"]];
        
        [db executeStatements:strInsertOrderFormDetails];
        [db close];
    }
}

-(void)insertProductDetails:(NSDictionary*)dict{
    
    NSDictionary *dictPD = [[dict safeValueeForKey:@"recordset"] safeValueeForKey:@"data"];
    AppLog(@"-->>%@",[[dictPD safeValueeForKey:@"user_id"] safeValueeForKey:@"text"]);
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        NSString *strInsertUserDetails = [NSString stringWithFormat:@"INSERT INTO product_details  VALUES('%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@')",
                                          [[dictPD safeValueeForKey:@"product_id"]                          safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"user_id"]                             safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"no_of_filter"]                        safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"col1"]                                safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"col2"]                                safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"col3"]                                safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"col4"]                                safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"uom_wise_mrp"]                        safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"sauda_allocation_basedon_filter"]     safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"product_in_business_prospect"]        safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"branch_wise_product"]                 safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"secondary_unit"]                      safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"destination_price_list"]              safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"destination_ordertype_price_list"]    safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"state_wise_mrp"]                      safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"multiple_rate"]                       safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"product_qty_wise_TD"]                 safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"focus_product"]                       safeValueeForKey:@"text"]];
        
        [db executeStatements:strInsertUserDetails];
        [db close];
    }
    
}

-(void)insertCustomerMasterWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        FMDatabase *db = [FMDatabase databaseWithPath:path];
        [db open];
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrCustomer = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrCustomer.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO customer_master(customer_code, customer_name, route_code, emp_code, current_balance, credit_limit, acedns, black_list, TD, cust_type, rds_tag, sauda_validity_period, address, pin, phone_no, check_flag, landline_no, owner_name, owner_phone, cust_class, weekly_closing_day, coverage_type, TIN, PAN, minimum_stock, branch_code, visit_day, email, sauda_limit, pending_qty, flag, SAP_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",arrCustomer[0],arrCustomer[1],arrCustomer[2],arrCustomer[3],arrCustomer[4],arrCustomer[5],arrCustomer[6],arrCustomer[7],arrCustomer[8],arrCustomer[9],arrCustomer[10],arrCustomer[11],arrCustomer[12],arrCustomer[13],arrCustomer[14],arrCustomer[15],arrCustomer[16],arrCustomer[17],arrCustomer[18],arrCustomer[19],arrCustomer[20],arrCustomer[21],arrCustomer[22],arrCustomer[23],arrCustomer[24],arrCustomer[25],arrCustomer[26],arrCustomer[27],arrCustomer[28],arrCustomer[29],@"1",arrCustomer[30]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertEmployeeMasterWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        FMDatabase *db = [FMDatabase databaseWithPath:path];
        [db open];
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrEmployee = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrEmployee.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO emp_master(emp_code, emp_name, sale_access, reporting_to, level, designation, vertical_value, branch_code, state, zone, acedns, lower_leaves) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",arrEmployee[0],arrEmployee[1],arrEmployee[2],arrEmployee[3],arrEmployee[4],arrEmployee[5],arrEmployee[6],arrEmployee[7],arrEmployee[8],arrEmployee[9],arrEmployee[10],arrEmployee[11]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertSelfAppraisalProductWiseWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        FMDatabase *db = [FMDatabase databaseWithPath:path];
        [db open];
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrSAPW = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrSAPW.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO self_appraisal_product_wise(prod_code, prod_desc, emp_code, month, target, achievement, prev_y_target, prev_y_achievement) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",arrSAPW[0],arrSAPW[1],arrSAPW[2],arrSAPW[3],arrSAPW[4],arrSAPW[5],arrSAPW[6],arrSAPW[7]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertBranchSchemePDFWithFilePath:(NSString*)filePath{
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        FMDatabase *db = [FMDatabase databaseWithPath:path];
        [db open];
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrBSPDF = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrBSPDF.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO branch_schemes_PDF(branch_code, PDF_file_name, acedns) VALUES (?, ?, ?)",arrBSPDF[0],arrBSPDF[1],arrBSPDF[2]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertProductMasterWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    
    NSData *data = [stringContent dataUsingEncoding:NSUTF8StringEncoding];
    NSDictionary *dict = [NSJSONSerialization JSONObjectWithData:data options:0 error:nil];
    AppLog(@"%@",dict);
    
    NSArray *arrProducts = [dict safeValueeForKey:@"product_date"];
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    [db open];
    for (NSDictionary *dictProduct in arrProducts) {
        // procced to insert data.
        BOOL success = [db executeUpdate:@"INSERT INTO product_master(prod_code, product_group_code, product_group_name, product_sub_group_code, product_sub_group_name, product_brand_code, product_brand_name, prod_desc, black_list, acedns, uom1, uom2, conversion_factor, pack_size, uom3, conversion_factor_two, TD, branch_code, vertical_value, secondary_unit, dns_prod_code, focus, weightage, vat, addl_vat, freight_cost, cl_stk) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",[dictProduct safeValueeForKey:@"prod_code"],[dictProduct safeValueeForKey:@"product_group_code"],@"",@"",@"",@"",@"",[dictProduct safeValueeForKey:@"prod_desc"],@"",@"",@"",@"",@"",@"",@"",@"",@"",[dictProduct safeValueeForKey:@"branch_code"],@"",@"",[dictProduct safeValueeForKey:@"dns_prod_code"],@"",@"",@"",@"",@"",@""];
        if (!success) {
            AppLog(@"error = %@", [db lastErrorMessage]);
        }
    }
    [db close];
}

-(void)insertMenuAccessWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        FMDatabase *db = [FMDatabase databaseWithPath:path];
        [db open];
        
        for (int i = 2; i < intRowCount + 2; i++) {
            
            NSArray *arrMA = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrMA.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO menu_access(not_accessibility_menu) VALUES (?)",arrMA[0]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertDestinationMasterWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        FMDatabase *db = [FMDatabase databaseWithPath:path];
        [db open];
        
        //CREATE TABLE destination_master (destination_code TEXT NOT NULL,destination_name TEXT NOT NULL)
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrDM = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrDM.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO destination_master(destination_code, destination_name, ex_for_type) VALUES (?, ?, ?)",arrDM[0],arrDM[1],arrDM[2]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertBranchMasterWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        FMDatabase *db = [FMDatabase databaseWithPath:path];
        [db open];
        
        //CREATE TABLE branch_master (company_code TEXT NOT NULL,branch_code TEXT NOT NULL,branch_name TEXT NOT NULL,Hq TEXT NULL,plant_name TEXT DEFAULT NULL)
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrBM = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrBM.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO branch_master(company_code, branch_code, branch_name, Hq, plant_name) VALUES (?, ?, ?, ?, ?)",arrBM[0],arrBM[1],arrBM[2],arrBM[3],arrBM[4]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertBranchDumpWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        FMDatabase *db = [FMDatabase databaseWithPath:path];
        [db open];
        
        //CREATE TABLE branch_dump (branch_code TEXT NOT NULL,dump_code TEXT NOT NULL,dump_name TEXT NOT NULL,acedns TEXT NOT NULL,is_plant TEXT NOT NULL,download_time TEXT NOT NULL)
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrBD = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrBD.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO branch_dump(branch_code, dump_code, dump_name, acedns, is_plant, download_time) VALUES (?, ?, ?, ?, ?, ?)",arrBD[0],arrBD[1],arrBD[2],arrBD[3],arrBD[4],arrBD[5]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}


#pragma mark - Helper Method

-(void)logout {
    [defaults setValue:nil      forKey:@"dealer_id"];
    [defaults setValue:nil      forKey:@"emp_code"];
    [defaults setValue:nil      forKey:@"emp_name"];
    [defaults setValue:nil      forKey:@"phone_number"];
    [defaults setValue:nil      forKey:@"selected_cust_code"];
    [defaults setValue:nil      forKey:@"selected_cust_name"];
    [defaults setBool:NO        forKey:@"datadownloaded"];
    [defaults setBool:NO        forKey:@"tablestructure"];
    [defaults setBool:nil       forKey:@"kServeyFormSubmitted"];
    [defaults synchronize];
    
    BOOL success;
    NSError *error;
    
    NSFileManager *fileManager = [NSFileManager defaultManager];
    
    NSString *documentsDirectory = [NSHomeDirectory() stringByAppendingPathComponent:@"Documents"];
    NSString *filePath = [documentsDirectory stringByAppendingPathComponent:@"StarSaathi.db"];
    
    success = [fileManager fileExistsAtPath:filePath];
    if (!success) {
        success = [fileManager removeItemAtPath:filePath error:&error];
    }
    
    if (success) {
        for (UIViewController *controller in self.navigationController.viewControllers) {
            //Do not forget to import AnOldViewController.h
            if ([controller isKindOfClass:[SplashViewController class]]) {
                [self.navigationController popToViewController:controller animated:NO];
                return;
            }
        }
    }
}

-(NSString*)notificationCount{
    NSString *strNotificationCount;
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        NSUInteger count = [db intForQuery:@"SELECT COUNT(notification_id) FROM notification where status = 'UNREAD'"];
        strNotificationCount = [NSString stringWithFormat:@"%lu",(unsigned long)count];
        [db close];
    }
    return strNotificationCount;
}

-(void)changeSliderImage {
    
    if (intCounterSlider < arrSlider.count) {
        NSIndexPath *idxPath = [NSIndexPath indexPathForItem:intCounterSlider inSection:0];
        [collViewSlider scrollToItemAtIndexPath:idxPath atScrollPosition:UICollectionViewScrollPositionCenteredHorizontally animated:true];
        pageControlSlider.currentPage = intCounterSlider;
        intCounterSlider += 1;
    }else{
        intCounterSlider = 0;
        NSIndexPath *idxPath = [NSIndexPath indexPathForItem:intCounterSlider inSection:0];
        [collViewSlider scrollToItemAtIndexPath:idxPath atScrollPosition:UICollectionViewScrollPositionCenteredHorizontally animated:false];
        pageControlSlider.currentPage = intCounterSlider;
        intCounterSlider = 1;
    }
}

-(NSString *)randomAlphanumericStringWithLength:(NSInteger)length {
    NSString *letters = @"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    NSMutableString *randomString = [NSMutableString stringWithCapacity:length];
    
    for (int i = 0; i < length; i++) {
        [randomString appendFormat:@"%C", [letters characterAtIndex:arc4random() % [letters length]]];
    }
    
    return randomString;
}

@end
