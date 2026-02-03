//
//  WebViewController.m
//  StarCementDealer
//

#import "WebViewController.h"
#import <WebKit/WebKit.h>
#import "DashboardViewController.h"
#import <SVProgressHUD/SVProgressHUD.h>
#import "GraphPopupView.h"

@interface WebViewController ()<WKNavigationDelegate>{
    __weak IBOutlet WKWebView *webViewNotification;
    __weak IBOutlet UIActivityIndicatorView *activityIndicator;
    __weak IBOutlet UIBarButtonItem *graphButton;
}

@end

@implementation WebViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
    if (![self.strCategory isEqualToString:@"scheme"]) {
        self.navigationItem.rightBarButtonItem = nil;
    }
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
}

#pragma mark - Initialization Method

-(void)designView{
    
    if ([self.strTitle isEqualToString:@"PDF"]) {
        UIBarButtonItem *rightBarButtonItem = [[UIBarButtonItem alloc] initWithTitle:@"Download"
                                                                               style:UIBarButtonItemStyleDone
                                                                              target:self
                                                                              action:@selector(btnDownloadClicked:)];
        rightBarButtonItem.tintColor = [UIColor whiteColor];
        self.navigationItem.rightBarButtonItem = rightBarButtonItem;
    }
    
    UINavigationBar *bar = [self.navigationController navigationBar];
    [bar setBackgroundColor:UIColorFromRGB(0xEB2228)];
    
    if (@available(iOS 13.0, *)) {
        UIView *statusBar = [[UIView alloc]initWithFrame:[UIApplication sharedApplication].keyWindow.windowScene.statusBarManager.statusBarFrame] ;
        statusBar.backgroundColor = UIColorFromRGB(0xEB2228);
        [[UIApplication sharedApplication].keyWindow addSubview:statusBar];
    }
    
    webViewNotification.navigationDelegate = self;
    webViewNotification.contentMode = UIViewContentModeScaleAspectFit;
    
    NSString *jScript = @"var meta = document.createElement('meta'); meta.setAttribute('name', 'viewport'); meta.setAttribute('content', 'width=device-width'); document.getElementsByTagName('head')[0].appendChild(meta);";
    
    WKUserScript *wkUScript = [[WKUserScript alloc] initWithSource:jScript
                                                    injectionTime:WKUserScriptInjectionTimeAtDocumentEnd
                                                 forMainFrameOnly:YES];
    
    [webViewNotification.configuration.userContentController addUserScript:wkUScript];
    
    [activityIndicator setHidden:YES];
}

-(void)loadData{
    self.title = self.strTitle;
    
    NSURL *url = [NSURL URLWithString:self.strWeblink];
    NSURLRequest *requestObj = [NSURLRequest requestWithURL:url];
    [webViewNotification loadRequest:requestObj];
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    
    if ([self.strTitle isEqualToString:@"Ledger"]) {
        for (UIViewController *controller in self.navigationController.viewControllers) {
            if ([controller isKindOfClass:[DashboardViewController class]]) {
                [self.navigationController popToViewController:controller animated:YES];
                return;
            }
        }
    } else if ([self.strTitle isEqualToString:@"PDF"]){
        [self.navigationController popViewControllerAnimated:true];
    } else {
        DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
        [self.navigationController pushViewController:dvc animated:NO];
    }
}

// - (IBAction)btnGraphClicked:(UIBarButtonItem *)sender {
//     NSString *category = [self.schemeData[@"category"] description];

//     NSMutableArray *dates = [NSMutableArray array];
//     NSMutableArray *qty   = [NSMutableArray array];

//     if ([[category lowercaseString] isEqualToString:@"single"]) {

//         id achievementsObj = self.schemeData[@"achievements"];

//         if ([achievementsObj isKindOfClass:[NSArray class]]) {

//             NSArray *achievements = (NSArray *)achievementsObj;

//             for (id obj in achievements) {

//                 if (![obj isKindOfClass:[NSDictionary class]]) continue;

//                 NSDictionary *item = (NSDictionary *)obj;

//                 // ---- DATE ----
//                 NSString *dateStr = [item[@"date"] description];

//                 // convert 2025-12-03 -> 03
//                 NSDateFormatter *input = [[NSDateFormatter alloc] init];
//                 [input setDateFormat:@"yyyy-MM-dd"];

//                 NSDateFormatter *output = [[NSDateFormatter alloc] init];
//                 [output setDateFormat:@"dd"];

//                 NSDate *dt = [input dateFromString:dateStr];
//                 NSString *finalDate = dt ? [output stringFromDate:dt] : dateStr;

//                 // ---- QTY (string OR number safe) ----
//                 NSNumber *liftingQty = nil;

//                 id qtyObj = item[@"lifting_qty"];
//                 if ([qtyObj isKindOfClass:[NSNumber class]]) {
//                     liftingQty = qtyObj;
//                 } 
//                 else if ([qtyObj isKindOfClass:[NSString class]]) {
//                     liftingQty = @([qtyObj floatValue]);
//                 }

//                 if (finalDate && liftingQty) {
//                     [dates addObject:finalDate];
//                     [qty addObject:liftingQty];
//                 }
//             }
//         }
//     }

//     NSLog(@"DATES ARRAY = %@", dates);
//     NSLog(@"QTY ARRAY   = %@", qty);

//     NSArray *dates = @[@"01", @"02", @"03", @"04", @"05", @"06"];
//     NSArray *qty   = @[@10, @30, @20, @50, @40, @60];
//     GraphPopupView *popup = [[GraphPopupView alloc] 
//     initWithTitle:@"Welcome!" 
//     message:@"Here is your performance graph."
//              dates:dates
//           orderQty:qty
//     ];
//     [popup showInView:self.view];
// }
- (IBAction)btnGraphClicked:(UIBarButtonItem *)sender {

    NSLog(@"SCHEME DATA = %@", self.schemeData);

    NSString *category = [self.schemeData[@"category"] description];
    NSString *title = [self.schemeData[@"scheme_name"] description];
    NSString *message = [self.schemeData[@"scheme_name"] description];
    
    NSMutableArray *dates = [NSMutableArray array];
    NSMutableArray *qty   = [NSMutableArray array];
    NSMutableArray *yAxisValues = [NSMutableArray array];

    [yAxisValues addObject:@0];
    [dates addObject:@""];
    [qty addObject:@0];

    NSArray *achievements = self.schemeData[@"achievements"];
    CGFloat highestQty = 0;

    if ([achievements isKindOfClass:[NSArray class]]) {

        for (NSDictionary *item in achievements) {

            // Date -> DD
            NSString *dateStr = [item[@"date"] description];
            if (dateStr.length >= 10) {
                NSString *finalDate = [dateStr substringWithRange:NSMakeRange(8, 2)];
                [dates addObject:finalDate];
            }

            // Qty
            id qtyObj = item[@"lifting_qty"];
            CGFloat lift = [qtyObj respondsToSelector:@selector(floatValue)] ? [qtyObj floatValue] : 0;

            [qty addObject:@(lift)];

            if (lift > highestQty) {
                highestQty = lift;
            }
        }
    }


   if ([[category lowercaseString] isEqualToString:@"single"]) {

        NSDictionary *slabDetails = self.schemeData[@"slab_details"];
        int step = [slabDetails[@"applicable_qty"] intValue];  // e.g. 60

        if (step > 0) {

            int limit = highestQty + step;   // ensures point always in range

            for (int i = step; i <= limit; i += step) {
                [yAxisValues addObject:@(i)];
            }
        }
    } else if ([[category lowercaseString] isEqualToString:@"multiple"]) {

        NSDictionary *slabDetails = self.schemeData[@"slab_details"];
        NSArray *slabs = slabDetails[@"slabs"];

        if ([slabs isKindOfClass:[NSArray class]]) {

            slabs = [slabs sortedArrayUsingDescriptors:@[
                [NSSortDescriptor sortDescriptorWithKey:@"lifting_max" ascending:YES]
            ]];

            for (NSDictionary *slab in slabs) {
                NSNumber *max = @([slab[@"lifting_max"] intValue]);
                [yAxisValues addObject:max];
            }
        }
    }

    NSLog(@"FINAL DATES = %@", dates);
    NSLog(@"FINAL QTY   = %@", qty);

    if (dates.count == 0 || qty.count == 0) {
        NSLog(@"No data available for graph");
        return;
    }

    GraphPopupView *popup = [[GraphPopupView alloc]
                             initWithTitle:title
                             message:message
                             dates:dates
                             orderQty:qty
                             yAxisValues:yAxisValues];

    [popup showInView:self.view];
}

- (void)btnDownloadClicked:(id)sender {
    [self savePDF];
}

#pragma mark - WebView Delegate

- (void)webView:(WKWebView *)webView didStartProvisionalNavigation:(WKNavigation *)navigation {
    activityIndicator.hidden = NO;
    [activityIndicator startAnimating];
}

- (void)webView:(WKWebView *)webView didFinishNavigation:(WKNavigation *)navigation {
    activityIndicator.hidden = YES;
    [activityIndicator stopAnimating];
    
    if ([self.strTitle isEqualToString:@"Ledger"]) {
        NSString *strCurrentUrl = webView.URL.absoluteString;
        if ([strCurrentUrl isEqualToString:@"https://starsaathi.com/SAP/ccavenue_pg/the_success_url.php"] ||
            [strCurrentUrl isEqualToString:@"https://starsaathi.com/SAP/ccavenue_pg/the_success_url_ne.php"]) {
            
            UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil
                                                                           message:@"Thank You your Transaction is successful"
                                                                    preferredStyle:UIAlertControllerStyleAlert];
            [alert addAction:[UIAlertAction actionWithTitle:@"Ok"
                                                      style:UIAlertActionStyleDefault
                                                    handler:^(UIAlertAction * _Nonnull action) {
                for (UIViewController *controller in self.navigationController.viewControllers) {
                    if ([controller isKindOfClass:[DashboardViewController class]]) {
                        [self.navigationController popToViewController:controller animated:YES];
                        return;
                    }
                }
            }]];
            [self presentViewController:alert animated:true completion:nil];
            
        } else if ([strCurrentUrl isEqualToString:@"https://starsaathi.com/SAP/ccavenue_pg/the_cancel_url.php"] ||
                   [strCurrentUrl isEqualToString:@"https://starsaathi.com/SAP/ccavenue_pg/the_cancel_url_ne.php"]){
            
            UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil
                                                                           message:@"Transaction Cancelled. Please Try Again."
                                                                    preferredStyle:UIAlertControllerStyleAlert];
            [alert addAction:[UIAlertAction actionWithTitle:@"Ok"
                                                      style:UIAlertActionStyleDefault
                                                    handler:^(UIAlertAction * _Nonnull action) {
                for (UIViewController *controller in self.navigationController.viewControllers) {
                    if ([controller isKindOfClass:[DashboardViewController class]]) {
                        [self.navigationController popToViewController:controller animated:YES];
                        return;
                    }
                }
            }]];
            [self presentViewController:alert animated:true completion:nil];
        }
    }
}

- (void)webView:(WKWebView *)webView didFailNavigation:(WKNavigation *)navigation withError:(NSError *)error {
    activityIndicator.hidden = YES;
    [activityIndicator stopAnimating];
}

#pragma mark - Helper Method (NSURLSession version)

-(void)savePDF {
    
    NSURL *URL = [NSURL URLWithString:self.strWeblink];
    NSURLSessionConfiguration *configuration = [NSURLSessionConfiguration defaultSessionConfiguration];
    NSURLSession *session = [NSURLSession sessionWithConfiguration:configuration];
    
    NSURLRequest *request = [NSURLRequest requestWithURL:URL];
    
    [SVProgressHUD show];
    
    NSURLSessionDownloadTask *downloadTask = [session downloadTaskWithRequest:request
                                                            completionHandler:^(NSURL *location, NSURLResponse *response, NSError *error) {
        dispatch_async(dispatch_get_main_queue(), ^{
            [SVProgressHUD dismiss];
        });
        
        if (error) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [SVProgressHUD showErrorWithStatus:error.localizedDescription];
            });
        } else {
            NSFileManager *fileManager = [NSFileManager defaultManager];
            NSURL *documentsDirectoryURL = [[fileManager URLsForDirectory:NSDocumentDirectory inDomains:NSUserDomainMask] lastObject];
            NSURL *destinationURL = [documentsDirectoryURL URLByAppendingPathComponent:@"Invoice_statement.pdf"];
            
            // Remove existing file if exists
            [fileManager removeItemAtURL:destinationURL error:nil];
            
            NSError *moveError = nil;
            [fileManager moveItemAtURL:location toURL:destinationURL error:&moveError];
            if (moveError) {
                dispatch_async(dispatch_get_main_queue(), ^{
                    [SVProgressHUD showErrorWithStatus:moveError.localizedDescription];
                });
            } else {
                dispatch_async(dispatch_get_main_queue(), ^{
                    [self loadPDFAndShare];
                });
            }
        }
    }];
    
    [downloadTask resume];
}

-(void)loadPDFAndShare {
    
    NSFileManager *fileManager = [NSFileManager defaultManager];
    NSURL *documentPath = [[fileManager URLsForDirectory:NSDocumentDirectory inDomains:NSUserDomainMask] lastObject];
    NSString *strDocPath = [documentPath.path stringByAppendingPathComponent:@"Invoice_statement.pdf"];
    
    if ([fileManager fileExistsAtPath:strDocPath]) {
        NSData *data = [NSData dataWithContentsOfFile:strDocPath];
        UIActivityViewController *activityController = [[UIActivityViewController alloc] initWithActivityItems:@[data]
                                                                                         applicationActivities:nil];
        [self presentViewController:activityController animated:true completion:nil];
    }
}

@end
