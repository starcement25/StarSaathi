//
//  MenuViewController.m
//  StarCementDealer
//
//  Created by Coral  on 16/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "MenuViewController.h"
#import "UIViewController+REFrostedViewController.h"
#import "NavigationViewController.h"
#import "XMLParser.h"
#import "LoginViewController.h"
#import "SplashViewController.h"
#import "HelpViewController.h"
#import <MessageUI/MessageUI.h>
#import "KYCTableViewController.h"
#import "YellowCartViewController.h"
#import "TermsViewController.h"
#import "PrivacyViewController.h"
#import "RefundViewController.h"
#import <UIKit/UIKit.h>
#import "CreditLimitViewController.h"
#import "WebViewController.h"
#import "AddLiftingViewController.h"
#import "LiftingHistoryViewController.h"
#import "DealerLiftingHistoryVC.h"
#import "DealerVisitListVC.h"
#import "ConsumerLotterySchemeVC.h"
#import "ExclusiveDealerDeclaration.h"
#import "KismatKiBoriViewController.h"
#import "UIAlertView+Category.h"

@interface MenuViewController ()<MFMailComposeViewControllerDelegate, UINavigationControllerDelegate, UIImagePickerControllerDelegate>{
    NSArray *arrMenu;
    UILabel *labelName;
    UIImageView *imageView;
    NSData *imageData;

    NSString *isShow;
}

@end

@implementation MenuViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
}

- (void)checkMenuData {
    NSString *strDealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
    // NSString *strDealerId = @"1000003658";
    NSString *urlString = [NSString stringWithFormat:@"https://starsaathi.com/SAP/admin/branch_wise_kismat_ki_bori_permission.php?customer_id=%@", strDealerId];
        
    NSURL *url = [NSURL URLWithString:urlString];
    NSURLSessionDataTask *task = [[NSURLSession sharedSession]
        dataTaskWithURL:url
      completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
        NSError *jsonError = nil;
        id jsonObject = [NSJSONSerialization JSONObjectWithData:data options:0 error:&jsonError];

        if (jsonError || ![jsonObject isKindOfClass:[NSDictionary class]]) {
            NSLog(@"JSON Error: %@", jsonError.localizedDescription);
            isShow = @"NO";
        } else {
            NSDictionary *json = (NSDictionary *)jsonObject;
            id statusValue = json[@"status"];
            
            if ([statusValue isKindOfClass:[NSString class]] && [statusValue isEqualToString:@"YES"]) {
                isShow = @"YES";
            } else {
                isShow = @"NO";
            }
        }
        dispatch_async(dispatch_get_main_queue(), ^{
            [self loadData];
        });
    }];
    [task resume];
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    
    // [self loadData];
    [self checkMenuData];
    [self designView];
    
    NSString *strProfilePic = [[NSUserDefaults standardUserDefaults] valueForKey:@"profile_image"];
    
    [self loadProfileImage:strProfilePic];
}

- (void)loadProfileImage:(NSString *)strProfilePic {
    if (!strProfilePic || strProfilePic.length == 0) {
        imageView.image = [UIImage imageNamed:@"user_placeholder"];
        return;
    }

    NSURL *url = [NSURL URLWithString:strProfilePic];
    if (!url) {
        imageView.image = [UIImage imageNamed:@"user_placeholder"];
        return;
    }

    // Async download
    NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithURL:url
                                                             completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
        UIImage *downloadedImage = nil;
        if (data && !error) {
            downloadedImage = [UIImage imageWithData:data];
        }

        dispatch_async(dispatch_get_main_queue(), ^{
            self->imageView.image = downloadedImage ?: [UIImage imageNamed:@"user_placeholder"];
        });
    }];
    [task resume];
}


#pragma mark - Initialization Method

-(void)designView{
    //self.tableView.separatorColor = [UIColor colorWithRed:150/255.0f green:161/255.0f blue:177/255.0f alpha:1.0f];
    self.tableView.separatorColor = [UIColor clearColor];
    self.tableView.delegate = self;
    self.tableView.dataSource = self;
    //self.tableView.opaque = NO;
    self.tableView.backgroundColor = [UIColor whiteColor];
    self.tableView.tableFooterView = [UIView new];
    self.tableView.tableHeaderView = ({
        UIView *view = [[UIView alloc] initWithFrame:CGRectMake(0, 0, 0, 184.0f)];
        view.backgroundColor = [UIColor colorWithRed:0.945 green:0.961 blue:0.969 alpha:1.00];
        imageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, 40, 100, 100)];
        imageView.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin;
        imageView.image = [UIImage imageNamed:@"user_placeholder"];
        imageView.layer.masksToBounds = YES;
        imageView.layer.cornerRadius = 50.0;
        imageView.layer.borderColor = [UIColor whiteColor].CGColor;
        imageView.layer.borderWidth = 3.0f;
        imageView.backgroundColor = [UIColor lightGrayColor];
        imageView.layer.rasterizationScale = [UIScreen mainScreen].scale;
        imageView.layer.shouldRasterize = YES;
        imageView.clipsToBounds = YES;
        imageView.userInteractionEnabled = true;
        [imageView addGestureRecognizer:[[UITapGestureRecognizer alloc]initWithTarget:self action:@selector(imgViewUserTapped:)]];
        
        labelName = [[UILabel alloc] initWithFrame:CGRectMake(0, 150, 0, 24)];
        //label.text = @"Username";
        AppLog(@"-->>%@",[[NSUserDefaults standardUserDefaults] valueForKey:@"emp_name"]);
        labelName.text = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_name"];
        labelName.font = [UIFont fontWithName:@"HelveticaNeue" size:17];
        labelName.backgroundColor = [UIColor clearColor];
        labelName.textColor = [UIColor colorWithRed:0.000 green:0.216 blue:0.584 alpha:1.00];
        [labelName sizeToFit];
        labelName.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin;
        
        [view addSubview:imageView];
        [view addSubview:labelName];
        view;
    });
}

-(void)loadData{
    
    AppLog(@"-->>%@",[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"]);
    if (checkUserType(kDealer) == true) {
        // if([isShow isEqualToString:@"YES"]){
        //     arrMenu = @[@"About Us", @"Help", @"KYC", @"App Payment History",@"Exclusive Dealer Declaration",@"Kismat Ki Bori",@"Issues",@"Dealer Visit",@"Terms and Conditions",@"Privacy Policy",@"Refund Policy",@"Credit Limit", @"Lifting History", @"Consumer Lottery Scheme", @"Logout"];
        // }else{
        //     arrMenu = @[@"About Us", @"Help", @"KYC", @"App Payment History",@"Exclusive Dealer Declaration",@"Issues",@"Dealer Visit",@"Terms and Conditions",@"Privacy Policy",@"Refund Policy",@"Credit Limit", @"Lifting History", @"Consumer Lottery Scheme", @"Logout"];
        // }

        if([isShow isEqualToString:@"YES"]){
            arrMenu = @[@"About Us", @"Help", @"KYC", @"App Payment History",@"Exclusive Dealer Declaration",@"Kismat Ki Bori",@"Issues",@"Dealer Visit",@"Terms and Conditions",@"Privacy Policy",@"Refund Policy",@"Credit Limit", @"Lifting History", @"Consumer Lottery Scheme", @"Logout"];
        }else{
            arrMenu = @[@"About Us", @"Help", @"KYC", @"App Payment History",@"Exclusive Dealer Declaration",@"Issues",@"Dealer Visit",@"Terms and Conditions",@"Privacy Policy",@"Refund Policy",@"Credit Limit", @"Lifting History", @"Consumer Lottery Scheme", @"Logout"];
        }
    }else{
        arrMenu = @[@"About Us", @"Help", @"KYC", @"Issues",@"Dealer Visit", @"Terms and Conditions", @"Privacy Policy", @"Refund Policy", @"Consumer Lottery Scheme", @"Logout"];
    }
    [self.tableView reloadData];
}


#pragma mark - IBAction's

-(void)imgViewUserTapped:(UITapGestureRecognizer*)gesture {
    
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil message:nil preferredStyle:UIAlertControllerStyleActionSheet];
    
    [alert addAction:[UIAlertAction actionWithTitle:@"Camera" style:UIAlertActionStyleDefault handler:^(UIAlertAction *action){
        if (![UIImagePickerController isSourceTypeAvailable:UIImagePickerControllerSourceTypeCamera]) {
            UDShowToastAlertWithTitle(@"Device has no camera", 2);
            return ;
        }
        
        UIImagePickerController *picker = [[UIImagePickerController alloc] init];
        picker.delegate = self;
        picker.allowsEditing = YES;
        picker.sourceType = UIImagePickerControllerSourceTypeCamera;
        
        [self presentViewController:picker animated:YES completion:NULL];
    }]];
    
    [alert addAction:[UIAlertAction actionWithTitle:@"Gallery" style:UIAlertActionStyleDefault handler:^(UIAlertAction *action){
        UIImagePickerController *picker = [[UIImagePickerController alloc] init];
        picker.delegate = self;
        picker.allowsEditing = YES;
        picker.sourceType = UIImagePickerControllerSourceTypePhotoLibrary;
        
        [self presentViewController:picker animated:YES completion:NULL];
    }]];
    
    [alert addAction:[UIAlertAction actionWithTitle:@"Cancel" style:UIAlertActionStyleCancel handler:^(UIAlertAction *action){
        
    }]];
    
    [self presentViewController:alert animated:YES completion:nil];
    
}

#pragma mark -
#pragma mark - UITableView Datasource

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath {
    return 54;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)sectionIndex {
    return arrMenu.count;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    static NSString *cellIdentifier = @"Cell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier];
    if (cell == nil) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:cellIdentifier];
    }
    cell.textLabel.text = arrMenu[indexPath.row];
    return cell;
}

#pragma mark -
#pragma mark UITableView Delegate

- (void)tableView:(UITableView *)tableView willDisplayCell:(UITableViewCell *)cell forRowAtIndexPath:(NSIndexPath *)indexPath {
    cell.backgroundColor = [UIColor clearColor];
    cell.textLabel.textColor = [UIColor colorWithRed:0.000 green:0.216 blue:0.584 alpha:1.00];
    cell.textLabel.font = [UIFont fontWithName:@"HelveticaNeue" size:17];
}



- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    NavigationViewController *navigationController = [self.storyboard instantiateViewControllerWithIdentifier:@"contentController"];
    
    NSString *strMenuItem = arrMenu[indexPath.row];
    
    if ([strMenuItem isEqualToString:@"About Us"]) {
        
        WebViewController *wvc = [self.storyboard instantiateViewControllerWithIdentifier:@"webvc"];
        wvc.strWeblink = @"https://starsaathi.com/SAP/weblink/about.html";
        wvc.strCategory = @"about";
        wvc.strTitle = strMenuItem;
        navigationController.viewControllers = @[wvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
        
    }else if ([strMenuItem isEqualToString:@"Help"]) {
        
        HelpViewController *hvc = [self.storyboard instantiateViewControllerWithIdentifier:@"helpViewController"];
        navigationController.viewControllers = @[hvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
        
    }else if ([strMenuItem isEqualToString:@"Issues"]){
        
        UIAlertController *alertController = [UIAlertController alertControllerWithTitle:nil
                                                                                 message:nil
                                                                          preferredStyle:UIAlertControllerStyleActionSheet];
        
        NSArray *arr = @[@"Call",@"Email"];
        for (NSString *str in arr) {
            UIAlertAction *action = [UIAlertAction actionWithTitle:str
                                                             style:UIAlertActionStyleDefault
                                                           handler:^(UIAlertAction *action) {
                if ([action.title isEqualToString:@"Call"]) {
                    NSString *phoneNumber = [@"tel://" stringByAppendingString:@"180034534500"];
                    [[UIApplication sharedApplication] openURL:[NSURL URLWithString:phoneNumber] options:@{} completionHandler:nil];
                }else{
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
                }
            }];
            [alertController addAction:action];
        }
        UIAlertAction *cancelAction = [UIAlertAction actionWithTitle:@"Cancel"
                                                               style:UIAlertActionStyleCancel
                                                             handler:^(UIAlertAction *action) {
        }];
        [alertController addAction:cancelAction];
        [self presentViewController:alertController animated:YES completion:nil];
        return;
        
    }else if ([strMenuItem isEqualToString:@"KYC"]){
        
        KYCTableViewController *kycvc = [self.storyboard instantiateViewControllerWithIdentifier:@"kyctvc"];
        navigationController.viewControllers = @[kycvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
        
    }else if ([strMenuItem isEqualToString:@"Yellow Card"]){
        
        YellowCartViewController *ycvc = [self.storyboard instantiateViewControllerWithIdentifier:@"yellowCartVC"];
        navigationController.viewControllers = @[ycvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
        
    }else if ([strMenuItem isEqualToString:@"Terms and Conditions"]){
        
        TermsViewController *ycvc = [self.storyboard instantiateViewControllerWithIdentifier:@"termsVC"];
        navigationController.viewControllers = @[ycvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
        
    }else if ([strMenuItem isEqualToString:@"Privacy Policy"]){
        
         WebViewController *wvc = [self.storyboard instantiateViewControllerWithIdentifier:@"webvc"];
        wvc.strWeblink = @"https://starsaathi.com/SAP/privacy.html";
        wvc.strCategory = @"PrivacyPolicy";
        wvc.strTitle = strMenuItem;
        navigationController.viewControllers = @[wvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
        // PrivacyViewController *ycvc = [self.storyboard instantiateViewControllerWithIdentifier:@"privacyVC"];
        // navigationController.viewControllers = @[ycvc];
        // self.frostedViewController.contentViewController = navigationController;
        // [self.frostedViewController hideMenuViewController];
        
    }else if ([strMenuItem isEqualToString:@"Refund Policy"]){
        
        RefundViewController *ycvc = [self.storyboard instantiateViewControllerWithIdentifier:@"refundVC"];
        navigationController.viewControllers = @[ycvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
        
    }else if ([strMenuItem isEqualToString:@"Credit Limit"]){
        
        CreditLimitViewController *clvc = [self.storyboard instantiateViewControllerWithIdentifier:@"creditLimitVC"];
        navigationController.viewControllers = @[clvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
        
    }else if ([strMenuItem isEqualToString:@"App Payment History"]){
        
        NSString *strWeblink = [NSString stringWithFormat:@"https://starsaathi.com/SAP/weblink/my_payment_history_weblink.php?the_id=%@",APP_CONSTANTS.strCustCode];
        WebViewController *wvc = [self.storyboard instantiateViewControllerWithIdentifier:@"webvc"];
        wvc.strWeblink = strWeblink;
        wvc.strCategory = @"history";
        wvc.strTitle = strMenuItem;
        navigationController.viewControllers = @[wvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
        
    }else if ([strMenuItem isEqualToString:@"ARC Consumer Scheme"]){
        
        NSString *strWeblink = [NSString stringWithFormat:@"https://starsaathi.com/SAP/arc_offer/index.php?login_user_id=%@&user_type=dealer",APP_CONSTANTS.strCustCode];
        WebViewController *wvc = [self.storyboard instantiateViewControllerWithIdentifier:@"webvc"];
        wvc.strWeblink = strWeblink;
        wvc.strCategory = @"arc";
        wvc.strTitle = strMenuItem;
        navigationController.viewControllers = @[wvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
        
    }else if ([strMenuItem isEqualToString:@"Add Lifting"]){
        
        AddLiftingViewController *alvc = [self.storyboard instantiateViewControllerWithIdentifier:@"addLiftingVC"];
        navigationController.viewControllers = @[alvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
        
    }else if ([strMenuItem isEqualToString:@"Lifting History"]){
        if (checkUserType(kDealer) == true) {
            DealerLiftingHistoryVC *dlhvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dealerLiftingHistoryVC"];
            navigationController.viewControllers = @[dlhvc];
            self.frostedViewController.contentViewController = navigationController;
            [self.frostedViewController hideMenuViewController];
        }else{
            LiftingHistoryViewController *lhvc = [self.storyboard instantiateViewControllerWithIdentifier:@"liftingHistoryVC"];
            navigationController.viewControllers = @[lhvc];
            self.frostedViewController.contentViewController = navigationController;
            [self.frostedViewController hideMenuViewController];
        }
        
    }else if ([strMenuItem isEqualToString:@"Logout"]){
        NSString *strUrl = [NSString stringWithFormat:@"https://starsaathi.com/SAP/acedns_star_clear_allocation_by_id.php?the_id=%@",[[NSUserDefaults standardUserDefaults]valueForKey:@"emp_code"]];
        [XMLParser callServiceWithPostData:strUrl withParam:nil success:^(NSData *data){
            NSError *jsonParserError;
            NSDictionary *jsonData = [NSJSONSerialization JSONObjectWithData:data
                                                                     options:NSJSONReadingMutableLeaves error:&jsonParserError];
            if ([[jsonData valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                
                NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
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
                if (success) {
                    success = [fileManager removeItemAtPath:filePath error:&error];
                }
                
                if (success) {
                    SplashViewController *svc = [self.storyboard instantiateViewControllerWithIdentifier:@"splashViewController"];
                    navigationController.viewControllers = @[svc];
                    self.frostedViewController.contentViewController = navigationController;
                    [self.frostedViewController hideMenuViewController];
                }
            }else{
                UDShowToastAlertWithTitle([jsonData safeValueeForKey:@"process_message"], 2);
            }
            
        } failed:^(NSString *strErrMsg){
            UDShowToastAlertWithTitle(strErrMsg, 2);
        }];
        
    }else if ([strMenuItem isEqualToString:@"Dealer Visit"]){
        DealerVisitListVC *dvlvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dealerVisitListVC"];
        navigationController.viewControllers = @[dvlvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
    }else if ([strMenuItem isEqualToString:@"Consumer Lottery Scheme"]){
        ConsumerLotterySchemeVC *clsvc = [self.storyboard instantiateViewControllerWithIdentifier:@"conLotterySchemeVC"];
        navigationController.viewControllers = @[clsvc];
        self.frostedViewController.contentViewController = navigationController;
        [self.frostedViewController hideMenuViewController];
    }else if([strMenuItem isEqualToString:@"Exclusive Dealer Declaration"]){
        [self.frostedViewController hideMenuViewController];
        @try {
            ExclusiveDealerDeclaration *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"ExclusiveDealerDeclaration"];
            navigationController.viewControllers = @[vc];
            self.frostedViewController.contentViewController = navigationController;
        } @catch (NSException *exception) {
            NSLog(@"Exception occurred: %@ - %@", exception.name, exception.reason);
        } @finally {
            // Optional: cleanup code
        }
    } else if([strMenuItem isEqualToString:@"Kismat Ki Bori"]){
        [self.frostedViewController hideMenuViewController];
        @try {
            KismatKiBoriViewController *vc1 = [self.storyboard instantiateViewControllerWithIdentifier:@"KismatKiBoriViewController"];
            navigationController.viewControllers = @[vc1];
            self.frostedViewController.contentViewController = navigationController;
        } @catch (NSException *exception) {
            NSLog(@"Exception occurred: %@ - %@", exception.name, exception.reason);
        } @finally {
            // Optional: cleanup code
        }
    }
    
}

#pragma mark - MFMailComposeViewController Delegate

- (void) mailComposeController:(MFMailComposeViewController *)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError *)error
{
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

#pragma mark - UIImagePickerControllerDelegate

- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingMediaWithInfo:(NSDictionary *)info {
    UIImage *chosenImage = info[UIImagePickerControllerEditedImage];
    //imageData = UIImageJPEGRepresentation(chosenImage, 0.5); //JPEG conversion
    imageData = UIImagePNGRepresentation(chosenImage);
    imageView.image = chosenImage;
    [picker dismissViewControllerAnimated:YES completion:NULL];
    [self uploadProfilePic];
}

- (void)imagePickerControllerDidCancel:(UIImagePickerController *)picker {
    [picker dismissViewControllerAnimated:YES completion:NULL];
}

#pragma mark - Helper Method

- (void)uploadProfilePic {
    if (!APP_DELEGATE.isServerReachable) {
        UDShowToastAlertWithTitle(@"Cannot connect to the server - please check your Internet connection and try again.", 2);
        return;
    }
    
    [SVProgressHUD showWithStatus:@"Uploading..."];
    
    NSString *urlString = @"https://starsaathi.com/SAP/acedns_update_profile_image.php";
    NSURL *url = [NSURL URLWithString:urlString];
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:url];
    request.HTTPMethod = @"POST";
    
    NSString *boundary = [NSString stringWithFormat:@"Boundary-%@", [[NSUUID UUID] UUIDString]];
    [request setValue:[NSString stringWithFormat:@"multipart/form-data; boundary=%@", boundary]
   forHTTPHeaderField:@"Content-Type"];
    
    NSMutableData *body = [NSMutableData data];
    
    // JSON part
    NSDictionary *dictProfile = @{
        @"the_id": APP_CONSTANTS.strCustCode ?: @"",
        @"user_type": [[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] ?: @""
    };
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:dictProfile options:0 error:nil];
    [body appendData:[[NSString stringWithFormat:@"--%@\r\n", boundary] dataUsingEncoding:NSUTF8StringEncoding]];
    [body appendData:[@"Content-Disposition: form-data; name=\"json\"\r\n" dataUsingEncoding:NSUTF8StringEncoding]];
    [body appendData:[@"Content-Type: application/json\r\n\r\n" dataUsingEncoding:NSUTF8StringEncoding]];
    [body appendData:jsonData];
    [body appendData:[@"\r\n" dataUsingEncoding:NSUTF8StringEncoding]];
    
    // Image part
    if (self->imageData) {
        [body appendData:[[NSString stringWithFormat:@"--%@\r\n", boundary] dataUsingEncoding:NSUTF8StringEncoding]];
        [body appendData:[@"Content-Disposition: form-data; name=\"profile_image\"; filename=\"image.png\"\r\n" dataUsingEncoding:NSUTF8StringEncoding]];
        [body appendData:[@"Content-Type: image/png\r\n\r\n" dataUsingEncoding:NSUTF8StringEncoding]];
        [body appendData:self->imageData];
        [body appendData:[@"\r\n" dataUsingEncoding:NSUTF8StringEncoding]];
    }
    
    [body appendData:[[NSString stringWithFormat:@"--%@--\r\n", boundary] dataUsingEncoding:NSUTF8StringEncoding]];
    request.HTTPBody = body;
    
    NSURLSession *session = [NSURLSession sharedSession];
    NSURLSessionDataTask *task = [session dataTaskWithRequest:request
                                            completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
        dispatch_async(dispatch_get_main_queue(), ^{
            [SVProgressHUD dismiss];
            
            if (error) {
                UDShowToastAlertWithTitle(error.localizedDescription, 2);
                return;
            }
            
            NSError *jsonError;
            NSDictionary *json = [NSJSONSerialization JSONObjectWithData:data options:kNilOptions error:&jsonError];
            
            if (!json || jsonError) {
                UDShowToastAlertWithTitle(@"Failed to parse server response", 2);
                return;
            }
            
            [[NSUserDefaults standardUserDefaults] setObject:json[@"the_profile_image_url"] forKey:@"profile_image"];
            [[NSUserDefaults standardUserDefaults] synchronize];
            
            UDShowToastAlertWithTitle(json[@"process_message"], 2);
        });
    }];
    
    [task resume];
}


@end
