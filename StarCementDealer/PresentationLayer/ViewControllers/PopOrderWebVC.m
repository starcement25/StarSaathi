//
//  PopOrderWebVC.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 22/09/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "PopOrderWebVC.h"
#import <WebKit/WebKit.h>
#import "PopOrderListViewController.h"

@interface PopOrderWebVC ()<WKNavigationDelegate>{
    __weak IBOutlet WKWebView *fpWebView;
    __weak IBOutlet UIActivityIndicatorView *activityIndicator;
}

@end

@implementation PopOrderWebVC

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
    
    fpWebView.navigationDelegate = self;
    fpWebView.contentMode = UIViewContentModeScaleAspectFit;
    
    NSString *jScript = @"var meta = document.createElement('meta'); meta.setAttribute('name', 'viewport'); meta.setAttribute('content', 'width=device-width'); document.getElementsByTagName('head')[0].appendChild(meta);";
    
    WKUserScript *wkUScript = [[WKUserScript alloc] initWithSource:jScript injectionTime:WKUserScriptInjectionTimeAtDocumentEnd forMainFrameOnly:YES];
    
    //Here you can customize configuration
    [fpWebView.configuration.userContentController addUserScript:wkUScript];
    
    [activityIndicator setHidden:YES];
}

-(void)loadData{
    NSURL *url = [NSURL URLWithString:self.strPaymentUrl];
    NSURLRequest *requestObj = [NSURLRequest requestWithURL:url];
    [fpWebView loadRequest:requestObj];
}

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
    
    AppLog(@"-->>%@",webView.URL.absoluteString);
    NSString *strCurrentUrl = webView.URL.absoluteString;
    
    if ([strCurrentUrl containsString:@"the_pop_success_url"]) {
        UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil message:@"Thank You your Transaction is successful" preferredStyle:UIAlertControllerStyleAlert];
        [alert addAction:[UIAlertAction actionWithTitle:@"Ok" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
            for (UIViewController *controller in self.navigationController.viewControllers) {
                if ([controller isKindOfClass:[PopOrderListViewController class]]) {
                    [[NSNotificationCenter defaultCenter] postNotificationName:@"resetPopOrder" object:self];
                    [self.navigationController popToViewController:controller animated:YES];
                    return;
                }
            }
        }]];
        [self presentViewController:alert animated:true completion:nil];
    } else if ([strCurrentUrl containsString:@"the_pop_cancel_url"]) {
        UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil message:@"Transaction Cancelled. Please Try Again." preferredStyle:UIAlertControllerStyleAlert];
        [alert addAction:[UIAlertAction actionWithTitle:@"Ok" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
            for (UIViewController *controller in self.navigationController.viewControllers) {
                if ([controller isKindOfClass:[PopOrderListViewController class]]) {
                    [[NSNotificationCenter defaultCenter] postNotificationName:@"resetPopOrder" object:self];
                    [self.navigationController popToViewController:controller animated:YES];
                    return;
                }
            }
        }]];
        [self presentViewController:alert animated:true completion:nil];
    }
}

- (void)webView:(WKWebView *)webView didFailNavigation:(null_unspecified WKNavigation *)navigation withError:(NSError *)error {
    activityIndicator.hidden = YES;
    [activityIndicator stopAnimating];
}

@end
