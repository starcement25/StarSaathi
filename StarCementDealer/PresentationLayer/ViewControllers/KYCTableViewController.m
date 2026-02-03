#import "KYCTableViewController.h"
#import "DashboardViewController.h"
#import "UIAlertView+Category.h"

@interface KYCTableViewController ()<UITextFieldDelegate>{
    
    __weak IBOutlet UITextField *txtFieldWhatsApp;
    __weak IBOutlet UITextField *txtFieldDOB;
    __weak IBOutlet UITextField *txtFieldDOM;
    __weak IBOutlet UITextField *txtFieldEmail;
    UITextField                 *txtFieldUniversal;
    UIDatePicker                *datePicker;
}

@end

@implementation KYCTableViewController

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

-(void)designView {
    self.title = @"KYC";
    
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
    
    datePicker = [[UIDatePicker alloc]init];
    if (@available(iOS 13.4, *)) {
        datePicker.preferredDatePickerStyle = UIDatePickerStyleWheels;
    }
    [datePicker setDate:[NSDate date]];
    datePicker.datePickerMode = UIDatePickerModeDate;
    datePicker.maximumDate = [NSDate date];
    [datePicker addTarget:self action:@selector(updateTextField:) forControlEvents:UIControlEventValueChanged];
    
    [txtFieldDOB setInputView:datePicker];
    [txtFieldDOM setInputView:datePicker];
    
    
    UIToolbar *toolbar = [[UIToolbar alloc] initWithFrame:CGRectZero];
    toolbar.barStyle = UIBarStyleDefault;
    [toolbar sizeToFit];
    
    UIBarButtonItem *flexSpace = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:self action:nil];
    
    UIBarButtonItem *doneBtn = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone target:self action:@selector(resignPicker)];
    
    [toolbar setItems:@[flexSpace, doneBtn] animated:YES];
    
    txtFieldDOB.inputAccessoryView = toolbar;
    txtFieldDOM.inputAccessoryView = toolbar;
    txtFieldWhatsApp.inputAccessoryView = toolbar;
    
}

-(void)loadData{
    [self getKYC];
}

#pragma mark - IBAction's

-(void)btnBackClicked:(UIButton*)btn{
    
    DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
    [self.navigationController pushViewController:dvc animated:NO];
    
}

- (IBAction)btnUpdateClicked:(UIButton *)sender {

    if (!validateNumber(txtFieldWhatsApp.text)) {
        UIAlertView *alert  = UDShowAlertWithTitle(nil, @"Please enter valid mobile number");
        alert.delegate      = self;
        alert.alertRespond  = ^(NSInteger index)
        {
            [txtFieldWhatsApp becomeFirstResponder];
        };
        return;
    }else if (![txtFieldDOB.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]].length){
        UIAlertView *alert  = UDShowAlertWithTitle(nil, @"Please enter dob");
        alert.delegate      = self;
        alert.alertRespond  = ^(NSInteger index)
        {
            [txtFieldDOB becomeFirstResponder];
        };
        return;
    }else if (![txtFieldDOM.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]].length){
        UIAlertView *alert  = UDShowAlertWithTitle(nil, @"Please enter dom");
        alert.delegate      = self;
        alert.alertRespond  = ^(NSInteger index)
        {
            [txtFieldDOM becomeFirstResponder];
        };
        return;
    }else if (!validateEmail(txtFieldEmail.text)){
        UIAlertView *alert  = UDShowAlertWithTitle(nil, @"Please enter valid email");
        alert.delegate      = self;
        alert.alertRespond  = ^(NSInteger index)
        {
            [txtFieldEmail becomeFirstResponder];
        };
        return;
    }
    [self.view endEditing:YES];
    AppLog(@"validation completed.");
    
    [self updateKYC];
}

#pragma mark - UITextField Delegate

- (BOOL)textFieldShouldReturn:(UITextField *)textField{
   return [textField resignFirstResponder];
}

- (void)textFieldDidBeginEditing:(UITextField *)textField{
    if (textField == txtFieldDOB || textField == txtFieldDOM) {
        txtFieldUniversal = textField;
        [APP_CONSTANTS.dateFormater setDateFormat:@"dd-MM-yyyy"];
        NSDate *date = [APP_CONSTANTS.dateFormater dateFromString:txtFieldUniversal.text];
        if(date == nil) {
            // do stuff
            AppLog(@"-->> Date is empty");
            return;
        }
        [datePicker setDate:date];
    }
}

#pragma mark - Helper Method

-(void)resignPicker{
    [self.view endEditing:YES];
}

// Formats the date chosen with the date picker.
- (NSString *)formatDate:(NSDate *)date
{
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateStyle:NSDateFormatterShortStyle];
    [dateFormatter setDateFormat:@"dd-MM-yyyy"];
    NSString *formattedDate = [dateFormatter stringFromDate:date];
    return formattedDate;
}

-(void)updateTextField:(UIDatePicker*)picker {
    txtFieldUniversal.text = [self formatDate:picker.date];
}

#pragma mark - Web Service

-(void)updateKYC {
    if (APP_DELEGATE.isServerReachable) {
        //emp_code,whatsapp_no,dob,dom,email_id
        //NSString *strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
        
//        NSString *strEmpCode;
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
//        }else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"sub dealer"]){
//            strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
//        }else{
//            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
//            FMDatabase *db = [FMDatabase databaseWithPath:path];
//            if ([db open]) {
//                FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
//                while ([s next]) {
//                    strEmpCode = [s stringForColumn:@"customer_code"];
//                }
//                [db close];
//            }
//        }
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode    forKey:@"emp_code"];
        [dict setValue:txtFieldWhatsApp.text        forKey:@"whatsapp_no"];
        [dict setValue:txtFieldDOB.text             forKey:@"dob"];
        [dict setValue:txtFieldDOM.text             forKey:@"dom"];
        [dict setValue:txtFieldEmail.text           forKey:@"email_id"];
        
        [SVProgressHUD showWithStatus:@"Updating..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_save_employee_kyc.php" parameters:dict completion:^(NSDictionary *dictResponse){
            [SVProgressHUD dismiss];
            if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            }else
                UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
        }];
        
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)getKYC {
    if (APP_DELEGATE.isServerReachable) {
        //emp_code
        //NSString *strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
        
//        NSString *strEmpCode;
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
//        }else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"sub dealer"]){
//            strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
//        }else{
//            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
//            FMDatabase *db = [FMDatabase databaseWithPath:path];
//            if ([db open]) {
//                FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
//                while ([s next]) {
//                    strEmpCode = [s stringForColumn:@"customer_code"];
//                }
//                [db close];
//            }
//        }
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"emp_code"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_show_employee_kyc.php" parameters:dict completion:^(NSDictionary *dictResponse){
            [SVProgressHUD dismiss];
            if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                NSDictionary *dictKYC = [dictResponse safeValueeForKey:@"employee_kyc_data"];          
                
                [self setKYCValue:dictKYC];
            }else
                UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
        }];
        
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - Set Method

-(void)setKYCValue:(NSDictionary*)dictKYC{
    
//    {
//        "emp_code": "E0594",
//        "whatsapp_no": "0000000000",
//        "dob": "08-02-2005",
//        "dom": "08-02-2019",
//        "email_id": "test@gmail.com"
//    }
    
    txtFieldWhatsApp.text   = [dictKYC safeValueeForKey:@"whatsapp_no"];
    txtFieldDOB.text        = [dictKYC safeValueeForKey:@"dob"];
    txtFieldDOM.text        = [dictKYC safeValueeForKey:@"dom"];
    txtFieldEmail.text      = [dictKYC safeValueeForKey:@"email_id"];
}

@end
