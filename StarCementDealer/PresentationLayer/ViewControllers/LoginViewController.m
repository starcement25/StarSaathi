//
//  LoginViewController.m
//  StarCementDealer
//
//  Created by Coral  on 17/08/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "LoginViewController.h"
#import "Tool.h"
#import "XMLParser.h"
#import "OTPViewController.h"
#import "DeviceId.h"

@interface LoginViewController ()<UITextFieldDelegate>{
    
    __weak IBOutlet UITextField *txtFieldDealerId;
    __weak IBOutlet UITextField *txtFieldMobileNumber;
    IBOutlet UIToolbar *toolbar;
    __weak IBOutlet UIButton *btnRegister;
    NSDictionary *dictResponse;
}

@end

@implementation LoginViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [self.navigationController setNavigationBarHidden:NO animated:animated];
    self.navigationItem.hidesBackButton = YES;
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
}

#pragma mark - Initialization Method

-(void)designView{
    btnRegister.hidden = true;
    txtFieldMobileNumber.inputAccessoryView = toolbar;
}

-(void)loadData{
    [self wsShowHideRegBtn];
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    return 0;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return 0;
}

#pragma mark - UITextField Delegate

- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    [textField resignFirstResponder];
    return YES;
}

#pragma mark - IBAction's

- (IBAction)btnContinueClicked:(UIButton *)sender {
    [self login];
}

#pragma mark - Helper Method

- (IBAction)resignKeyboard:(UIBarButtonItem *)sender {
    [self.tableView endEditing:YES];
}

#pragma mark - Web Service

-(void)wsShowHideRegBtn {
    
    if (APP_DELEGATE.isServerReachable) {
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_show_hide_reg_btn.php" parameters:nil completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    if ([[dictResponse safeValueeForKey:@"is_show_reg_btn"] isEqualToString:@"YES"]) {
                        btnRegister.hidden = false;
                    }
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }
}

-(void)login{
    
    // dealer_id = D025
    // phonenumber = 9832069113
    
    if (![txtFieldDealerId.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]].length) {
        UDShowToastAlertWithTitle(@"Please enter dealer id.", 2);
        return;
    }else if (!validateNumber(txtFieldMobileNumber.text)){
        UDShowToastAlertWithTitle(@"Please enter mobile number.", 2);
        return;
    }
    
    NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
    [dict setValue:[Tool crypt:@"START"]                    forKey:@"nickname"];
    [dict setValue:[Tool crypt:txtFieldMobileNumber.text]   forKey:@"phonenumber"];
    [dict setValue:[Tool crypt:[DeviceId GetDeviceID]]      forKey:@"deviceid"];
    [dict setValue:[Tool crypt:txtFieldDealerId.text]       forKey:@"dealer_id"];
    
    if (APP_DELEGATE.isServerReachable) {
        [SVProgressHUD showWithStatus:@"Loading..."];
        [XMLParser callAPIWithURL:@"https://starsaathi.com/SAP/reportmvc/api/v2/checkloginnew_v2" parameters:dict completion:^(NSData *data){
            [SVProgressHUD dismiss];
            if (data != nil) {
                NSString *strResponse = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
                NSString *strOutput = [Tool decrypt:strResponse];
                AppLog(@"-->>%@",strOutput);
                
                /*{
                 "process_status": "YES",
                 "process_message": "OTP has been sent to your mobile number.",
                 "user_type": "sub dealer",
                 "emp_code": "C\/0046115",
                 "customer_code": "C\/0046115",
                 "dns_emp_code": "ADPC001",
                 "emp_name": "MOJIUL ALI PC (M6002200138)",
                 "sale_access": "PRIMARY",
                 "newpassword": "1234",
                 "deviceid": "",
                 "phonenumber": "9831722939",
                 "acedns": "Y",
                 "broker_id": "",
                 "dns_broker_id": "",
                 "contact_person": "",
                 "mail_id": "",
                 "brokerage_cost": "",
                 "state_code": "",
                 "otp_text": "OTP is 2377 for Star Saathi log in STAR CEMENT",
                 "belong_dealer_code": "C\/0046114",
                 "belong_dealer_dns_code": "ADH111",
                 "belong_dealer_name": "PC scheme (ADHIKARY ENTERPRISE)"
                 }*/
                
                NSError *jsonError;
                NSData *objectData = [strOutput dataUsingEncoding:NSUTF8StringEncoding];
                strOutput = [[[[NSString alloc] initWithData:objectData encoding:NSASCIIStringEncoding] stringByReplacingOccurrencesOfString:@"\t" withString:@""] stringByReplacingOccurrencesOfString:@"\0" withString:@""];
                objectData = [strOutput dataUsingEncoding:NSUTF8StringEncoding];
                dictResponse = [NSJSONSerialization JSONObjectWithData:objectData
                                                               options:NSJSONReadingMutableContainers
                                                                 error:&jsonError];
                
                if ([[dictResponse safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {

                    // Extract numeric OTP from "otp_text"
                    NSString *otpText = [dictResponse safeValueeForKey:@"otp_text"];
                    NSString *otp = nil;

                    if (otpText && otpText.length > 0) {
                        // Use regex to find the first number sequence
                        NSRegularExpression *regex = [NSRegularExpression regularExpressionWithPattern:@"\\d+" options:0 error:nil];
                        NSTextCheckingResult *match = [regex firstMatchInString:otpText options:0 range:NSMakeRange(0, otpText.length)];
                        if (match) {
                            otp = [otpText substringWithRange:match.range];
                        }
                    }
                    
                    //                    user_type = broker
                    //                    emp_code = BROKER005
                    //                    customer_code = ""
                    //                    dns_emp_code = ""
                    //                    emp_name = AMIT ENTERPRISE
                    //                    sale_access = PRIMARY
                    //                    deviceid = ""
                    //                    phonenumber = 7278212381
                    //                    acedns = Y
                    //                    broker_id = BR0005
                    //                    dns_broker_id = BROKER005
                    //                    contact_person = AMIT ENTERPRISE
                    //                    mail_id = manojtura@yahoo.co.in
                    //                    brokerage_cost = ""
                    //                    state_code = ""
                    
                    
                    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
                    [defaults setValue:[dictResponse safeValueeForKey:@"dns_emp_code"]  forKey:@"dealer_id"];
                    [defaults setValue:[dictResponse safeValueeForKey:@"emp_code"]      forKey:@"emp_code"];
                    [defaults setValue:[dictResponse safeValueeForKey:@"emp_name"]      forKey:@"emp_name"];
                    [defaults setValue:[dictResponse safeValueeForKey:@"phonenumber"]   forKey:@"phone_number"];
                    
                    [defaults setValue:[dictResponse safeValueeForKey:@"user_type"]     forKey:@"user_type"];
                    [defaults setValue:[dictResponse safeValueeForKey:@"customer_code"] forKey:@"customer_code"];
                    [defaults setValue:[dictResponse safeValueeForKey:@"broker_id"]     forKey:@"broker_id"];
                    [defaults setValue:[dictResponse safeValueeForKey:@"dns_broker_id"] forKey:@"dns_broker_id"];
                    
                    [defaults setValue:[dictResponse safeValueeForKey:@"belong_dealer_code"]        forKey:@"kBelongDealerCode"];
                    [defaults setValue:[dictResponse safeValueeForKey:@"belong_dealer_dns_code"]    forKey:@"kBelongDealerDnsCode"];
                    [defaults setValue:[dictResponse safeValueeForKey:@"belong_dealer_name"]        forKey:@"kBelongDealerName"];
                    [defaults setValue:[dictResponse safeValueeForKey:@"is_survey_form_submitted"]  forKey:@"kServeyFormSubmitted"];
     
                    if (otp) {
                        [defaults setValue:otp forKey:@"login_otp"];
                    }
     
                    [defaults synchronize];
                    
                    [self performSegueWithIdentifier:@"loginToOtp" sender:self];
                }else
                    UDShowToastAlertWithTitle([dictResponse safeValueeForKey:@"process_message"], 2);
            }
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
}

#pragma mark - Segue 

-(void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender{
    if ([segue.identifier isEqualToString:@"loginToOtp"]) {
        OTPViewController *otpvc = segue.destinationViewController;
        otpvc.dictOTP = dictResponse;
        otpvc.strDealerId = txtFieldDealerId.text;
    }
}

@end
