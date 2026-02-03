//
//  KismatKiBoriViewController.m
//  StarCementDealer
//
//  Created by SBINFO on 11/07/25.
//  Copyright © 2025 Coral . All rights reserved.
//

#import "KismatKiBoriViewController.h"
#import "DashboardViewController.h"
#import "UIAlertView+Category.h"

@interface KismatKiBoriViewController () <UITextFieldDelegate>{
    // UI
    __weak IBOutlet UITextField *fieldOwner;
    __weak IBOutlet UITextField *fieldMobile;
    __weak IBOutlet UITextField *fieldDate;
    __weak IBOutlet UITextField *fieldQty;
    __weak IBOutlet UISwitch *switchDraw;
    __weak IBOutlet UIButton *KKBSubmitBtn;
    __weak IBOutlet UIStackView *mainStack;
    __weak IBOutlet UIStackView *couponAdd;
    __weak IBOutlet UIStackView *couponStack;
    __weak IBOutlet UIScrollView *couponScrollView;

    UIActivityIndicatorView *activityIndicator;

    NSString *minQty;

    NSString *numberOfCouponAdd;
}
@end


@implementation KismatKiBoriViewController

#pragma mark - View Life Cycle
- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];

    couponAdd.hidden = YES;
    couponStack.hidden = YES;

    fieldMobile.delegate = self;

    numberOfCouponAdd=@"0";
    
    UITapGestureRecognizer *tap = [[UITapGestureRecognizer alloc]
                                   initWithTarget:self
                                   action:@selector(dismissKeyboard)];
    tap.cancelsTouchesInView = NO;
    [self.view addGestureRecognizer:tap];

    fieldDate.userInteractionEnabled = YES;
    UITapGestureRecognizer *tapGesture = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(showDatePicker)];
    [fieldDate addGestureRecognizer:tapGesture];

    [self setupActivityIndicator]; 
    [self loadData]; 
}


- (BOOL)textField:(UITextField *)textField
shouldChangeCharactersInRange:(NSRange)range
replacementString:(NSString *)string {

    if (textField == fieldMobile) {
        // Allow only digits
        NSCharacterSet *nonNumberSet = [[NSCharacterSet decimalDigitCharacterSet] invertedSet];
        if ([string rangeOfCharacterFromSet:nonNumberSet].location != NSNotFound) {
            return NO;
        }

        // Enforce max length 10
        NSString *newString = [textField.text stringByReplacingCharactersInRange:range withString:string];
        return newString.length <= 10;
    }

    return YES;
}
- (void)designView {
    self.title = @"Kishmat Ki Bori";
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
    [self requestForCheckMinQty];
}
- (void)dismissKeyboard {
    [self.view endEditing:YES];
}

#pragma mark - button press
- (IBAction)addNewCoupon:(id)sender {
    UIStackView *couponRow = [[UIStackView alloc] init];
    couponRow.axis = UILayoutConstraintAxisHorizontal;
    couponRow.spacing = 8;
    couponRow.distribution = UIStackViewDistributionFill;
    couponRow.alignment = UIStackViewAlignmentFill;
    
    // Create a text field for coupon input
    UITextField *couponField = [[UITextField alloc] init];
    couponField.borderStyle = UITextBorderStyleRoundedRect;
    couponField.placeholder = @"Enter Coupon";
    couponField.keyboardType = UIKeyboardTypeNumberPad;
    couponField.translatesAutoresizingMaskIntoConstraints = NO;
    [couponField.widthAnchor constraintEqualToConstant:300].active = YES;
    
    // Create a delete button
    UIButton *deleteButton = [UIButton buttonWithType:UIButtonTypeSystem];
    [deleteButton setTitle:@"Delete" forState:UIControlStateNormal];
    [deleteButton setTitleColor:[UIColor redColor] forState:UIControlStateNormal];
    deleteButton.translatesAutoresizingMaskIntoConstraints = NO;
    [deleteButton addTarget:self action:@selector(deleteCouponRow:) forControlEvents:UIControlEventTouchUpInside];

    // Add both views to the horizontal stack
    [couponRow addArrangedSubview:couponField];
    

    int count = [numberOfCouponAdd intValue];
    if(count>0){
        [couponRow addArrangedSubview:deleteButton];
    }

    NSString *quantity=fieldQty.text;
    int qtyValue = [quantity intValue];
    int minValue = [minQty intValue];
    if(qtyValue/minValue>count){
        [couponStack addArrangedSubview:couponRow];
        count += 1;
        numberOfCouponAdd=[NSString stringWithFormat:@"%d", count];
    }else{
        NSString *message=[NSString stringWithFormat:@"Your can't able to add more coupon."];
        [self showAlertWithTitle:@"Error" message:message];
    }
}
- (void)deleteCouponRow:(UIButton *)sender {
    UIView *rowToRemove = sender.superview;
    if (rowToRemove && [couponStack.arrangedSubviews containsObject:rowToRemove]) {
        [couponStack removeArrangedSubview:rowToRemove];
        [rowToRemove removeFromSuperview];
    }
}
- (IBAction)btnContinueClicked:(id)sender {
    [self checkDataForSubmit];
}
- (IBAction)switchDrawValueChanged:(UISwitch *)sender {
    if (sender.isOn) {
        NSString *quantity=fieldQty.text;
        int qtyValue = [quantity intValue];
        int minValue = [minQty intValue];
        if (qtyValue >= minValue) {
            [self showHideCouponStack:@"NO"];
        }else{
            [sender setOn:NO animated:YES];
            NSString *message=[NSString stringWithFormat:@"Your purchase quantity must be more than %@ otherwise you are not able to add coupon.",minQty];
            [self showAlertWithTitle:@"Error" message:message];
        }
    } else {
        [self showHideCouponStack:@"YES"];
        for (UIView *subview in couponStack.arrangedSubviews) {
            [couponStack removeArrangedSubview:subview];
            [subview removeFromSuperview];
        }
        numberOfCouponAdd=@"0";
    }
}
- (void)showDatePicker {
    UIDatePicker *datePicker = [[UIDatePicker alloc] init];
    datePicker.datePickerMode = UIDatePickerModeDate;
    
    if (@available(iOS 13.4, *)) {
        datePicker.preferredDatePickerStyle = UIDatePickerStyleWheels;
    }

    // Set maximum date to today (no future)
    datePicker.maximumDate = [NSDate date];

    // Create alert controller to hold the date picker
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:@"Select Date"
                                                                   message:@"\n\n\n\n\n\n\n\n\n"
                                                            preferredStyle:UIAlertControllerStyleActionSheet];

    [alert.view addSubview:datePicker];

    // Add constraints manually to datePicker inside alert
    datePicker.translatesAutoresizingMaskIntoConstraints = NO;
    [NSLayoutConstraint activateConstraints:@[
        [datePicker.centerXAnchor constraintEqualToAnchor:alert.view.centerXAnchor],
        [datePicker.topAnchor constraintEqualToAnchor:alert.view.topAnchor constant:40],
    ]];

    UIAlertAction *okAction = [UIAlertAction actionWithTitle:@"Done" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
        NSDateFormatter *formatter = [[NSDateFormatter alloc] init];
        [formatter setDateFormat:@"yyyy-MM-dd"];
        NSString *dateString = [formatter stringFromDate:datePicker.date];
        fieldDate.text = dateString;
    }];

    UIAlertAction *cancelAction = [UIAlertAction actionWithTitle:@"Cancel" style:UIAlertActionStyleCancel handler:nil];

    [alert addAction:okAction];
    [alert addAction:cancelAction];

    // Present the alert
    [self presentViewController:alert animated:YES completion:nil];
}

#pragma mark - VIEW HIDE
- (void)checkDataForSubmit {
    NSString *ownerName = fieldOwner.text;
    NSString *ownerMobileName = fieldMobile.text;
    NSString *dateOfPurchase = fieldDate.text;
    NSString *purchaseQty = fieldQty.text;

    int qtyValue = [purchaseQty intValue];
    int minValue = [minQty intValue];
    NSCharacterSet *nonDigitSet = [[NSCharacterSet decimalDigitCharacterSet] invertedSet];

    if([ownerName isEqualToString:@""]){
        NSString *message=[NSString stringWithFormat:@"Please enter House Owner Name."];
        [self showAlertWithTitle:@"Error" message:message];
    }else if([ownerMobileName isEqualToString:@""]){
        NSString *message=[NSString stringWithFormat:@"Please enter House Owner Contact Number."];
        [self showAlertWithTitle:@"Error" message:message];
    }else if (ownerMobileName.length != 10 || [ownerMobileName rangeOfCharacterFromSet:nonDigitSet].location != NSNotFound){
        NSString *message=[NSString stringWithFormat:@"Please enter valid House Owner Contact Number."];
        [self showAlertWithTitle:@"Error" message:message];
    }else if([dateOfPurchase isEqualToString:@""]){
        NSString *message=[NSString stringWithFormat:@"Please select Date of Purchase."];
        [self showAlertWithTitle:@"Error" message:message];
    }else if([purchaseQty isEqualToString:@""]){
        NSString *message=[NSString stringWithFormat:@"Please enter Purchase Qty."];
        [self showAlertWithTitle:@"Error" message:message];
    }else if(minValue>qtyValue){
        NSString *message=[NSString stringWithFormat:@"Purchase quantity must be more or equal than %d.",minValue];    
        [self showAlertWithTitle:@"Error" message:message];
    }else if([numberOfCouponAdd isEqualToString:@"0"]){
         NSString *message=[NSString stringWithFormat:@"You have to add a coupon to submit."];    
        [self showAlertWithTitle:@"Error" message:message];
    }else if ([self validateAllCouponFields]) {
        [self requestForBoriSubmit];
    }
}
- (BOOL)validateAllCouponFields {
    NSMutableSet *couponSet = [NSMutableSet set];

    for (UIView *subview in couponStack.arrangedSubviews) {
        if ([subview isKindOfClass:[UIStackView class]]) {
            UIStackView *rowStack = (UIStackView *)subview;
            for (UIView *element in rowStack.arrangedSubviews) {
                if ([element isKindOfClass:[UITextField class]]) {
                    UITextField *couponField = (UITextField *)element;
                    NSString *code = [couponField.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];

                    // Empty check
                    if (code.length == 0) {
                        [self showAlertWithTitle:@"Missing Coupon" message:@"Enter 6-digit coupon number to submit."];
                        return NO;
                    }

                    // Length check
                    if (code.length != 6) {
                        [self showAlertWithTitle:@"Invalid Coupon" message:@"Enter 6 digit coupon number."];
                        return NO;
                    }

                    // Duplicate check
                    if ([couponSet containsObject:code]) {
                        [self showAlertWithTitle:@"Duplicate Coupon" message:@"Enter 6-digit coupon number."];
                        return NO;
                    }

                    [couponSet addObject:code];
                }
            }
        }
    }

    return YES; // All fields are valid
}

#pragma mark - VIEW HIDE
- (void)showHideCouponStack:(NSString *)value {
    if([value isEqualToString:@"NO"]){
        dispatch_async(dispatch_get_main_queue(), ^{
                couponStack.hidden = NO;
                couponAdd.hidden=NO;
                fieldQty.enabled = NO;
            });
    }else{
        dispatch_async(dispatch_get_main_queue(), ^{
                couponStack.hidden = YES;
                couponAdd.hidden = YES;
                fieldQty.enabled = YES;
            });
    }
}

#pragma mark - API Calling
- (void)requestForCheckMinQty {
    [activityIndicator startAnimating];
    [[UIApplication sharedApplication] beginIgnoringInteractionEvents];
    NSString *strDealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
    // NSString *strDealerId = @"1000003658";
    NSString *urlString = [NSString stringWithFormat:@"https://starsaathi.com/SAP/admin/branch_wise_kismat_ki_bori_permission.php?customer_id=%@", strDealerId];
    NSURL *url = [NSURL URLWithString:urlString];
    NSURLSessionDataTask *task = [[NSURLSession sharedSession]
        dataTaskWithURL:url
      completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
        if (error || data == nil) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showAlertWithTitle:@"Error" message:@"Failed to fetch data contact to ADMIN."];
            });
            return;
        }
        NSError *jsonError;
        NSDictionary *json = [NSJSONSerialization JSONObjectWithData:data options:0 error:&jsonError];
        if (jsonError || ![json[@"status"] isEqualToString:@"YES"]) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showAlertWithTitle:@"Error" message:@"Invalid data received."];
            });
            return;
        }
        minQty = json[@"bag_quantity"];
        dispatch_async(dispatch_get_main_queue(), ^{
            [activityIndicator stopAnimating];
            [[UIApplication sharedApplication] endIgnoringInteractionEvents];
        });
    }];
    [task resume];
}
- (void)requestForBoriSubmit {
    [activityIndicator startAnimating];
    [[UIApplication sharedApplication] beginIgnoringInteractionEvents];

    NSArray *validCoupons = [self collectValidCoupons];
    if (validCoupons == nil) {
        [activityIndicator stopAnimating];
        return;
    }

    NSString *strDealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
    // NSString *strDealerId = @"1000003658";
    
    NSDictionary *payload = @{
        @"customer_id": strDealerId,
        @"house_owner_name": fieldOwner.text,
        @"house_owner_phone": fieldMobile.text,
        @"date_of_purchase": fieldDate.text,
        @"bags_quantity": fieldQty.text,
        @"has_coupon": @"Yes",
        @"coupons_details": validCoupons
    };
    NSError *error;
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:payload options:NSJSONWritingPrettyPrinted error:&error];
    NSString *jsonString = [[NSString alloc] initWithData:jsonData encoding:NSUTF8StringEncoding];
    
    NSURL *url = [NSURL URLWithString:@"https://starsaathi.com/SAP/admin/add_sikkim_consumer_scheme.php"];
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

            NSString *status = json[@"status"];

            dispatch_async(dispatch_get_main_queue(), ^{
                if ([status isEqualToString:@"success"]) {
                    [self showAlertWithTitle1:@"Success" message: @"Scheme added successfully."];
                } else {
                    [self showAlertWithTitle1:@"Error" message: @"Coupon number already submitted."];
                }
            });
        }];
    [task resume];
}
- (NSArray *)collectValidCoupons {
    NSMutableArray *couponArray = [NSMutableArray array];
    NSMutableSet *couponSet = [NSMutableSet set];

    for (UIView *subview in couponStack.arrangedSubviews) {
        if ([subview isKindOfClass:[UIStackView class]]) {
            UIStackView *row = (UIStackView *)subview;

            for (UIView *innerView in row.arrangedSubviews) {
                if ([innerView isKindOfClass:[UITextField class]]) {
                    UITextField *textField = (UITextField *)innerView;
                    NSString *couponCode = [textField.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];
                    [couponSet addObject:couponCode];
                    [couponArray addObject:@{@"coupon_number": couponCode}];
                }
            }
        }
    }

    return [couponArray copy];
}

#pragma mark - Toast and alert
- (void)showToastMessage:(NSString *)message {
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil
                                                                   message:message
                                                            preferredStyle:UIAlertControllerStyleAlert];

    [self presentViewController:alert animated:YES completion:^{
        dispatch_after(dispatch_time(DISPATCH_TIME_NOW, 1.5 * NSEC_PER_SEC), dispatch_get_main_queue(), ^{
            [alert dismissViewControllerAnimated:YES completion:nil];
            
            dispatch_async(dispatch_get_main_queue(), ^{
                
            });
            
        });
    }];
}
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

@end
