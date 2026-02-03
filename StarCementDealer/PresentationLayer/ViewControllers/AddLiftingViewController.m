//
//  AddLiftingViewController.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 24/05/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import "AddLiftingViewController.h"
#import "DashboardViewController.h"
#import "ProductMasterBO.h"

@interface AddLiftingViewController ()<UITextFieldDelegate, UIPickerViewDelegate, UIPickerViewDataSource>{
    
    __weak IBOutlet UITextField *txtFieldLinkedDealer;
    __weak IBOutlet UITextField *txtFieldProdName;
    __weak IBOutlet UITextField *txtFieldQtyInBags;
    __weak IBOutlet UITextField *txtFieldLiftingDate;
    __weak IBOutlet UITextField *txtFieldChallanNumber;
    UIDatePicker *datePicker;
    NSMutableArray *arrProduct;
    UIPickerView *pickerViewProd;
}

@end

@implementation AddLiftingViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    
    self.title = @"Add Lifting";
    
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
    
    datePicker = [[UIDatePicker alloc]init];
    if (@available(iOS 13.4, *)) {
        datePicker.preferredDatePickerStyle = UIDatePickerStyleWheels;
    }
    [datePicker setDate:[NSDate date]];
    datePicker.datePickerMode = UIDatePickerModeDate;
    //    datePicker.minimumDate = [[NSCalendar currentCalendar] dateByAddingUnit:NSCalendarUnitDay value:-30 toDate:[NSDate date] options:0];
    //    datePicker.maximumDate = [NSDate date];
    [datePicker addTarget:self action:@selector(updateTextField:) forControlEvents:UIControlEventValueChanged];
    
    pickerViewProd = [[UIPickerView alloc] initWithFrame:CGRectZero];
    pickerViewProd.delegate = self;
    pickerViewProd.dataSource = self;
    
    [txtFieldProdName setInputView:pickerViewProd];
    [txtFieldLiftingDate setInputView:datePicker];
}

-(void)loadData {
    arrProduct = [[NSMutableArray alloc] init];
    [self wsLiftingDateValidation];
    [self getProductMaster];
    //[self loadProductMaster];
    txtFieldLinkedDealer.text = [[NSUserDefaults standardUserDefaults] valueForKey:@"kBelongDealerName"];
}

#pragma mark - Web Service

-(void)getProductMaster {
    if (APP_DELEGATE.isServerReachable) {
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"emp_code"];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"user_type"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/branchwise-product-data-download-v2.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    
                    NSArray *arrProducts = [dictResponse safeValueeForKey:@"product_date"];
                    
                    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
                    FMDatabase *db = [FMDatabase databaseWithPath:path];
                    [db open];
                    [db executeUpdate:@"DELETE FROM product_master"];
                    for (NSDictionary *dictProduct in arrProducts) {
                        // procced to insert data.
                        BOOL success = [db executeUpdate:@"INSERT INTO product_master(prod_code, product_group_code, product_group_name, product_sub_group_code, product_sub_group_name, product_brand_code, product_brand_name, prod_desc, black_list, acedns, uom1, uom2, conversion_factor, pack_size, uom3, conversion_factor_two, TD, branch_code, vertical_value, secondary_unit, dns_prod_code, focus, weightage, vat, addl_vat, freight_cost, cl_stk) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",[dictProduct safeValueeForKey:@"prod_code"],[dictProduct safeValueeForKey:@"product_group_code"],@"",@"",@"",@"",@"",[dictProduct safeValueeForKey:@"prod_desc"],@"",@"",@"",@"",@"",@"",@"",@"",@"",[dictProduct safeValueeForKey:@"branch_code"],@"",@"",[dictProduct safeValueeForKey:@"dns_prod_code"],@"",@"",@"",@"",@"",@""];
                        if (!success) {
                            AppLog(@"error = %@", [db lastErrorMessage]);
                        }
                    }
                    [db close];
                    [self loadProductMaster];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)wsLiftingDateValidation{
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"dealer_id"] forKey:@"customer_code"];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"user_type"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/lifting_date_validation_data.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    NSDictionary *dictLifting = [[dictResponse safeValueeForKey:@"lifting_validation_data"] objectAtIndex:0];
                    NSString *strValidationFrom = [dictLifting safeValueeForKey:@"validation_from"];
                    //NSString *strValidationTo = [dictLifting safeValueeForKey:@"validation_to"];
                    NSString *strValidationLastDate = [dictLifting safeValueeForKey:@"validation_last_date"];
                    NSString *strCurrentDate = [dictLifting safeValueeForKey:@"current_date"];
                    
                    APP_CONSTANTS.dateFormater.dateFormat = @"yyyy-MM-dd";
                    NSDate *dateFrom = [APP_CONSTANTS.dateFormater dateFromString:strValidationFrom];
                    //NSDate *dateTo = [APP_CONSTANTS.dateFormater dateFromString:strValidationTo];
                    NSDate *dateLast = [APP_CONSTANTS.dateFormater dateFromString:strValidationLastDate];
                    NSDate *dateCurrent = [APP_CONSTANTS.dateFormater dateFromString:strCurrentDate];
                    
                    datePicker.minimumDate = dateFrom;
                    datePicker.maximumDate = dateCurrent;
                    
                    BOOL flag = [self date:dateCurrent isBetweenDate:dateFrom andDate:dateLast];
                    if (!flag) {
                        UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil message:@"Please contact admin" preferredStyle:UIAlertControllerStyleAlert];
                        [alert addAction:[UIAlertAction actionWithTitle:@"Ok" style:UIAlertActionStyleCancel handler:^(UIAlertAction * _Nonnull action) {
                            [self.navigationController popViewControllerAnimated:true];
                        }]];
                        [self presentViewController:alert animated:true completion:nil];
                    }
                }else{
                    UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil message:[dictResponse valueForKey:@"process_message"] preferredStyle:UIAlertControllerStyleAlert];
                    [alert addAction:[UIAlertAction actionWithTitle:@"Ok" style:UIAlertActionStyleCancel handler:^(UIAlertAction * _Nonnull action) {
                        [self.navigationController popViewControllerAnimated:true];
                    }]];
                    [self presentViewController:alert animated:true completion:nil];
                }
                
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - IBAction's

- (IBAction)btnSubmitClicked:(id)sender {
    if (APP_DELEGATE.isServerReachable) {
        
        //linked_dealer_cust_code,sub_dealer_cust_code,prod_code,tot_bag_qty,lifting_date,challan_no
        
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[defaults valueForKey:@"kBelongDealerCode"] forKey:@"linked_dealer_cust_code"];
        [dict setValue:[defaults valueForKey:@"emp_code"] forKey:@"sub_dealer_cust_code"];
        [dict setValue:txtFieldProdName.accessibilityHint forKey:@"prod_code"];
        [dict setValue:txtFieldQtyInBags.text forKey:@"tot_bag_qty"];
        [dict setValue:convertDateFormat3(txtFieldLiftingDate.text) forKey:@"lifting_date"];
        [dict setValue:txtFieldChallanNumber.text forKey:@"challan_no"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_add_lifting.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    //                    txtFieldProdName.text = @"";
                    //                    txtFieldProdName.accessibilityHint = @"";
                    //                    txtFieldQtyInBags.text = @"";
                    //                    txtFieldLiftingDate.text = @"";
                    //                    txtFieldChallanNumber.text = @"";
                    //                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                    
                    [self.delegate reloadLiftingHistory];
                    [self.navigationController popViewControllerAnimated:true];
                    
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

-(void)btnBackClicked:(UIButton*)btn{
    //    DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
    //    [self.navigationController pushViewController:dvc animated:NO];
    [self.navigationController popViewControllerAnimated:true];
}

#pragma mark - UIPickerView Delegate and DataSource

- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView {
    return 1;
}

- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component {
    return arrProduct.count;
}

- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component {
    ProductMasterBO *PMB = arrProduct[row];
    return PMB.strProdDesc;
}

- (void)pickerView:(UIPickerView *)pickerView didSelectRow:(NSInteger)row inComponent:(NSInteger)component {
    ProductMasterBO *PMB = arrProduct[row];
    txtFieldProdName.text = PMB.strProdDesc;
    txtFieldProdName.accessibilityHint = PMB.strProdCode;
}

#pragma mark - UITextField Delegate

- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    return [textField resignFirstResponder];
}

#pragma mark - Database Method

-(void)loadProductMaster{
    
    if ([APP_CONSTANTS.db open]) {
        
        FMResultSet *s = [APP_CONSTANTS.db executeQuery:@"SELECT * FROM product_master"];
        
        while ([s next]) {
            //retrieve values for each record
            ProductMasterBO *PMB = [[ProductMasterBO alloc] init];
            
            PMB.strBranchCode           = [s stringForColumn:@"branch_code"];
            PMB.strProdCode             = [s stringForColumn:@"prod_code"];
            PMB.strDnsProdCode          = [s stringForColumn:@"dns_prod_code"];
            PMB.strProductGroupCode     = [s stringForColumn:@"product_group_code"];
            PMB.strProdSubGroupCode     = [s stringForColumn:@"product_sub_group_code"];
            PMB.strProdBrandCode        = [s stringForColumn:@"product_brand_code"];
            PMB.strProdDesc             = [s stringForColumn:@"prod_desc"];
            PMB.strClStk                = [s stringForColumn:@"cl_stk"];
            PMB.strUOM1                 = [s stringForColumn:@"UOM1"];
            PMB.strUOM2                 = [s stringForColumn:@"UOM2"];
            PMB.strUOM3                 = [s stringForColumn:@"UOM3"];
            PMB.strAcedns               = [s stringForColumn:@"acedns"];
            PMB.strBlackList            = [s stringForColumn:@"black_list"];
            PMB.strVerticalValue        = [s stringForColumn:@"vertical_value"];
            PMB.strFreightCost          = [s stringForColumn:@"freight_cost"];
            PMB.strFocus                = [s stringForColumn:@"focus"];
            PMB.strWeightage            = [s stringForColumn:@"weightage"];
            PMB.strVat                  = [s stringForColumn:@"vat"];
            PMB.strAddlVat              = [s stringForColumn:@"addl_vat"];
            PMB.strConversionFactor     = [s stringForColumn:@"conversion_factor"];
            PMB.strConversionFactorTwo  = [s stringForColumn:@"conversion_factor_two"];
            PMB.strSecondaryUnit        = [s stringForColumn:@"secondary_unit"];
            PMB.strPackSize             = [s stringForColumn:@"pack_size"];
            PMB.strTD                   = [s stringForColumn:@"TD"];
            
            [arrProduct addObject:PMB];
        }
        [APP_CONSTANTS.db close];
        [pickerViewProd reloadAllComponents];
    }
}

#pragma mark - Helper Method

-(void)setUpToolbar {
    
    UIToolbar *toolbar = [[UIToolbar alloc] initWithFrame:CGRectZero];
    toolbar.barStyle = UIBarStyleDefault;
    [toolbar sizeToFit];
    
    UIBarButtonItem *flexSpace = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:self action:nil];
    
    UIBarButtonItem *doneBtn = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone target:self action:@selector(resignPicker)];
    
    [toolbar setItems:@[flexSpace, doneBtn] animated:YES];
    
    txtFieldQtyInBags.inputAccessoryView = toolbar;
    txtFieldLiftingDate.inputAccessoryView = toolbar;
    txtFieldProdName.inputAccessoryView = toolbar;
    txtFieldChallanNumber.inputAccessoryView = toolbar;
}

-(void)resignPicker{
    [self.view endEditing:YES];
}

-(void)updateTextField:(UIDatePicker*)picker {
    txtFieldLiftingDate.text = [self formatDate:picker.date];
}

// Formats the date chosen with the date picker.
- (NSString *)formatDate:(NSDate *)date {
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateStyle:NSDateFormatterShortStyle];
    [dateFormatter setDateFormat:@"dd-MM-yyyy"];
    NSString *formattedDate = [dateFormatter stringFromDate:date];
    return formattedDate;
}

- (BOOL) date:(NSDate*)date isBetweenDate:(NSDate*)beginDate andDate:(NSDate*)endDate {
    return (([date compare:beginDate] != NSOrderedAscending) && ([date compare:endDate] != NSOrderedDescending));
}

@end
