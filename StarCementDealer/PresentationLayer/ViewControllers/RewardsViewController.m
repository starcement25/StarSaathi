//
//  RewardsViewController.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 23/09/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "RewardsViewController.h"
#import <WebKit/WebKit.h>
#import "NSString+CharHandling.h"

@interface RewardsViewController ()<WKNavigationDelegate>{
    __weak IBOutlet WKWebView *fpWebView;
    __weak IBOutlet UIActivityIndicatorView *activityIndicator;
}

@end

@implementation RewardsViewController

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
    
    activityIndicator.hidden = true;
    
    fpWebView.navigationDelegate = self;
    fpWebView.contentMode = UIViewContentModeScaleAspectFit;
    
    NSString *jScript = @"var meta = document.createElement('meta'); meta.setAttribute('name', 'viewport'); meta.setAttribute('content', 'width=device-width'); document.getElementsByTagName('head')[0].appendChild(meta);";
    
    WKUserScript *wkUScript = [[WKUserScript alloc] initWithSource:jScript injectionTime:WKUserScriptInjectionTimeAtDocumentEnd forMainFrameOnly:YES];
    
    //Here you can customize configuration
    [fpWebView.configuration.userContentController addUserScript:wkUScript];
    
    [activityIndicator setHidden:YES];
}

-(void)loadData{
    NSURL *url = [NSURL URLWithString:self.strRewardLink];
    NSURLRequest *requestObj = [NSURLRequest requestWithURL:url];
    [fpWebView loadRequest:requestObj];
}

//#pragma mark - Web Service
//
//-(void)getRewards{
//    
//    if (APP_DELEGATE.isServerReachable) {
//        
//        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
//        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"emp_code"];        
//        
//        [SVProgressHUD showWithStatus:@"Loading..."];
//        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/dealer-wise-rewards.php" parameters:dict completion:^(NSDictionary *dictResponse){
//            dispatch_async(dispatch_get_main_queue(), ^(void) {
//                [SVProgressHUD dismiss];
//                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
//                    NSString *strRewardLink = [[[dictResponse safeValueeForKey:@"reward_data"] objectAtIndex:0] safeValueeForKey:@"reward_link"];
//                    strRewardLink = [NSString handleSpecialSymbol:strRewardLink];
//                    NSURL *url = [NSURL URLWithString:strRewardLink];
//                    NSURLRequest *requestObj = [NSURLRequest requestWithURL:url];
//                    [fpWebView loadRequest:requestObj];
//                    
//                    [self performSegueWithIdentifier:@"dashboardToRewards" sender:self];
//                    
//                }else
//                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
//            });
//        }];
//    }else
//        UDShowToastAlertWithTitle(kNoInternet, 2);
//}


#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:true];
}

#pragma mark - WebView Delegate

- (void)webView:(WKWebView *)webView didStartProvisionalNavigation:(null_unspecified WKNavigation *)navigation {
    activityIndicator.hidden = NO;
    [activityIndicator startAnimating];
}

- (void)webView:(WKWebView *)webView didFinishNavigation:(null_unspecified WKNavigation *)navigation {
    activityIndicator.hidden = YES;
    [activityIndicator stopAnimating];
}

- (void)webView:(WKWebView *)webView didFailNavigation:(null_unspecified WKNavigation *)navigation withError:(NSError *)error {
    activityIndicator.hidden = YES;
    [activityIndicator stopAnimating];
}

@end
