//
//  LedgerStatementViewController.m
//  StarCementDealer
//

#import "LedgerStatementViewController.h"
#import <WebKit/WebKit.h>
#import <SVProgressHUD/SVProgressHUD.h>

@interface LedgerStatementViewController () <WKNavigationDelegate>

@property (weak, nonatomic) IBOutlet WKWebView *fpWebView;
@property (weak, nonatomic) IBOutlet UIActivityIndicatorView *activityIndicator;

@end

@implementation LedgerStatementViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization

- (void)designView {
    UINavigationBar *bar = [self.navigationController navigationBar];
    [bar setBackgroundColor:UIColorFromRGB(0xEB2228)];
    
    if (@available(iOS 13.0, *)) {
        UIView *statusBar = [[UIView alloc] initWithFrame:[UIApplication sharedApplication].keyWindow.windowScene.statusBarManager.statusBarFrame];
        statusBar.backgroundColor = UIColorFromRGB(0xEB2228);
        [[UIApplication sharedApplication].keyWindow addSubview:statusBar];
    }
    
    self.fpWebView.navigationDelegate = self;
    self.fpWebView.contentMode = UIViewContentModeScaleAspectFit;
    
    NSString *jScript = @"var meta = document.createElement('meta'); meta.setAttribute('name', 'viewport'); meta.setAttribute('content', 'width=device-width'); document.getElementsByTagName('head')[0].appendChild(meta);";
    WKUserScript *wkUScript = [[WKUserScript alloc] initWithSource:jScript injectionTime:WKUserScriptInjectionTimeAtDocumentEnd forMainFrameOnly:YES];
    [self.fpWebView.configuration.userContentController addUserScript:wkUScript];
    
    [self.activityIndicator setHidden:YES];
}

- (void)loadData {
    self.title = self.strTitle;
    NSURL *url = [NSURL URLWithString:self.strWeblink];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    [self.fpWebView loadRequest:request];
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)btnDownloadClicked:(id)sender {
    [self savePDF];
}

#pragma mark - WKNavigationDelegate

- (void)webView:(WKWebView *)webView didStartProvisionalNavigation:(WKNavigation *)navigation {
    self.activityIndicator.hidden = NO;
    [self.activityIndicator startAnimating];
}

- (void)webView:(WKWebView *)webView didFinishNavigation:(WKNavigation *)navigation {
    self.activityIndicator.hidden = YES;
    [self.activityIndicator stopAnimating];
}

- (void)webView:(WKWebView *)webView didFailNavigation:(WKNavigation *)navigation withError:(NSError *)error {
    self.activityIndicator.hidden = YES;
    [self.activityIndicator stopAnimating];
}

#pragma mark - Helper Methods

- (void)savePDF {
    NSURL *url = [NSURL URLWithString:self.strWeblink];
    NSURLSessionConfiguration *configuration = [NSURLSessionConfiguration defaultSessionConfiguration];
    NSURLSession *session = [NSURLSession sessionWithConfiguration:configuration];
    
    [SVProgressHUD showWithStatus:@"Downloading PDF..."];
    
    NSURLSessionDownloadTask *downloadTask = [session downloadTaskWithURL:url
                                                        completionHandler:^(NSURL *location, NSURLResponse *response, NSError *error) {
        dispatch_async(dispatch_get_main_queue(), ^{
            [SVProgressHUD dismiss];
            
            if (error) {
                [SVProgressHUD showErrorWithStatus:error.localizedDescription];
                return;
            }
            
            NSFileManager *fileManager = [NSFileManager defaultManager];
            NSURL *documentsDirectory = [[fileManager URLsForDirectory:NSDocumentDirectory inDomains:NSUserDomainMask] lastObject];
            NSURL *destinationURL = [documentsDirectory URLByAppendingPathComponent:@"Ledger_statement.pdf"];
            
            // Remove existing file if any
            [fileManager removeItemAtURL:destinationURL error:nil];
            
            // Move downloaded file to documents directory
            [fileManager moveItemAtURL:location toURL:destinationURL error:nil];
            
            NSLog(@"File downloaded to: %@", destinationURL);
            
            [self loadPDFAndShare];
        });
    }];
    
    [downloadTask resume];
}

- (void)loadPDFAndShare {
    NSFileManager *fileManager = [NSFileManager defaultManager];
    NSURL *documentsDirectory = [[fileManager URLsForDirectory:NSDocumentDirectory inDomains:NSUserDomainMask] lastObject];
    NSString *pdfPath = [documentsDirectory.path stringByAppendingPathComponent:@"Ledger_statement.pdf"];
    
    if ([fileManager fileExistsAtPath:pdfPath]) {
        NSData *data = [NSData dataWithContentsOfFile:pdfPath];
        UIActivityViewController *activityController = [[UIActivityViewController alloc] initWithActivityItems:@[data] applicationActivities:nil];
        [self presentViewController:activityController animated:YES completion:nil];
    }
}

@end
