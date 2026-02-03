//
//  ConsumerLotterySchemeVC.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 21/01/25.
//  Copyright © 2025 Coral . All rights reserved.
//

#import "ConsumerLotterySchemeVC.h"
#import "DashboardViewController.h"

@interface ConsumerLotterySchemeVC ()<UITextFieldDelegate>{
    
    __weak IBOutlet UITextField *txtFieldOwnerName;
    __weak IBOutlet UITextField *txtFieldOwnerPhone;
    __weak IBOutlet UITextField *txtFieldDOP;
    __weak IBOutlet UITextField *txtFieldDhalaiMaster;
    __weak IBOutlet UITextField *txtFieldWeatherShield;
    __weak IBOutlet UILabel *lblTotalQty;
    
}

@end

@implementation ConsumerLotterySchemeVC

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    self.title = @"Consumer Lottery Scheme";
    [self setUpToolbar];
    
    UINavigationBar *bar = [self.navigationController navigationBar];
    [bar setBackgroundColor:UIColorFromRGB(0xEB2228)];
    
    if (@available(iOS 13.0, *)) {
        UIView *statusBar = [[UIView alloc]initWithFrame:[UIApplication sharedApplication].keyWindow.windowScene.statusBarManager.statusBarFrame] ;
        statusBar.backgroundColor = UIColorFromRGB(0xEB2228);
        [[UIApplication sharedApplication].keyWindow addSubview:statusBar];
    } else {
        // Fallback on earlier versions
    }
    
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"back_arrow"] style:UIBarButtonItemStylePlain target:self action:@selector(btnBackClicked:)];
    btnBack.tintColor = [UIColor whiteColor];
    self.navigationItem.leftBarButtonItem = btnBack;
}

-(void)loadData {
    
}

#pragma mark - IBAction's

-(void)btnBackClicked:(UIButton*)btn{
    DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
    [self.navigationController pushViewController:dvc animated:NO];
}

- (IBAction)btnSubmitClicked:(UIButton *)sender {
    
    if (![txtFieldOwnerName.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]].length) {
        UDShowToastAlertWithTitle(@"Please enter owner name", 2);
        return;
    }else if (!validateNumber(txtFieldOwnerPhone.text)){
        UDShowToastAlertWithTitle(@"Please enter valid owner phone number", 2);
        return;
    }else if (txtFieldDOP.text.length == 0){
        UDShowToastAlertWithTitle(@"Please enter date of purchase", 2);
        return;
    }else if (![self isAtLeastOneFieldFilled]){
        UDShowToastAlertWithTitle(@"Please enter qty in at least one of the product", 2);
        return;
    }
    
    if (APP_DELEGATE.isServerReachable) {
        
        // Get the current date and time
        NSDate *currentDate = [NSDate date];
        // Set the desired format for the time
        [APP_CONSTANTS.dateFormater setDateFormat:@"HH:mm:ss"];
        // Format the current date to get the time
        NSString *currentTime = [APP_CONSTANTS.dateFormater stringFromDate:currentDate];
        // Combine date and time strings
        NSString *strCombinedDateTime = [NSString stringWithFormat:@"%@ %@", APP_DELEGATE.strDateForNewOrderEnq, currentTime];
        
        NSString *strTransId = [NSString stringWithFormat:@"SO%@%@",[[NSUserDefaults standardUserDefaults] valueForKey:@"kUserLoginId"],getTimestamp()];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"kUserLoginId"] forKey:@"customer_id"];
        [dict setValue:strTransId forKey:@"trans_id"];
        [dict setValue:strCombinedDateTime forKey:@"datetime"];
        [dict setValue:txtFieldOwnerName.text forKey:@"customer_name"];
        [dict setValue:txtFieldOwnerPhone.text forKey:@"customer_phone_no"];
        [dict setValue:txtFieldDOP.text forKey:@"date_of_purchase"];
        [dict setValue:[self getQuantityFromTextField:txtFieldDhalaiMaster] forKey:@"dhalai_master_qty"];
        [dict setValue:[self getQuantityFromTextField:txtFieldWeatherShield] forKey:@"weather_shield_qty"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/save_consumer_scheme.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    
                    txtFieldOwnerName.text = @"";
                    txtFieldOwnerPhone.text = @"";
                    txtFieldDOP.text = @"";
                    txtFieldDhalaiMaster.text = @"";
                    txtFieldWeatherShield.text = @"";
                    lblTotalQty.text = @"0";
                    
                    UDShowToastAlertWithTitle([dictResponse safeValueeForKey:@"process_message"], 2);
                    
                     DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
                    [self.navigationController pushViewController:dvc animated:NO];

                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
    
}

#pragma mark - UITextFieldDelegate

- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    return [textField resignFirstResponder];
}

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
    if (textField == txtFieldOwnerPhone) {
        // Get the updated text
        NSString *updatedText = [textField.text stringByReplacingCharactersInRange:range withString:string];
        // Limit to 10 characters
        return updatedText.length <= 10;
    }else if (textField == txtFieldDhalaiMaster || textField == txtFieldWeatherShield){
        // Get the updated text
        NSString *updatedText = [textField.text stringByReplacingCharactersInRange:range withString:string];
        // Limit to 3 characters
        
        // Calculate the sum
        [self updateSumWithTextField:textField updatedText:updatedText];
        
        return updatedText.length <= 3;
    }
    return true;
}

#pragma mark - Helper Method

-(void)setUpToolbar {
    
    UIToolbar *toolbar = [[UIToolbar alloc] initWithFrame:CGRectZero];
    toolbar.barStyle = UIBarStyleDefault;
    [toolbar sizeToFit];
    UIBarButtonItem *flexSpace = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:self action:nil];
    UIBarButtonItem *btnDone = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone target:self action:@selector(resignPicker)];
    [toolbar setItems:@[flexSpace, btnDone] animated:YES];
    
    txtFieldOwnerPhone.inputAccessoryView = toolbar;
    txtFieldDOP.inputAccessoryView = toolbar;
    txtFieldDhalaiMaster.inputAccessoryView = toolbar;
    txtFieldWeatherShield.inputAccessoryView = toolbar;
    
    
    UIDatePicker *datePickerDOP = [[UIDatePicker alloc]init];
    if (@available(iOS 13.4, *)) {
        datePickerDOP.preferredDatePickerStyle = UIDatePickerStyleWheels;
    }
    [datePickerDOP setDate:[NSDate date]];
    datePickerDOP.datePickerMode = UIDatePickerModeDate;
    datePickerDOP.maximumDate = [NSDate date];
    [datePickerDOP addTarget:self action:@selector(setDate:) forControlEvents:UIControlEventValueChanged];
    
    // Set the minimum date to January 21, 2025
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateFormat:@"yyyy-MM-dd"];
    NSDate *minDate = [dateFormatter dateFromString:@"2025-01-21"];
    
    // Get the current date
    NSDate *currentDate = [NSDate date];
    
    // Set the date picker's minimum and maximum dates
    datePickerDOP.minimumDate = minDate;
    datePickerDOP.maximumDate = currentDate;
    
    txtFieldDOP.inputView = datePickerDOP;
    
}

-(void)resignPicker{
    [self.view endEditing:YES];
}

- (BOOL)isAtLeastOneFieldFilled {
    // Trim whitespace and check if either field contains meaningful input
    NSString *trimmedTextField1 = [txtFieldDhalaiMaster.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
    NSString *trimmedTextField2 = [txtFieldWeatherShield.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
    
    return trimmedTextField1.length > 0 || trimmedTextField2.length > 0;
}

// Formats the date chosen with the date picker.
- (NSString *)formatDate:(NSDate *)date {
    [APP_CONSTANTS.dateFormater setDateStyle:NSDateFormatterShortStyle];
    [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
    NSString *formattedDate = [APP_CONSTANTS.dateFormater stringFromDate:date];
    return formattedDate;
}

-(void)setDate:(UIDatePicker*)picker {
    txtFieldDOP.text = [self formatDate:picker.date];
}

- (NSString*)getQuantityFromTextField:(UITextField *)textField {
    // Trim whitespace from the text
    NSString *trimmedText = [textField.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
    
    // Check if the text is empty or contains non-numeric characters
    if (trimmedText.length == 0) {
        return @"0"; // Default to 0
    }
    
    // Convert the valid numeric string to an integer
    return trimmedText;
}

- (void)updateSumWithTextField:(UITextField *)textField updatedText:(NSString *)updatedText {
    
    NSInteger quantity1 = (textField == txtFieldDhalaiMaster) ? [self getQuantityFromString:updatedText] : [self getQuantityFromString:txtFieldDhalaiMaster.text];
    NSInteger quantity2 = (textField == txtFieldWeatherShield) ? [self getQuantityFromString:updatedText] : [self getQuantityFromString:txtFieldWeatherShield.text];
    
    NSInteger total = quantity1 + quantity2;
    
    // Update the result label
    lblTotalQty.text = [NSString stringWithFormat:@"%ld", (long)total];
}

- (NSInteger)getQuantityFromString:(NSString *)string {
    // Trim whitespace
    NSString *trimmedString = [string stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
    
    // Check if the text is numeric
    if (trimmedString.length == 0 || ![self isNumeric:trimmedString]) {
        return 0; // Default to 0
    }
    
    // Convert valid numeric string to integer
    return [trimmedString integerValue];
}

- (BOOL)isNumeric:(NSString *)string {
    NSCharacterSet *nonDigitCharacters = [[NSCharacterSet decimalDigitCharacterSet] invertedSet];
    return [string rangeOfCharacterFromSet:nonDigitCharacters].location == NSNotFound;
}

@end
