//
//  YellowCartViewController.m
//  StarCementDealer
//
//  Created by Apple on 16/03/20.
//  Copyright © 2020 Coral . All rights reserved.
//

#import "YellowCartViewController.h"
#import "DashboardViewController.h"
#import "SubDealerCell.h"
#import "CustomerMasterBO.h"
#import "ProductMasterBO.h"
#import "UIAlertView+Category.h"

@interface YellowCartViewController ()<UITextFieldDelegate, UITableViewDelegate, UITableViewDataSource>{
    
    __weak IBOutlet UITextField *txtFieldSubDealer;
    __weak IBOutlet UITextField *txtFieldDate;
    __weak IBOutlet UITextField *txtFieldChallanNo;
    __weak IBOutlet UITextField *txtFieldQuantity;
    __weak IBOutlet UITextField *txtFieldProduct;
    IBOutlet UIView *viewItemList;
    
    __weak IBOutlet UISearchBar *searchBarSubDealer;
    __weak IBOutlet UITableView *tblViewSubDealers;
    
    NSMutableArray *arrSubDealer;
    NSMutableArray *arrProduct;
    
    UITextField *activeTextField;
    
}

@end

@implementation YellowCartViewController

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
    
    self.title = @"Yellow Card";
    
    [tblViewSubDealers registerNib:[UINib nibWithNibName:@"SubDealerCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewSubDealers.separatorColor = [UIColor clearColor];
    
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"back_arrow"] style:UIBarButtonItemStylePlain target:self action:@selector(btnBackClicked:)];
    btnBack.tintColor = [UIColor whiteColor];
    self.navigationItem.leftBarButtonItem = btnBack;
    
    UIImageView *imgViewSD = [[UIImageView alloc]initWithFrame:CGRectMake(0, 0, 30, 30)];
    imgViewSD.image = [UIImage imageNamed:@"drop"];
    txtFieldSubDealer.rightView = imgViewSD;
    txtFieldSubDealer.rightViewMode = UITextFieldViewModeAlways;
    
    UIImageView *imgViewDate = [[UIImageView alloc]initWithFrame:CGRectMake(0, 0, 30, 30)];
    imgViewDate.image = [UIImage imageNamed:@"cal"];
    txtFieldDate.rightView = imgViewDate;
    txtFieldDate.rightViewMode = UITextFieldViewModeAlways;
    
    UIImageView *imgViewProduct = [[UIImageView alloc]initWithFrame:CGRectMake(0, 0, 30, 30)];
    imgViewProduct.image = [UIImage imageNamed:@"drop"];
    txtFieldProduct.rightView = imgViewProduct;
    txtFieldProduct.rightViewMode = UITextFieldViewModeAlways;
    
    UIToolbar *toolbar = [[UIToolbar alloc] initWithFrame:CGRectZero];
    toolbar.barStyle = UIBarStyleDefault;
    [toolbar sizeToFit];
    UIBarButtonItem *flexSpace = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:self action:nil];
    UIBarButtonItem *doneBtn = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone target:self action:@selector(resignPicker)];
    [toolbar setItems:@[flexSpace, doneBtn] animated:YES];
    
    txtFieldSubDealer.inputAccessoryView = toolbar;
    txtFieldDate.inputAccessoryView = toolbar;
    txtFieldChallanNo.inputAccessoryView = toolbar;
    txtFieldQuantity.inputAccessoryView = toolbar;
    txtFieldProduct.inputAccessoryView = toolbar;
    
    UIDatePicker *datePicker = [[UIDatePicker alloc]init];
    if (@available(iOS 13.4, *)) {
        datePicker.preferredDatePickerStyle = UIDatePickerStyleWheels;
    }
    [datePicker setDate:[NSDate date]];
    datePicker.datePickerMode = UIDatePickerModeDate;
    //datePicker.maximumDate = [NSDate date];
    [datePicker addTarget:self action:@selector(updateTextField:) forControlEvents:UIControlEventValueChanged];
    
    [txtFieldDate setInputView:datePicker];
    
    
}

-(void)loadData {
    arrSubDealer = [[NSMutableArray alloc]init];
    arrProduct = [[NSMutableArray alloc] init];
    
    [self getSubDealerList];
    [self loadProductMaster];
    
}

#pragma mark - IBAction's

-(void)btnBackClicked:(UIButton*)btn{
    
    DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
    [self.navigationController pushViewController:dvc animated:NO];
    
}

- (IBAction)btnSubmitClicked:(UIButton *)sender {
    
    if (![txtFieldSubDealer.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]].length){
        UIAlertView *alert  = UDShowAlertWithTitle(nil, @"Please select sub delaer");
        alert.delegate      = self;
        alert.alertRespond  = ^(NSInteger index)
        {
            [txtFieldSubDealer becomeFirstResponder];
        };
        return;
    }else if (![txtFieldDate.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]].length){
        UIAlertView *alert  = UDShowAlertWithTitle(nil, @"Please select date");
        alert.delegate      = self;
        alert.alertRespond  = ^(NSInteger index)
        {
            [txtFieldDate becomeFirstResponder];
        };
        return;
    }else if (![txtFieldChallanNo.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]].length){
        UIAlertView *alert  = UDShowAlertWithTitle(nil, @"Please enter challan number");
        alert.delegate      = self;
        alert.alertRespond  = ^(NSInteger index)
        {
            [txtFieldChallanNo becomeFirstResponder];
        };
        return;
    }else if (![txtFieldQuantity.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]].length){
        UIAlertView *alert  = UDShowAlertWithTitle(nil, @"Please enter quantity");
        alert.delegate      = self;
        alert.alertRespond  = ^(NSInteger index)
        {
            [txtFieldQuantity becomeFirstResponder];
        };
        return;
    }else if (![txtFieldProduct.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]].length){
        UIAlertView *alert  = UDShowAlertWithTitle(nil, @"Please select product");
        alert.delegate      = self;
        alert.alertRespond  = ^(NSInteger index)
        {
            [txtFieldProduct becomeFirstResponder];
        };
        return;
    }
    
    
    if (APP_DELEGATE.isServerReachable) {
        
        //NSString *strCustomerCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
        
        NSString *strCustomerCode;
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strCustomerCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
//        }else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"sub dealer"]){
//            strCustomerCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
//        }else{
//            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
//            FMDatabase *db = [FMDatabase databaseWithPath:path];
//            if ([db open]) {
//                FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
//                while ([s next]) {
//                    strCustomerCode = [s stringForColumn:@"customer_code"];
//                }
//                [db close];
//            }
//        }
        
        if (checkUserType(kbroker) == true) {
            strCustomerCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
        }else if (checkUserType(kSubDealer) == true){
            strCustomerCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
        }else{
            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
            FMDatabase *db = [FMDatabase databaseWithPath:path];
            if ([db open]) {
                FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
                while ([s next]) {
                    strCustomerCode = [s stringForColumn:@"customer_code"];
                }
                [db close];
            }
        }
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:strCustomerCode                           forKey:@"logged_in_customer_code"];
        [dict setValue:txtFieldSubDealer.accessibilityLabel forKey:@"selected_sub_dealer_code"];
        [dict setValue:txtFieldDate.text                    forKey:@"selected_date"];
        [dict setValue:txtFieldChallanNo.text               forKey:@"challan_no"];
        [dict setValue:txtFieldQuantity.text                forKey:@"qty_in_bags"];
        [dict setValue:txtFieldProduct.accessibilityLabel   forKey:@"selected_prod_code"];
        
        [SVProgressHUD show];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_submit_yellow_card_details.php" parameters:dict completion:^(NSDictionary *dictResponse){
            [SVProgressHUD dismiss];
            if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                dispatch_after(dispatch_time(DISPATCH_TIME_NOW, (int64_t)(2.1 * NSEC_PER_SEC)), dispatch_get_main_queue(), ^{
                    DashboardViewController *dvc = [self.storyboard instantiateViewControllerWithIdentifier:@"dashboardViewController"];
                    [self.navigationController pushViewController:dvc animated:NO];
                });
            }else
                UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
        }];
        
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
}

#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    if (activeTextField == txtFieldSubDealer) {
       return arrSubDealer.count;
    }else{
        return arrProduct.count;
    }
}

- (SubDealerCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *cellIdentifier =  @"cell";
    SubDealerCell *cell = [tblViewSubDealers dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    
    if (activeTextField == txtFieldSubDealer) {
        CustomerMasterBO *CMBO = arrSubDealer[indexPath.row];
        cell.lblSubDealer.text = CMBO.strCustomerName;
    }else{
        ProductMasterBO *PMB = arrProduct[indexPath.row];
        cell.lblSubDealer.text = PMB.strProdDesc;
    }
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    if (activeTextField == txtFieldSubDealer) {
        CustomerMasterBO *CMBO = arrSubDealer[indexPath.row];
        AppLog(@"--%@",CMBO.strCustomerName);
        AppLog(@"--%@",CMBO.strAddress);
        txtFieldSubDealer.text = CMBO.strCustomerName;
        txtFieldSubDealer.accessibilityLabel = CMBO.strCustomerCode;
    }else{
        ProductMasterBO *PMB = arrProduct[indexPath.row];
        AppLog(@"--%@",PMB.strProdDesc);
        txtFieldProduct.text = PMB.strProdDesc;
        txtFieldProduct.accessibilityLabel = PMB.strProdCode;
    }
    [self.view endEditing:true];
    [viewItemList setHidden:true];
}

#pragma mark - UISearchBar Delegate

- (void)searchBar:(UISearchBar *)searchBar textDidChange:(NSString *)searchText{
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        
        NSString *strQuery = [NSString stringWithFormat:@"select address, customer_name FROM customer_master WHERE  cust_type != 'Dealer' AND customer_name LIKE '%%%@%%'",searchBar.text];
        [arrSubDealer removeAllObjects];
        FMResultSet *s = [db executeQuery:strQuery];
        while ([s next]) {
            CustomerMasterBO *CMBO = [[CustomerMasterBO alloc]init];
            CMBO.strCustomerName = [s stringForColumn:@"customer_name"];
            CMBO.strAddress = [s stringForColumn:@"address"];
            [arrSubDealer addObject:CMBO];
        }
        [db close];
    }
    [tblViewSubDealers reloadData];
}

- (void)searchBarSearchButtonClicked:(UISearchBar *)searchBar{
    [searchBar resignFirstResponder];
}

#pragma mark - UITextField Delegate

- (void)textFieldDidBeginEditing:(UITextField *)textField {
    if (textField == txtFieldSubDealer || textField == txtFieldProduct) {
        viewItemList.frame = CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, APP_CONSTANTS.fltAppHeight);
        [self.view addSubview:viewItemList];
        [viewItemList setHidden:false];
        [self.view endEditing:true];
        activeTextField = textField;
        [tblViewSubDealers reloadData];
    }
}

#pragma mark - Helper Method

-(void)resignPicker {
    [self.view endEditing:true];
}

// Formats the date chosen with the date picker.
- (NSString *)formatDate:(NSDate *)date {
    
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateStyle:NSDateFormatterShortStyle];
    [dateFormatter setDateFormat:@"yyyy-MM-dd"];
    NSString *formattedDate = [dateFormatter stringFromDate:date];
    return formattedDate;
}

-(void)updateTextField:(UIDatePicker*)picker {
    txtFieldDate.text = [self formatDate:picker.date];
}

#pragma mark - Database Method

-(void)getSubDealerList{
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        FMResultSet *s = [db executeQuery:@"SELECT address, customer_name, customer_code FROM customer_master WHERE  cust_type != 'Dealer'"];
        while ([s next]) {
            CustomerMasterBO *CMBO = [[CustomerMasterBO alloc]init];
            CMBO.strCustomerName = [s stringForColumn:@"customer_name"];
            CMBO.strCustomerCode = [s stringForColumn:@"customer_code"];
            CMBO.strAddress = [s stringForColumn:@"address"];
            [arrSubDealer addObject:CMBO];
        }
        [db close];
    }
    [tblViewSubDealers reloadData];
}

-(void)loadProductMaster{
    
    
    if ([APP_CONSTANTS.db open]) {
        NSString *isBranchWiseProduct = [APP_CONSTANTS.db stringForQuery:@"select branch_wise_product from product_details"];
        NSString *strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
        NSString *strBranchCode = [APP_CONSTANTS.db stringForQuery:@"SELECT branch_code FROM customer_master where customer_code = ?",strEmpCode];
        
        FMResultSet *s = ([isBranchWiseProduct isEqualToString:@"yes"]) ? [APP_CONSTANTS.db executeQuery:@"SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.branch_code = ?",strBranchCode] : [APP_CONSTANTS.db executeQuery:@"SELECT * FROM product_master"];
        while ([s next]) {
            //retrieve values for each record
            ProductMasterBO *PMB = [[ProductMasterBO alloc] init];

              PMB.strProdCode             = [s stringForColumn:@"prod_code"];
              PMB.strProdDesc             = [s stringForColumn:@"prod_desc"];
            
            [arrProduct addObject:PMB];
        }
        [APP_CONSTANTS.db close];
        [tblViewSubDealers reloadData];
    }
}

@end
