//
//  NewOrderEnquiryVC.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 06/12/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import "NewOrderEnquiryVC.h"
#import "ProductMasterBO.h"
#import "AlertHelper.h"

@interface NewOrderEnquiryVC ()<UIPickerViewDelegate, UIPickerViewDataSource, UITextFieldDelegate, UITextViewDelegate>{
    __weak IBOutlet UIScrollView *fpScrollView;
    __weak IBOutlet UITextField *txtFieldLinkedDealer;
    __weak IBOutlet UITextField *txtFieldProdName;
    __weak IBOutlet UITextField *txtFieldQtyInBags;
    __weak IBOutlet UITextField *txtFieldDateOfLifting;
    __weak IBOutlet UITextView *txtViewRemarks;
    UIView *activeField;
    UIDatePicker *datePicker;
    NSMutableArray *arrProduct;
    UIPickerView *pickerViewProd;
}

@end

@implementation NewOrderEnquiryVC

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
    
}

-(void)viewWillDisappear:(BOOL)animated {
    [super viewWillDisappear:animated];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillHideNotification object:nil];
}

#pragma mark - Initialization Method

-(void)designView {
    [self setUpToolbar];
    [self setUpPickerView];
    [self setUpDatePicker];
    [self setUpTxtFieldAccessory];
    
    // Mimic UITextField's border style
    //txtViewRemarks.layer.borderColor = [[UIColor lightGrayColor] CGColor]; // Same as UITextField default border color
    txtViewRemarks.layer.borderColor = [[UIColor colorWithRed:0.85 green:0.85 blue:0.85 alpha:1.0] CGColor]; // HEX #D9D9D9
    txtViewRemarks.layer.borderWidth = 0.5; // Border width
    txtViewRemarks.layer.cornerRadius = 5.0; // Rounded corners
    
    // Optional: Add shadow to mimic UITextField shadow (if required)
    txtViewRemarks.layer.shadowColor = [[UIColor blackColor] CGColor];
    txtViewRemarks.layer.shadowOffset = CGSizeMake(0, 1);
    txtViewRemarks.layer.shadowOpacity = 0.2;
    txtViewRemarks.layer.shadowRadius = 1.0;
    
    // Add padding inside the UITextView (UITextField has default padding)
    txtViewRemarks.textContainerInset = UIEdgeInsetsMake(8, 8, 8, 8);
    
}

-(void)loadData {
    arrProduct = [[NSMutableArray alloc] init];
    [self loadProductMaster];
    txtFieldLinkedDealer.text = [[NSUserDefaults standardUserDefaults] valueForKey:@"kBelongDealerName"];
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(id)sender {
    [self.navigationController popViewControllerAnimated:true];
}

- (IBAction)btnSubmitClicked:(id)sender {
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSString *strOrderId = [NSString stringWithFormat:@"RS%@%@",[[NSUserDefaults standardUserDefaults] valueForKey:@"kUserLoginId"],getTimestamp()];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"kUserLoginId"] forKey:@"customer_id"];
        [dict setValue:strOrderId forKey:@"order_query_data[0][order_id]"];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"kBelongDealerCode"] forKey:@"order_query_data[0][linked_dealer_code]"];        
        [dict setValue:txtFieldProdName.accessibilityHint forKey:@"order_query_data[0][dns_prod_code]"];
        [dict setValue:txtFieldProdName.text forKey:@"order_query_data[0][prod_name]"];
        [dict setValue:txtFieldQtyInBags.text forKey:@"order_query_data[0][qty_bags]"];
        [dict setValue:convertDateFormat3(txtFieldDateOfLifting.text) forKey:@"order_query_data[0][query_date]"];
        [dict setValue:APP_DELEGATE.strDateForNewOrderEnq forKey:@"order_query_data[0][date_of_lifting]"];
        [dict setValue:txtViewRemarks.text forKey:@"order_query_data[0][remarks]"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/save_order_query_data.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    [AlertHelper showAlertWithTitle:kAppName message:[dictResponse valueForKey:@"process_message"] fromViewController:self okHandler:^{
                        [self btnBackClicked:nil];
                    }];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
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

#pragma mark - UITextField Delegate

- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    return [textField resignFirstResponder];
}

- (void)textFieldDidBeginEditing:(UITextField *)textField {
    activeField = textField;
}

- (void)textFieldDidEndEditing:(UITextField *)textField {
    activeField = nil;
}

#pragma mark - UITextView Delegate

- (void)textViewDidBeginEditing:(UITextView *)textView {
    activeField = textView;
}

- (void)textViewDidEndEditing:(UITextView *)textView {
    activeField = nil;
}

#pragma mark - Helper Method

-(void)setUpToolbar {
    UIToolbar *toolbar = [[UIToolbar alloc] initWithFrame:CGRectZero];
    toolbar.barStyle = UIBarStyleDefault;
    [toolbar sizeToFit];
    
    UIBarButtonItem *flexSpace = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:self action:nil];
    
    UIBarButtonItem *doneBtn = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone target:self action:@selector(resignPicker)];
    
    [toolbar setItems:@[flexSpace, doneBtn] animated:YES];
    
    txtFieldProdName.inputAccessoryView = toolbar;
    txtFieldQtyInBags.inputAccessoryView = toolbar;
    txtFieldDateOfLifting.inputAccessoryView = toolbar;
    txtViewRemarks.inputAccessoryView = toolbar;
}

-(void)setUpPickerView {
    pickerViewProd = [[UIPickerView alloc] initWithFrame:CGRectZero];
    pickerViewProd.delegate = self;
    pickerViewProd.dataSource = self;
    [txtFieldProdName setInputView:pickerViewProd];
}

-(void)setUpDatePicker {
    datePicker = [[UIDatePicker alloc]init];
    if (@available(iOS 13.4, *)) {
        datePicker.preferredDatePickerStyle = UIDatePickerStyleWheels;
    }
    [datePicker setDate:[NSDate date]];
    datePicker.datePickerMode = UIDatePickerModeDate;
    datePicker.minimumDate = [NSDate date];
    [datePicker addTarget:self action:@selector(updateTextField:) forControlEvents:UIControlEventValueChanged];
    [txtFieldDateOfLifting setInputView:datePicker];
}

-(void)setUpTxtFieldAccessory{
    // Create an image view with the desired image
    UIImage *image = [UIImage imageNamed:@"down_arrow"]; // Replace with your image name
    UIImageView *imageView = [[UIImageView alloc] initWithImage:image];
    
    // Set the image view's content mode for proper scaling
    imageView.contentMode = UIViewContentModeScaleAspectFit;
    
    // Set the size for the image view
    CGFloat imageWidth = 15;
    CGFloat imageHeight = 9;
    imageView.frame = CGRectMake(0, 0, imageWidth, imageHeight);
    
    // Create a container view for the image with padding
    CGFloat padding = 10; // Adjust padding as needed
    UIView *containerView = [[UIView alloc] initWithFrame:CGRectMake(0, 0, imageWidth + padding, imageHeight)];
    [containerView addSubview:imageView];
    
    // Position the image view inside the container view
    imageView.center = containerView.center;
    
    // Set the container view as the right view of the text field
    txtFieldProdName.rightView = containerView;
    txtFieldProdName.rightViewMode = UITextFieldViewModeAlways; // Always show the right view
}

-(void)resignPicker{
    [self.view endEditing:YES];
}

-(void)updateTextField:(UIDatePicker*)picker {
    txtFieldDateOfLifting.text = [self formatDate:picker.date];
}

// Formats the date chosen with the date picker.
- (NSString *)formatDate:(NSDate *)date {
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateStyle:NSDateFormatterShortStyle];
    [dateFormatter setDateFormat:@"dd-MM-yyyy"];
    NSString *formattedDate = [dateFormatter stringFromDate:date];
    return formattedDate;
}

#pragma mark - Keyboard Method

-(void)keyboardWillShow:(NSNotification*)notification {
    // Get keyboard size and animation duration
    NSDictionary *userInfo = notification.userInfo;
    CGRect keyboardFrame = [userInfo[UIKeyboardFrameEndUserInfoKey] CGRectValue];
    CGFloat keyboardHeight = keyboardFrame.size.height;
    
    // Adjust UIScrollView content inset
    UIEdgeInsets contentInset = fpScrollView.contentInset;
    contentInset.bottom = keyboardHeight;
    fpScrollView.contentInset = contentInset;
    fpScrollView.scrollIndicatorInsets = contentInset;
    
    // Ensure the field is visible
    CGRect textViewFrame = [fpScrollView convertRect:activeField.frame toView:self.view];
    [fpScrollView scrollRectToVisible:textViewFrame animated:YES];
}

-(void)keyboardWillHide:(NSNotification*)notification {
    UIEdgeInsets contentInsets = UIEdgeInsetsZero;
    fpScrollView.contentInset = contentInsets;
    fpScrollView.scrollIndicatorInsets = contentInsets;
}



@end
