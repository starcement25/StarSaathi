//
//  SelectProductViewController.m
//  StarCementDealer
//
//  Created by Coral  on 09/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "SelectProductViewController.h"
#import "ProductCell.h"
#import "ProductMasterBO.h"
#import "OrderViewController.h"
#import "OrderSubDealerViewController.h"

@interface SelectProductViewController ()<UITableViewDataSource, UITableViewDelegate, UITextFieldDelegate>{
    
    __weak IBOutlet UILabel *lblRupees;
    __weak IBOutlet UILabel *lblOutStanding;
    __weak IBOutlet UITableView *tblViewProduct;
    IBOutlet UIToolbar *toolbarDone;
    NSMutableArray *arrProduct;
    NSMutableArray *arrFinalProduct;
    NSDictionary *dictLedger;
}

@end

@implementation SelectProductViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillShow:) name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillHide:) name:UIKeyboardWillHideNotification object:nil];
    
    [self.navigationController setNavigationBarHidden:NO animated:animated];
    self.navigationItem.hidesBackButton = YES;
}

-(void)viewWillDisappear:(BOOL)animated{
    [super viewWillDisappear:animated];
    
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillHideNotification object:nil];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView{
    [tblViewProduct registerNib:[UINib nibWithNibName:@"ProductCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewProduct.separatorColor = [UIColor clearColor];
}

-(void)loadData{
    arrProduct = [[NSMutableArray alloc] init];
    arrFinalProduct = [[NSMutableArray alloc]init];
    [self getLedgerDetails];
    [self getProductMaster];
    //[self loadProductMaster];
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

-(IBAction)btnContinueClicked:(UIButton*)button{
    [self passDataToNextViewController];
}

- (IBAction)btnForwardClicked:(id)sender {
    [self passDataToNextViewController];
}

- (IBAction)resignKeyboard:(UIBarButtonItem *)sender {
    [self.view endEditing:YES];
}

-(void)passDataToNextViewController{
    [arrFinalProduct removeAllObjects];
    for (ProductMasterBO *PMBO in arrProduct) {
        PMBO.strQuantity = [PMBO.strQuantity stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
        if (PMBO.strQuantity.length) {
            [arrFinalProduct addObject:PMBO];
        }
    }
    
    if (!arrFinalProduct.count) {
        UDShowToastAlertWithTitle(@"Please add at least one product", 2);
        return;
    }
    
    if (checkUserType(kSubDealer) == true) {
        [self performSegueWithIdentifier:@"selectProductToOrderDetailsForSubDealer" sender:self];
    }else{
        [self performSegueWithIdentifier:@"selectProductToOrderDetails" sender:self];
    }
    
//    if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"sub dealer"]) {
//        [self performSegueWithIdentifier:@"selectProductToOrderDetailsForSubDealer" sender:self];
//    }else{
//        [self performSegueWithIdentifier:@"selectProductToOrderDetails" sender:self];
//    }
}

#pragma mark - Segue Method

-(void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender{
    if ([segue.identifier isEqualToString:@"selectProductToOrderDetails"]) {
        OrderViewController *ovc = segue.destinationViewController;
        ovc.arrProdList = arrFinalProduct;
    }else if ([segue.identifier isEqualToString:@"selectProductToOrderDetailsForSubDealer"]){
        OrderSubDealerViewController *osdvc = segue.destinationViewController;
        osdvc.arrProdList = arrFinalProduct;
    }
}

#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return arrProduct.count;
}

- (ProductCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *cellIdentifier = @"cell";
    ProductCell *cell = [tblViewProduct dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    ProductMasterBO *PMB = arrProduct[indexPath.row];
    cell.lblProductName.text = PMB.strProdDesc;
    cell.txtFieldUnit.inputAccessoryView = toolbarDone;
    cell.txtFieldUnit.delegate = self;
    cell.txtFieldUnit.tag = indexPath.row;
    return cell;
}



#pragma mark - Database Method

-(void)loadProductMaster{
    
    
    if ([APP_CONSTANTS.db open]) {
        //NSString *isBranchWiseProduct = [APP_CONSTANTS.db stringForQuery:@"select branch_wise_product from product_details"];
        
//        NSString *strEmpCode;
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
//        }else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"sub dealer"]){
//            strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
//        }else{
//            strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
////            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
////            FMDatabase *db = [FMDatabase databaseWithPath:path];
////            if ([db open]) {
////                FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
////                while ([s next]) {
////                    strEmpCode = [s stringForColumn:@"customer_code"];
////                }
////                [db close];
////            }
//        }
        
//        NSString *strBranchCode = [APP_CONSTANTS.db stringForQuery:@"SELECT branch_code FROM customer_master where customer_code = ?",APP_CONSTANTS.strCustCode];
//        
//        //FMResultSet *s = ([isBranchWiseProduct isEqualToString:@"yes"]) ? [APP_CONSTANTS.db executeQuery:@"SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.branch_code = ?",strBranchCode] : [APP_CONSTANTS.db executeQuery:@"SELECT * FROM product_master"];
//        
//        //NSString *strQuery = ([isBranchWiseProduct isEqualToString:@"yes"]) ? [NSString stringWithFormat:@"SELECT DISTINCT PM.*,0 FROM product_master PM WHERE PM.branch_code = '%@'",strBranchCode] : @"SELECT * FROM product_master";
//        
//        NSString *strQuery = @"SELECT * FROM product_master";
        
        FMResultSet *s = [APP_CONSTANTS.db executeQuery:@"SELECT * FROM product_master"];
        
        //FMResultSet *s = ([isBranchWiseProduct isEqualToString:@"yes"]) ? [APP_CONSTANTS.db executeQuery:@"SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.branch_code = ?",strBranchCode] : [APP_CONSTANTS.db executeQuery:@"SELECT * FROM product_master"];
        
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
            //PMB.strDownloadTime         = [s stringForColumn:@"download_time"];
            //PMB.strDownloadTimeClStk    = [s stringForColumn:@"download_time_cl_stk"];
            
            [arrProduct addObject:PMB];
        }
        [APP_CONSTANTS.db close];
        [tblViewProduct reloadData];
    }
}

#pragma mark - Web Service Method

-(void)getLedgerDetails{
    
    if (APP_DELEGATE.isServerReachable) {
        
//        NSString *strCustCode;
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
//        }else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"sub dealer"]){
//            strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
//        }else{
//            strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
////            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
////            FMDatabase *db = [FMDatabase databaseWithPath:path];
////            if ([db open]) {
////                FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
////                while ([s next]) {
////                    strCustCode = [s stringForColumn:@"customer_code"];
////                }
////                [db close];
////            }
//        }
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_ledger_by_id.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    dictLedger = dictResponse;
                    [self populateData:dictResponse];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

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

#pragma mark - Set Data Method

-(void)populateData:(NSDictionary*)dictResponse{
    AppLog(@"-->>%@",dictResponse);
    lblRupees.text = [NSString stringWithFormat:@"₹ %@",[[dictResponse safeValueeForKey:@"ledger_balance_data"] safeValueeForKey:@"balance"]];
    lblOutStanding.text = [NSString stringWithFormat:@"OUTSTANDING AS ON %@",convertDateFormat1([[dictResponse safeValueeForKey:@"ledger_balance_data"] safeValueeForKey:@"date"])];
}

#pragma mark - Keyboard Method

- (void)keyboardWillShow:(NSNotification *)sender {
    
    CGFloat height = [[sender.userInfo objectForKey:UIKeyboardFrameEndUserInfoKey] CGRectValue].size.height;
    height -= 44;
    NSTimeInterval duration = [[sender.userInfo objectForKey:UIKeyboardAnimationDurationUserInfoKey] doubleValue];
    UIViewAnimationOptions curveOption = [[sender.userInfo objectForKey:UIKeyboardAnimationCurveUserInfoKey] unsignedIntegerValue] << 16;
    
    [UIView animateKeyframesWithDuration:duration delay:0 options:UIViewAnimationOptionBeginFromCurrentState|curveOption animations:^{
        UIEdgeInsets edgeInsets = UIEdgeInsetsMake(0, 0, height, 0);
        tblViewProduct.contentInset = edgeInsets;
        tblViewProduct.scrollIndicatorInsets = edgeInsets;
    }completion:nil];
}

- (void)keyboardWillHide:(NSNotification *)sender {
    
    NSTimeInterval duration = [[sender.userInfo objectForKey:UIKeyboardAnimationDurationUserInfoKey] doubleValue];
    UIViewAnimationOptions curveOption = [[sender.userInfo objectForKey:UIKeyboardAnimationCurveUserInfoKey] unsignedIntegerValue] << 16;
    
    [UIView animateKeyframesWithDuration:duration delay:0 options:UIViewAnimationOptionBeginFromCurrentState|curveOption animations:^{
        UIEdgeInsets edgeInsets = UIEdgeInsetsZero;
        tblViewProduct.contentInset = edgeInsets;
        tblViewProduct.scrollIndicatorInsets = edgeInsets;
    }completion:nil];
}

#pragma mark - UITextFieldDelegate

//- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
//    
//    NSString *newString = [textField.text stringByReplacingCharactersInRange:range withString:string];
//    NSArray *arrDotValue = [newString componentsSeparatedByString:@"."];
//    
//    if ([newString floatValue] > 999.99) {
//        return NO;
//    }else if (arrDotValue.count>2){
//        return NO;
//    }else if([arrDotValue count] >= 2) {
//        NSString *sepStr=[NSString stringWithFormat:@"%@",[arrDotValue objectAtIndex:1]];
//        if (!([sepStr length]>2)) {
//            if ([sepStr length]==2 && [string isEqualToString:@"."]) {
//                return NO;
//            }
//            //return YES;
//        }else{
//            return NO;
//        }
//    }
//    
//    ProductMasterBO *PMBO = arrProduct[textField.tag];
//    AppLog(@"-->>%@",newString);
//    PMBO.strQuantity = newString;
//    return (newString.length<=6);
//}

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
    // Construct the new text
    NSString *newText = [textField.text stringByReplacingCharactersInRange:range withString:string];
    
    // Allow empty field (optional)
    if (newText.length == 0) {
        return YES;
    }
    
    // Validate the input format (digits and optional decimal point)
    NSCharacterSet *allowedCharacters = [NSCharacterSet characterSetWithCharactersInString:@"0123456789."];
    NSCharacterSet *inputCharacters = [NSCharacterSet characterSetWithCharactersInString:newText];
    
    if (![allowedCharacters isSupersetOfSet:inputCharacters]) {
        return NO; // Reject invalid characters
    }
    
    // Ensure only one decimal point
    NSArray *components = [newText componentsSeparatedByString:@"."];
    if (components.count > 2) {
        return NO; // Reject multiple decimal points
    }
    
    // Ensure no more than 2 digits after the decimal point
    if (components.count == 2) {
        NSString *decimalPart = components[1];
        if (decimalPart.length > 2) {
            return NO; // Reject if more than 2 digits after the decimal
        }
    }
    
    // Validate the range (1 to 999.99)
    NSNumberFormatter *formatter = [[NSNumberFormatter alloc] init];
    formatter.numberStyle = NSNumberFormatterDecimalStyle;
    NSNumber *number = [formatter numberFromString:newText];
    
    if (number) {
        double value = [number doubleValue];
        if (value < 1 || value > 999.99) {
            return NO; // Reject values outside the range
        }
    } else {
        return NO; // Reject invalid numbers
    }
    
    ProductMasterBO *PMBO = arrProduct[textField.tag];
    AppLog(@"-->>%@",newText);
    PMBO.strQuantity = newText;
    
    return YES;
}

@end
