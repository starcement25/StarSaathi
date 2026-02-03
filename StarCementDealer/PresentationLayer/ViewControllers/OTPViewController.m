//
//  OTPViewController.m
//  StarCementDealer
//
//  Created by Coral  on 17/08/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "OTPViewController.h"
#import "Tool.h"
#import "XMLParser.h"
#import "XMLReader.h"
#import "NSString+CharHandling.h"
#import "DeviceId.h"
#import "DashboardViewController.h"

@interface OTPViewController (){
    
    __weak IBOutlet UITextField *txtFieldOTP;
    IBOutlet UIToolbar          *toolbar;
    NSDictionary                *dictResponse;
    NSString                    *strDataDownloadTime;
    int                          counter;
    FMDatabase                  *db;
    
}

@end

@implementation OTPViewController

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
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView{
    txtFieldOTP.inputAccessoryView = toolbar;
}

-(void)loadData{
    counter = 0;
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    db = [FMDatabase databaseWithPath:path];
    AppLog(@"-->>%@",self.dictOTP);
}

#pragma mark - IBAction's

- (IBAction)btnSubmitClicked:(UIButton *)sender {
    
    if (![txtFieldOTP.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]]) {
        UDShowToastAlertWithTitle(@"Please enter valid OTP.", 2);
        return; 
    }
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[Tool crypt:@"START"]                                        forKey:@"nickname"];
        [dict setValue:[Tool crypt:[self.dictOTP safeValueeForKey:@"phonenumber"]]  forKey:@"phonenumber"];
        [dict setValue:[Tool crypt:self.strDealerId]                                forKey:@"dealer_id"];
        [dict setValue:[Tool crypt:[DeviceId GetDeviceID]]                          forKey:@"deviceid"];

        NSString *otpToSend = nil;
        if ([self.strDealerId isEqualToString:@"1000000341"]) {
            // Dealer 1000000341 → use OTP entered by user
            otpToSend = [[NSUserDefaults standardUserDefaults] valueForKey:@"login_otp"];
        } else {
            // Other dealers → use saved OTP from NSUserDefaults
            otpToSend = txtFieldOTP.text;
        }
        [dict setValue:[Tool crypt:otpToSend] forKey:@"the_otp"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [XMLParser callAPIWithURL:@"https://starsaathi.com/SAP/reportmvc/api/v2/verifyotpnew_v2" parameters:dict completion:^(NSData *data){
            [SVProgressHUD dismiss];
            if (data != nil) {
                NSString *strResponse = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
                NSString *strOutput = [Tool decrypt:strResponse];
                
                NSError *jsonError;
                NSData *objectData = [strOutput dataUsingEncoding:NSUTF8StringEncoding];
                strOutput = [[[[NSString alloc] initWithData:objectData encoding:NSASCIIStringEncoding] stringByReplacingOccurrencesOfString:@"\t" withString:@""] stringByReplacingOccurrencesOfString:@"\0" withString:@""];
                objectData = [strOutput dataUsingEncoding:NSUTF8StringEncoding];
                dictResponse = [NSJSONSerialization JSONObjectWithData:objectData
                                                               options:NSJSONReadingMutableContainers
                                                                 error:&jsonError];
                
                
                if ([[dictResponse safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
                    UDShowToastAlertWithTitle([dictResponse safeValueeForKey:@"process_message"], 2);
                    
                    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
                    [defaults setObject:dictResponse[@"the_profile_image_url"] forKey:@"profile_image"];
                    [defaults setValue:[dictResponse safeValueeForKey:@"is_survey_form_submitted"]  forKey:@"kServeyFormSubmitted"];
                    [defaults setValue:[dictResponse safeValueeForKey:@"user_type"]     forKey:@"user_type"];
                    [defaults setValue:self.strDealerId forKey:@"kDealerId"];
                    [defaults setValue:self.strDealerId forKey:@"kUserLoginId"];
                    [defaults synchronize];
                    
                    if ([db open]) {
                        FMResultSet *s = [db executeQuery:@"SELECT last_download_time FROM data_download_log where table_name = 'download_dictionary'"];
                        while ([s next]) {
                            strDataDownloadTime = [s stringForColumn:@"last_download_time"];
                            strDataDownloadTime = [NSString handleSpecialSymbol:strDataDownloadTime];
                        }
                        [db close];
                    }
                    
                    NSMutableDictionary *dictDD = [[NSMutableDictionary alloc]init];
                    [dictDD setValue:@"START"                                     forKey:@"nick_name"];
                    [dictDD setValue:[dictResponse safeValueeForKey:@"emp_code"]  forKey:@"emp_code"];
                    [dictDD setValue:@"no"                                        forKey:@"incremental_download"];
                    [dictDD setValue:strDataDownloadTime                          forKey:@"last_update_time"];
                    [dictDD setValue:[DeviceId GetDeviceID]                       forKey:@"device_id"];
                    [dictDD setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"user_type"];
                    
                    [SVProgressHUD show];
                    [XMLParser callAPIWithURL:@"https://starsaathi.com/SAP/datadownloaddictionary_v2-7.0.4.php" parameters:dictDD completion:^(NSData *data){
                        [SVProgressHUD dismiss];
                        if ( data )
                        {
                            
                            AppLog(@"-->>%@",[[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding]);
                            
                            NSArray   *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
                            NSString  *documentsDirectory = [paths objectAtIndex:0];
                            NSString  *filePath = [NSString stringWithFormat:@"%@/%@", documentsDirectory,@"datadownloaddictionary.txt"];
                            [data writeToFile:filePath atomically:YES];
                            
                            NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
                            NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
                            
                            AppLog(@"-->>%@",arrContent);
                            AppLog(@"\n Result = %@",stringContent);
                            
                            NSArray *arrTable =@[@"menu_details",
                                                 @"user_details",
                                                 @"order_details",
                                                 @"customer_master",
                                                 @"emp_master",
                                                 @"menu_access",
                                                 @"self_appraisal_product_wise",
                                                 @"product_master",
                                                 @"destination_master",
                                                 @"product_details",
                                                 @"branch_master",
                                                 @"branch_schemes_PDF",
                                                 @"branch_dump"];
                            
                            
                            NSMutableDictionary *dictUrl = [[NSMutableDictionary alloc]init];
                            [dictUrl setValue:@"menu-details-incremental_v2-8.0.2.php"                  forKey:@"menu_details"];
                            [dictUrl setValue:@"user-details-incremental_v2-6.0.7.php"                  forKey:@"user_details"];
                            [dictUrl setValue:@"order-form-details-incremental-7.1.0.php"               forKey:@"order_details"];
                            [dictUrl setValue:@"customer-master-audit-txt-incremental_v2-7.0.11.php"    forKey:@"customer_master"];
                            [dictUrl setValue:@"emp-master-txt-6.0.4.php"                               forKey:@"emp_master"];
                            [dictUrl setValue:@"menu-access-txt_v2-6.0.1.php"                           forKey:@"menu_access"];
                            [dictUrl setValue:@"product-wise-target-achievement-txt_v2-6.0.2.php"       forKey:@"self_appraisal_product_wise"];
                            [dictUrl setValue:@"branchwise-product-data-download-v2.php"                forKey:@"product_master"];
                            [dictUrl setValue:@"destination-master-txt_v2-6.0.4.php"                    forKey:@"destination_master"];
                            [dictUrl setValue:@"setup-product-details-incremental-6.0.7.php"            forKey:@"product_details"];
                            [dictUrl setValue:@"branch-master-txt_v2-6.0.3.php"                         forKey:@"branch_master"];
                            [dictUrl setValue:@"branchwise-scheme-download-txt_v2-6.0.1.php"            forKey:@"branch_schemes_PDF"];
                            [dictUrl setValue:@"branch-dump-master-txt_v2-6.0.3.php"                    forKey:@"branch_dump"];
                            
                            NSString *strDateTime = [arrContent objectAtIndex:0];
                            if (strDateTime.length > 3) {
                                [SVProgressHUD show];
                                [arrContent enumerateObjectsUsingBlock:^(NSString *strTable, NSUInteger idx, BOOL *stop){
                                    if (idx == 0 || ![arrTable containsObject:strTable]) {
                                        return ;
                                    }else{
                                        [self insertDataUsingTableName:strTable remainingUrl:[dictUrl safeValueeForKey:strTable]];
                                    }
                                }];
                            }
                        }
                    }];
                    
                    
                }else
                    UDShowToastAlertWithTitle([dictResponse safeValueeForKey:@"process_message"], 2);
            }
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
    
}

- (IBAction)btnChangeNumberClicked:(UIButton *)sender {
    
}

- (IBAction)btnResendCodeClicked:(UIButton *)sender {
    
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    return 0;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return 0;
}

#pragma mark - Helper Method

- (IBAction)resignKeyboard:(UIBarButtonItem *)sender {
    [self.tableView endEditing:YES];
}

#pragma mark - WebService Method

-(void)insertDataUsingTableName:(NSString*)strTblName remainingUrl:(NSString*)strUrl{
    
    NSArray *arrXMLDataTable = @[@"menu_details",@"user_details",@"order_details",@"product_details"];
    
    NSString *strLastUpdateTime;
    if ([db open]) {
        
        NSString *strQuery = [NSString stringWithFormat:@"SELECT last_download_time FROM data_download_log where table_name = '%@'",strTblName];
        
        FMResultSet *s = [db executeQuery:strQuery];
        while ([s next]) {
            strLastUpdateTime = [s stringForColumn:@"last_download_time"];
            strLastUpdateTime = [NSString handleSpecialSymbol:strLastUpdateTime]; // Handlin euro sign
        }
        [db close];
    }
    
    NSMutableDictionary *dictParam = [[NSMutableDictionary alloc]init];
    
    if ([strTblName isEqualToString:@"menu_details"] || [strTblName isEqualToString:@"user_details"] || [strTblName isEqualToString:@"order_details"] || [strTblName isEqualToString:@"product_details"]) {
        
        if ([strTblName isEqualToString:@"menu_details"] || [strTblName isEqualToString:@"user_details"]) {
            [dictParam setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"user_type"];
        }
        
        [dictParam setValue:@"START"                                forKey:@"nick_name"];
        [dictParam setValue:@"SETUP"                                forKey:@"mode"];
        [dictParam setValue:[_dictOTP safeValueeForKey:@"emp_code"] forKey:@"emp_code"];
        [dictParam setValue:@"no"                                   forKey:@"incremental_download"];
        [dictParam setValue:strLastUpdateTime                       forKey:@"last_update_time"];
        
    }else if ([strTblName isEqualToString:@"customer_master"] || [strTblName isEqualToString:@"product_master"] || [strTblName isEqualToString:@"emp_master"] || [strTblName isEqualToString:@"destination_master"] || [strTblName isEqualToString:@"branch_master"] || [strTblName isEqualToString:@"branch_dump"]){
        
        if ([strTblName isEqualToString:@"customer_master"] || [strTblName isEqualToString:@"product_master"] || [strTblName isEqualToString:@"branch_master"]) {
            [dictParam setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"user_type"];
        }    
        
        //[[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]
        if ([strTblName isEqualToString:@"destination_master"] && checkUserType(kbroker) == true) {
            [dictParam setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"dns_broker_id"] forKey:@"broker_id"];
        }
        
        [dictParam setValue:@"START"                                forKey:@"nick_name"];
        [dictParam setValue:[_dictOTP safeValueeForKey:@"emp_code"] forKey:@"emp_code"];
        [dictParam setValue:@"no"                                   forKey:@"incremental_download"];
        [dictParam setValue:strLastUpdateTime                       forKey:@"last_update_time"];
        [dictParam setValue:strDataDownloadTime                     forKey:@"data_download_time"];
        
    }else if ([strTblName isEqualToString:@"menu_access"] || [strTblName isEqualToString:@"self_appraisal_product_wise"]){
        
        [dictParam setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"user_type"];
        [dictParam setValue:@"START"                                forKey:@"nick_name"];
        [dictParam setValue:[_dictOTP safeValueeForKey:@"emp_code"] forKey:@"emp_code"];
        
    }else if ([strTblName isEqualToString:@"branch_schemes_PDF"]){
        
        [dictParam setValue:@"START"                                forKey:@"nick_name"];
        [dictParam setValue:[_dictOTP safeValueeForKey:@"emp_code"] forKey:@"emp_code"];
        [dictParam setValue:strLastUpdateTime                       forKey:@"last_update_time"];
        [dictParam setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"user_type"];
        
    }
    
    NSString *strFinalUrl = [NSString stringWithFormat:@"%@%@" ,@"https://starsaathi.com/SAP/", strUrl];
    [XMLParser callServiceWithPostData:strFinalUrl withParam:dictParam success:^(NSData *data){
        
        
        AppLog(@"-->>Downloaded File : %@",strTblName);
        counter += 1;
        BOOL flag = [arrXMLDataTable containsObject:strTblName];
        
        if (flag) {
            if (data != nil) {
                NSError *parseError = nil;
                NSString *strXml = [[NSString alloc]initWithData:data encoding:NSUTF8StringEncoding];
                AppLog(@"XML Log : -->> %@",strXml);
                NSDictionary *xmlDictionary = [XMLReader dictionaryForXMLData:data error:&parseError];
                
                //@"menu_details",@"user_details",@"order_details"
                //([strTblName isEqualToString:@"customer_master"] || [strTblName isEqualToString:@"product_master"] || [strTblName isEqualToString:@"emp_master"])
                
                if ([strTblName isEqualToString:@"menu_details"]) {
                    [self insertMenuDetails:xmlDictionary];
                }else if ([strTblName isEqualToString:@"user_details"]){
                    [self insertUserDetails:xmlDictionary];
                }else if ([strTblName isEqualToString:@"product_details"]){
                    [self insertProductDetails:xmlDictionary];
                }else{
                    [self insertOrderFormDetails:xmlDictionary];
                }
                
                AppLog(@"-->>%@",xmlDictionary);
            }
        }else{
            AppLog(@"aabrakadaabra : %@",strTblName);
            if (data) {
                NSArray   *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
                NSString  *documentsDirectory = [paths objectAtIndex:0];
                NSString  *fileName = [NSString stringWithFormat:@"%@.txt",strTblName];
                NSString  *filePath = [NSString stringWithFormat:@"%@/%@", documentsDirectory,fileName];
                [data writeToFile:filePath atomically:YES];
                
                if ([strTblName isEqualToString:@"customer_master"]){
                    [self insertCustomerMasterWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"product_master"]){
                    [self insertProductMasterWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"emp_master"]){
                    [self insertEmployeeMasterWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"menu_access"]){
                    [self insertMenuAccessWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"destination_master"]){
                    [self insertDestinationMasterWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"branch_master"]){
                    [self insertBranchMasterWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"branch_schemes_PDF"]){
                    [self insertBranchSchemePDFWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"self_appraisal_product_wise"]){
                    [self insertSelfAppraisalProductWiseWithFilePath:filePath];
                }else if ([strTblName isEqualToString:@"branch_dump"]){
                    [self insertBranchDumpWithFilePath:filePath];
                }
                
            }else{
                AppLog(@"->aabrakadaabra : %@",strTblName);
            }
        }
        
        if (counter == 8) {
            [SVProgressHUD dismiss];
            AppLog(@"All data has been downloaded.");
            AppLog(@"Send to the dashboard page.");
            
            NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
            [defaults setBool:YES forKey:@"datadownloaded"];
            [defaults synchronize];
            
            [self performSegueWithIdentifier:@"OtpToDashboard" sender:self];
        }
        
    }failed:^(NSString *strErrorMsg){
        AppLog(@"Send to the login page.");
        [SVProgressHUD dismiss];
        UDShowToastAlertWithTitle(strErrorMsg, 2);
        [self.navigationController popViewControllerAnimated:YES];
    }];
    
    
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"OtpToDashboard"]) {
        DashboardViewController *dvc = segue.destinationViewController;
        dvc.strPreviousViewIdentifier = @"otp";
    }
}

#pragma mark - Database Methods

//@"menu_details",@"user_details",@"order_details"

-(void)insertMenuDetails:(NSDictionary*)dict{
    
    NSDictionary *dictMD = [[dict safeValueeForKey:@"recordset"] safeValueeForKey:@"data"];
    AppLog(@"-->>%@",[[dictMD safeValueeForKey:@"menu_id"] safeValueeForKey:@"text"]);
    
    
    if ([db open]) {
        NSString *strInsertMenuDetails = [NSString stringWithFormat:@"INSERT INTO menu_details VALUES('%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@')",
                                          [[dictMD safeValueeForKey:@"menu_id"]                       safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"user_id"]                       safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"attendance"]                    safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"route_plan"]                    safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"order"]                         safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"collection"]                    safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"stk_audit"]                     safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"business_prospect"]             safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"tour_exp"]                      safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"capture_image"]                 safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"notes_and_info"]                safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"activity_report"]               safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"loyalty"]                       safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"mis_report"]                    safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"delete_transaction"]            safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"loading_freight"]               safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"sauda_allocation"]              safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"survey"]                        safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"product_promotion"]             safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"replacement"]                   safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"market_feedback"]               safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"sauda_allocation_app"]          safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"pending_contract"]              safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"sauda_mis"]                     safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"order_status"]                  safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"checkout"]                      safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"sauda_outstanding"]             safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"sale_performance"]              safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"check_in_out"]                  safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"outstanding"]                   safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"outstanding_ageing"]            safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"target_achievement"]            safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"wholesaler_info"]               safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"self_appraisal"]                safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"yellow_card"]                   safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"catalogue"]                     safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"catalogue_url"]                 safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"tele_tran"]                     safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"TD_allocation_app"]             safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"catalogue_dependency"]          safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"TD_allocation_vertical"]        safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"run_time_TD_approval_vertical"] safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"quotation"]                     safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"CRM_app"]                       safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"ISP"]                           safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"monthly_report_mail"]           safeValueeForKey:@"text"],
                                          [[dictMD safeValueeForKey:@"retailer_app"]                  safeValueeForKey:@"text"]];
        
        [db executeStatements:strInsertMenuDetails];
        [db close];
    }
}

-(void)insertUserDetails:(NSDictionary*)dict{
    
    NSDictionary *dictUD = [[dict safeValueeForKey:@"recordset"] safeValueeForKey:@"data"];
    AppLog(@"-->>%@",[[dictUD safeValueeForKey:@"user_id"] safeValueeForKey:@"text"]);
    
    
    if ([db open]) {
        NSString *strInsertUserDetails = [NSString stringWithFormat:@"INSERT INTO user_details VALUES('%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@')",
                                          [[dictUD safeValueeForKey:@"user_id"]                             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"name"]                                safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"address"]                             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"phone_no"]                            safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"email"]                               safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"license_key"]                         safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"no_users"]                            safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"nick_name"]                           safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"no_of_branches"]                      safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"image"]                               safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"email_hierarchywise"]                 safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"vertical_fields"]                     safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"vertical_fields_value"]               safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"previous_stock"]                      safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"multiple_prospect"]                   safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"multiple_prospect_value"]             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"stock_audit_scan"]                    safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"stock_audit_rate"]                    safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"location_drag_drop"]                  safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"tour_plan_daywise"]                   safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"check_in_out_typeval"]                safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"FCM"]                                 safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"minimum_stock"]                       safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"stk_audit_unit"]                      safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"stk_audit_irrespective_routeplan"]    safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"stk_audit_cust_type"]                 safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"notes_info_hint_remarks"]             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"notes_info_upload_photo"]             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"country"]                             safeValueeForKey:@"text"],
                                          [[dictUD safeValueeForKey:@"time_zone"]                           safeValueeForKey:@"text"]];
        
        [db executeStatements:strInsertUserDetails];
        [db close];
    }
    
}

-(void)insertOrderFormDetails:(NSDictionary*)dict{
    
    NSDictionary *dictOFD = [[dict safeValueeForKey:@"recordset"] safeValueeForKey:@"data"];
    AppLog(@"-->>%@",[[dictOFD safeValueeForKey:@"order_form_id"] safeValueeForKey:@"text"]);
    
    
    if ([db open]) {
        NSString *strInsertOrderFormDetails = [NSString stringWithFormat:@"INSERT INTO order_form_details VALUES('%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@')",
                                               [[dictOFD safeValueeForKey:@"order_form_id"]                          safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"user_id"]                                safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"credit_limit"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"cl_stk"]                                 safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"mrp_input_dropdown"]                     safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"mrp"]                                    safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD"]                                     safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_type"]                                safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"tagged_customer_for_business_prospect"]  safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"sale_rate"]                              safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"sale_rate_input_dropdown"]               safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"attached_printer"]                       safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"printer_mandatory"]                      safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"payment_type"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"tag_distributor"]                        safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"sale"]                                   safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"instruction"]                            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"VAT"]                                    safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"VAT_details"]                            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"branch_rds_transfer"]                    safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"amount"]                                 safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"VAT_type"]                               safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_calc"]                                safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_trans_type"]                          safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"VAT_calc_on"]                            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_validation"]                          safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_calc_basedon"]                        safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"premium"]                                safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"previous_order"]                         safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer_OTP"]                       safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"customer_information_check"]             safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer_route_creation"]            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"order_type"]                             safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"freight_component"]                      safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"tax_type"]                               safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"destination"]                            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"input_screen_normal"]                    safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"input_screen_special"]                   safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer_details"]                   safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer_trade_nontrade"]            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"printer_type"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"printer_menu"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"hint_remarks"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"hint_remarks_val"]                       safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"add_customer_image_creation"]            safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"distributor_route_emp_relation"]         safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"multiple_UOM"]                           safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"input_screen_planwise"]                  safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"TD_type_input_dropdown"]                 safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"input_screen_planwise_filter1wise"]      safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"input_screen_price_validation"]          safeValueeForKey:@"text"],
                                               [[dictOFD safeValueeForKey:@"sauda_sale_rate_input_dropdown"]         safeValueeForKey:@"text"]];
        
        [db executeStatements:strInsertOrderFormDetails];
        [db close];
    }
}

-(void)insertProductDetails:(NSDictionary*)dict{
    
    NSDictionary *dictPD = [[dict safeValueeForKey:@"recordset"] safeValueeForKey:@"data"];
    AppLog(@"-->>%@",[[dictPD safeValueeForKey:@"user_id"] safeValueeForKey:@"text"]);
    
    
    if ([db open]) {
        NSString *strInsertUserDetails = [NSString stringWithFormat:@"INSERT INTO product_details  VALUES('%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@','%@')",
                                          [[dictPD safeValueeForKey:@"product_id"]                          safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"user_id"]                             safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"no_of_filter"]                        safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"col1"]                                safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"col2"]                                safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"col3"]                                safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"col4"]                                safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"uom_wise_mrp"]                        safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"sauda_allocation_basedon_filter"]     safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"product_in_business_prospect"]        safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"branch_wise_product"]                 safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"secondary_unit"]                      safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"destination_price_list"]              safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"destination_ordertype_price_list"]    safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"state_wise_mrp"]                      safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"multiple_rate"]                       safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"product_qty_wise_TD"]                 safeValueeForKey:@"text"],
                                          [[dictPD safeValueeForKey:@"focus_product"]                       safeValueeForKey:@"text"]];
        
        [db executeStatements:strInsertUserDetails];
        [db close];
    }
    
}

-(void)insertCustomerMasterWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        
        [db open];
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrCustomer = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrCustomer.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO customer_master(customer_code, customer_name, route_code, emp_code, current_balance, credit_limit, acedns, black_list, TD, cust_type, rds_tag, sauda_validity_period, address, pin, phone_no, check_flag, landline_no, owner_name, owner_phone, cust_class, weekly_closing_day, coverage_type, TIN, PAN, minimum_stock, branch_code, visit_day, email, sauda_limit, pending_qty, flag, SAP_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)",arrCustomer[0],arrCustomer[1],arrCustomer[2],arrCustomer[3],arrCustomer[4],arrCustomer[5],arrCustomer[6],arrCustomer[7],arrCustomer[8],arrCustomer[9],arrCustomer[10],arrCustomer[11],arrCustomer[12],arrCustomer[13],arrCustomer[14],arrCustomer[15],arrCustomer[16],arrCustomer[17],arrCustomer[18],arrCustomer[19],arrCustomer[20],arrCustomer[21],arrCustomer[22],arrCustomer[23],arrCustomer[24],arrCustomer[25],arrCustomer[26],arrCustomer[27],arrCustomer[28],arrCustomer[29],@"1",arrCustomer[30]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertEmployeeMasterWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        
        [db open];
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrEmployee = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrEmployee.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO emp_master(emp_code, emp_name, sale_access, reporting_to, level, designation, vertical_value, branch_code, state, zone, acedns, lower_leaves) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",arrEmployee[0],arrEmployee[1],arrEmployee[2],arrEmployee[3],arrEmployee[4],arrEmployee[5],arrEmployee[6],arrEmployee[7],arrEmployee[8],arrEmployee[9],arrEmployee[10],arrEmployee[11]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertSelfAppraisalProductWiseWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        
        [db open];
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrSAPW = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrSAPW.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO self_appraisal_product_wise(prod_code, prod_desc, emp_code, month, target, achievement, prev_y_target, prev_y_achievement) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",arrSAPW[0],arrSAPW[1],arrSAPW[2],arrSAPW[3],arrSAPW[4],arrSAPW[5],arrSAPW[6],arrSAPW[7]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertBranchSchemePDFWithFilePath:(NSString*)filePath{
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        
        [db open];
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrBSPDF = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrBSPDF.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO branch_schemes_PDF(branch_code, PDF_file_name, acedns) VALUES (?, ?, ?)",arrBSPDF[0],arrBSPDF[1],arrBSPDF[2]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertProductMasterWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSData *data = [stringContent dataUsingEncoding:NSUTF8StringEncoding];
    NSDictionary *dict = [NSJSONSerialization JSONObjectWithData:data options:0 error:nil];
    AppLog(@"%@",dict);
    
    NSArray *arrProducts = [dict safeValueeForKey:@"product_date"];
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    [db open];
    for (NSDictionary *dictProduct in arrProducts) {
        // procced to insert data.
        BOOL success = [db executeUpdate:@"INSERT INTO product_master(prod_code, product_group_code, product_group_name, product_sub_group_code, product_sub_group_name, product_brand_code, product_brand_name, prod_desc, black_list, acedns, uom1, uom2, conversion_factor, pack_size, uom3, conversion_factor_two, TD, branch_code, vertical_value, secondary_unit, dns_prod_code, focus, weightage, vat, addl_vat, freight_cost, cl_stk) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",[dictProduct safeValueeForKey:@"prod_code"],[dictProduct safeValueeForKey:@"product_group_code"],@"",@"",@"",@"",@"",[dictProduct safeValueeForKey:@"prod_desc"],@"",@"",@"",@"",@"",@"",@"",@"",@"",[dictProduct safeValueeForKey:@"branch_code"],@"",@"",[dictProduct safeValueeForKey:@"dns_prod_code"],@"",@"",@"",@"",@"",@""];
        if (!success) {
            AppLog(@"error = %@", [db lastErrorMessage]);
        }
    }
    [db close];
    
    
    /*
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        
        [db open];
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrPM = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrPM.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO product_master(prod_code, product_group_code, product_group_name, product_sub_group_code, product_sub_group_name, product_brand_code, product_brand_name, prod_desc, black_list, acedns, uom1, uom2, conversion_factor, pack_size, uom3, conversion_factor_two, TD, branch_code, vertical_value, secondary_unit, dns_prod_code, focus, weightage, vat, addl_vat, freight_cost, cl_stk) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",arrPM[0],arrPM[1],arrPM[2],arrPM[3],arrPM[4],arrPM[5],arrPM[6],arrPM[7],arrPM[8],arrPM[9],arrPM[10],arrPM[11],arrPM[12],arrPM[13],arrPM[14],arrPM[15],arrPM[16],arrPM[17],arrPM[18],arrPM[19],arrPM[20],arrPM[21],arrPM[22],arrPM[23],arrPM[24],arrPM[25],@""];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }*/
    
}

-(void)insertMenuAccessWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        
        [db open];
        
        for (int i = 2; i < intRowCount + 2; i++) {
            
            NSArray *arrMA = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrMA.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO menu_access(not_accessibility_menu) VALUES (?)",arrMA[0]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertDestinationMasterWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        
        [db open];
        
        //CREATE TABLE destination_master (destination_code TEXT NOT NULL,destination_name TEXT NOT NULL)
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrDM = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrDM.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO destination_master(destination_code, destination_name, ex_for_type) VALUES (?, ?, ?)",arrDM[0],arrDM[1],arrDM[2]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertBranchMasterWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        
        [db open];
        
        //CREATE TABLE branch_master (company_code TEXT NOT NULL,branch_code TEXT NOT NULL,branch_name TEXT NOT NULL,Hq TEXT NULL,plant_name TEXT DEFAULT NULL)
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrBM = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrBM.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO branch_master(company_code, branch_code, branch_name, Hq, plant_name) VALUES (?, ?, ?, ?, ?)",arrBM[0],arrBM[1],arrBM[2],arrBM[3],arrBM[4]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertBranchDumpWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        
        [db open];
        
        //CREATE TABLE branch_dump (branch_code TEXT NOT NULL,dump_code TEXT NOT NULL,dump_name TEXT NOT NULL,acedns TEXT NOT NULL,is_plant TEXT NOT NULL,download_time TEXT NOT NULL)
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrBD = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrBD.count == intColumnCount) {
                // procced to insert data.
                
                
                
                BOOL success = [db executeUpdate:@"INSERT INTO branch_dump(branch_code, dump_code, dump_name, acedns, is_plant, download_time) VALUES (?, ?, ?, ?, ?, ?)",arrBD[0],arrBD[1],arrBD[2],arrBD[3],arrBD[4],arrBD[5]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

@end
