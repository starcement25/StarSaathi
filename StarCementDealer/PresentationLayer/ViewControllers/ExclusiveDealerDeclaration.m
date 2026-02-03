//
//  ExclusiveDealerDeclaration.m
//  StarCementDealer
//
//  Created by SBINFO on 01/07/25.
//  Copyright © 2025 Coral . All rights reserved.
//

#import "ExclusiveDealerDeclaration.h"
#import "DashboardViewController.h"
#import "UIAlertView+Category.h"

@interface ExclusiveDealerDeclaration ()<UITextFieldDelegate>{
    __weak IBOutlet UITextField *dealerField;
    __weak IBOutlet UITextField *branchField;
    __weak IBOutlet UITextField *liftingQtyField;
    __weak IBOutlet UIButton *submitButton;
    NSDictionary *dictResponse;
    NSArray *monthList;
    NSString *dealerName;
    NSString *dealerBranch;
    UIActivityIndicatorView *activityIndicator;
}
@end

@implementation ExclusiveDealerDeclaration

#pragma mark - View Life Cycle
- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self setupActivityIndicator]; 
    [self loadData];
}

-(void)designView {
    self.title = @"Exclusive Dealer Declaration";
    UINavigationBar *bar = [self.navigationController navigationBar];
    [bar setBackgroundColor:UIColorFromRGB(0xEB2228)];
    if (@available(iOS 13.0, *)) {
        UIView *statusBar = [[UIView alloc]initWithFrame:[UIApplication sharedApplication].keyWindow.windowScene.statusBarManager.statusBarFrame] ;
        statusBar.backgroundColor = UIColorFromRGB(0xEB2228);
        [[UIApplication sharedApplication].keyWindow addSubview:statusBar];
    }
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"back_arrow"] style:UIBarButtonItemStylePlain target:self action:@selector(btnBackClicked:)];
    btnBack.tintColor = [UIColor whiteColor];
    self.navigationItem.leftBarButtonItem = btnBack;
}

- (void)setupActivityIndicator {
    activityIndicator = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleLarge];
    activityIndicator.center = self.view.center;
    activityIndicator.hidesWhenStopped = YES;
    [self.view addSubview:activityIndicator];
}

- (void)loadData {
    [self requestForMonthList];
}

#pragma mark - pre define data show and month list show
- (void)requestForMonthList {
    [activityIndicator startAnimating];
    [[UIApplication sharedApplication] beginIgnoringInteractionEvents];

    NSString *strDealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
    NSString *urlString = [NSString stringWithFormat:@"https://starsaathi.com/SAP/api_exclusive_get_month_list.php?customer_id=%@", strDealerId];
    NSURL *url = [NSURL URLWithString:urlString];
    NSURLSessionDataTask *task = [[NSURLSession sharedSession]
        dataTaskWithURL:url
      completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
        dispatch_async(dispatch_get_main_queue(), ^{
             [self requestForDealerDetails];
        });

        if (error || data == nil) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showAlertWithTitle:@"Error" message:@"Failed to fetch month list."];
            });
            return;
        }
        NSError *jsonError;
        NSDictionary *json = [NSJSONSerialization JSONObjectWithData:data options:0 error:&jsonError];
        if (jsonError || ![json[@"process_status"] isEqualToString:@"YES"]) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showAlertWithTitle:@"Error" message:@"Invalid data received."];
            });
            return;
        }
        monthList = json[@"months"];
        dispatch_async(dispatch_get_main_queue(), ^{
            [self showMonthPickerPopup];
        });
    }];
    [task resume];
}

- (void)requestForDealerDetails {
    NSString *strDealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
    NSString *urlString = [NSString stringWithFormat:@"https://starsaathi.com/SAP/api_get_exclusive_dealername.php?customer_id=%@", strDealerId];
    
    NSURL *url = [NSURL URLWithString:urlString];
    NSURLSessionDataTask *task = [[NSURLSession sharedSession]
        dataTaskWithURL:url
        completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
            dispatch_async(dispatch_get_main_queue(), ^{
            [activityIndicator stopAnimating];
            [[UIApplication sharedApplication] endIgnoringInteractionEvents];
        });

        if (error || data == nil) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showAlertWithTitle:@"Error" message:@"Failed to fetch dealer details."];
            });
            return;
        }
        NSError *jsonError;
        NSDictionary *json = [NSJSONSerialization JSONObjectWithData:data options:0 error:&jsonError];
        
        NSString *status = json[@"process_status"];
                if ([status isEqualToString:@"YES"]) {
                    NSString *dealerNameValue = json[@"customer_name"];
                    NSString *dealerBranchValue = json[@"branch_name"];

                    dispatch_async(dispatch_get_main_queue(), ^{
                        dealerName = dealerNameValue;
                        dealerBranch = dealerBranchValue;
                        dealerField.text = dealerNameValue;
                        branchField.text = dealerBranchValue;
                    });
                } else {
                    dispatch_async(dispatch_get_main_queue(), ^{
                        NSString *message = json[@"process_message"] ?: @"Something went wrong.";
                        [self showAlertWithTitle:@"Error" message:message];
                    });
                }
    }];
    [task resume];
}

#pragma mark - pick month and check this available or not
- (void)showMonthPickerPopup {
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:@"Select Month" message:nil preferredStyle:UIAlertControllerStyleActionSheet];
    for (NSDictionary *month in monthList) {
        NSString *title = month[@"month_name"];
        UIAlertAction *action = [UIAlertAction actionWithTitle:title style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {  
            [self handleMonthSelection:month];
        }];
        [alert addAction:action];
    }
    UIAlertAction *cancel = [UIAlertAction actionWithTitle:@"Cancel" style:UIAlertActionStyleCancel handler:nil];
    [alert addAction:cancel];

    [self presentViewController:alert animated:YES completion:nil];
}

- (void)handleMonthSelection:(NSDictionary *)monthDict {
    BOOL isApplied = [monthDict[@"is_applied"] boolValue];
    BOOL cutoff = [monthDict[@"cutoff"] boolValue];
    NSString *message = monthDict[@"message"];
    NSString *monthName = monthDict[@"month_name"];
    NSString *monthKey = monthDict[@"key"];

    if (!isApplied && cutoff) {
        [[NSUserDefaults standardUserDefaults] setObject:monthKey forKey:@"SelectedMonthKey"];
        [[NSUserDefaults standardUserDefaults] synchronize];
    } else {
        [self showToastMessage:message];
    }
}

#pragma mark - button press
- (void)btnBackClicked:(UIButton*)btn{
    
    DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
    [self.navigationController pushViewController:dvc animated:NO];
    
}

- (IBAction)btnContinueClicked:(id)sender {
    [self requestForSubmitRequest];
}

#pragma mark - Submit api call
- (void)requestForSubmitRequest {
    NSString *dealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
    NSString *dealerNameValue = dealerField.text;
    NSString *branchValue = branchField.text;
    NSString *qtyValue = liftingQtyField.text;
    NSString *monthValue = [[NSUserDefaults standardUserDefaults] valueForKey:@"SelectedMonthKey"];

    if (dealerNameValue.length == 0 || branchValue.length == 0 || qtyValue.length == 0 || monthValue.length == 0) {
        [self showAlertWithTitle:@"Error" message:@"All fields are required."];
        return;
    }

    [activityIndicator startAnimating];
    [[UIApplication sharedApplication] beginIgnoringInteractionEvents];

    NSDictionary *params = @{
        @"customer_id": dealerId,
        @"dealer_name": dealerNameValue,
        @"branch": branchValue,
        @"lifting_qty": qtyValue,
        @"month": monthValue
    };

    NSError *error;
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:params options:0 error:&error];
    if (error) {
        [self showAlertWithTitle:@"Error" message:@"Failed to prepare request."];
        return;
    }

    NSURL *url = [NSURL URLWithString:@"https://starsaathi.com/SAP/api_exclusive_data_save.php"];
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:url];
    [request setHTTPMethod:@"POST"];
    [request setValue:@"application/json" forHTTPHeaderField:@"Content-Type"];
    [request setHTTPBody:jsonData];

    NSURLSessionDataTask *task = [[NSURLSession sharedSession]
        dataTaskWithRequest:request
        completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
        dispatch_async(dispatch_get_main_queue(), ^{
            [activityIndicator stopAnimating];
            [[UIApplication sharedApplication] endIgnoringInteractionEvents];
        });


            if (error || data == nil) {
                dispatch_async(dispatch_get_main_queue(), ^{
                    [self showAlertWithTitle:@"Error" message:@"Network error. Please try again."];
                });
                return;
            }

            NSError *jsonError;
            NSDictionary *json = [NSJSONSerialization JSONObjectWithData:data options:0 error:&jsonError];
            if (jsonError) {
                dispatch_async(dispatch_get_main_queue(), ^{
                    [self showAlertWithTitle:@"Error" message:@"Invalid response from server."];
                });
                return;
            }

            NSString *status = json[@"process_status"];

            dispatch_async(dispatch_get_main_queue(), ^{
                if ([status isEqualToString:@"Yes"]) {
                    [self showAlertWithTitle1:@"Success" message: @"Declaration submitted successfully."];
                } else {
                    [self showAlertWithTitle1:@"Error" message: json[@"error"] ?: @"Failed to submit declaration."];
                }
            });
        }];
    [task resume];
}

#pragma mark - Toast and alert
- (void)showAlertWithTitle:(NSString *)title message:(NSString *)message {
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:title
                                                                   message:message
                                                            preferredStyle:UIAlertControllerStyleAlert];

    UIAlertAction *ok = [UIAlertAction actionWithTitle:@"OK"
                                                 style:UIAlertActionStyleDefault
                                               handler:nil];

    [alert addAction:ok];
    [self presentViewController:alert animated:YES completion:nil];
}

- (void)showAlertWithTitle1:(NSString *)title message:(NSString *)message {
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:title
                                                                   message:message
                                                            preferredStyle:UIAlertControllerStyleAlert];

    UIAlertAction *ok = [UIAlertAction actionWithTitle:@"OK"
                                                 style:UIAlertActionStyleDefault
                                               handler:^(UIAlertAction * _Nonnull action) {
        // Navigate to DashboardViewController
        DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
        [self.navigationController pushViewController:dvc animated:NO];
    }];

    [alert addAction:ok];
    [self presentViewController:alert animated:YES completion:nil];
}

- (void)showToastMessage:(NSString *)message {
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil
                                                                   message:message
                                                            preferredStyle:UIAlertControllerStyleAlert];

    [self presentViewController:alert animated:YES completion:^{
        dispatch_after(dispatch_time(DISPATCH_TIME_NOW, 1.5 * NSEC_PER_SEC), dispatch_get_main_queue(), ^{
            [alert dismissViewControllerAnimated:YES completion:nil];
            
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showMonthPickerPopup];
            });
            
        });
    }];
}

@end
