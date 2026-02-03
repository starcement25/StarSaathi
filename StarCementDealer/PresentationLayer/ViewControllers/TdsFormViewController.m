//
//  TdsFormViewController.m
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 18/06/21.
//  Copyright © 2021 Coral . All rights reserved.
//

#import "TdsFormViewController.h"
#import <WebKit/WebKit.h>
#import "DashboardViewController.h"

@interface TdsFormViewController ()<WKNavigationDelegate>{
    __weak IBOutlet WKWebView *fpWebView;
    __weak IBOutlet UIActivityIndicatorView *activityIndicator;
}

@end

@implementation TdsFormViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    
    self.navigationItem.hidesBackButton = YES;
    
    fpWebView.navigationDelegate = self;
    fpWebView.contentMode = UIViewContentModeScaleAspectFit;
    
    NSString *jScript = @"var meta = document.createElement('meta'); meta.name = 'viewport'; meta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'; var head = document.getElementsByTagName('head')[0]; head.appendChild(meta);";
    
    WKUserScript *wkUScript = [[WKUserScript alloc] initWithSource:jScript injectionTime:WKUserScriptInjectionTimeAtDocumentEnd forMainFrameOnly:YES];
    
    //Here you can customize configuration
    [fpWebView.configuration.userContentController addUserScript:wkUScript];
    [activityIndicator setHidden:YES];
}

-(void)loadData {
    
    self.navigationItem.hidesBackButton = YES;
    NSString *strWeblink = [NSString stringWithFormat:@"https://starsaathi.com/SAP/weblink/survey_form.php?the_id=%@",APP_CONSTANTS.strCustCode];
    NSURL *url = [NSURL URLWithString:strWeblink];
    NSURLRequest *requestObj = [NSURLRequest requestWithURL:url];
    [fpWebView loadRequest:requestObj];
}

#pragma mark - WebView Delegate

- (void)webView:(WKWebView *)webView didStartProvisionalNavigation:(null_unspecified WKNavigation *)navigation {
    activityIndicator.hidden = NO;
    [activityIndicator startAnimating];
}

- (void)webView:(WKWebView *)webView didFinishNavigation:(null_unspecified WKNavigation *)navigation {
    
    activityIndicator.hidden = YES;
    [activityIndicator stopAnimating];
    
    NSString *strRelativePath = webView.URL.relativePath;
    AppLog(@"RelativePath-->>%@",strRelativePath);
    if ([strRelativePath isEqualToString:@"/SAP/weblink/survey_success_page.php"]) {
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        [defaults setValue:@"YES" forKey:@"kServeyFormSubmitted"];
        [defaults synchronize];
        dispatch_after(dispatch_time(DISPATCH_TIME_NOW, (int64_t)(2 * NSEC_PER_SEC)), dispatch_get_main_queue(), ^{
            [self.navigationController popViewControllerAnimated:true];
        });
    }
}


- (void)webView:(WKWebView *)webView didFailNavigation:(null_unspecified WKNavigation *)navigation withError:(NSError *)error {
    activityIndicator.hidden = YES;
    [activityIndicator stopAnimating];
}

@end
