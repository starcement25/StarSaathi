//
//  OrderSubDealerViewController.m
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 10/05/21.
//  Copyright © 2021 Coral . All rights reserved.
//

#import "OrderSubDealerViewController.h"
#import "CustomerMasterBO.h"
#import "OrderConfirmationViewController.h"

@interface OrderSubDealerViewController (){
    __weak IBOutlet UITextField *txtFieldConsigneeName;
    __weak IBOutlet UITextView *txtViewConsigneeAddress;
    __weak IBOutlet UITextField *txtFieldPhoneNumber;
    __weak IBOutlet UITextField *txtFieldLinkedDealerName;
    __weak IBOutlet UITextField *txtFieldLinkedDealerCode;
    __weak IBOutlet UIScrollView *scrollViewOD;
    IBOutlet UIToolbar *toolbar;
    
    UIView *activeField;
    CustomerMasterBO *CMBO;
}
@end

@implementation OrderSubDealerViewController

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
    
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillShow:) name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillHide:) name:UIKeyboardWillHideNotification object:nil];
    
}

-(void)viewWillDisappear:(BOOL)animated {
    [super viewWillDisappear:animated];
}

#pragma mark - Initialization Method

-(void)designView {
    
    txtViewConsigneeAddress.inputAccessoryView = toolbar;
    
    txtFieldConsigneeName.leftView = [[UIView alloc]initWithFrame:CGRectMake(0, 0, 8, txtFieldConsigneeName.frame.size.height)];
    txtFieldConsigneeName.leftViewMode = UITextFieldViewModeAlways;
    
    txtFieldPhoneNumber.leftView = [[UIView alloc]initWithFrame:CGRectMake(0, 0, 8, txtFieldConsigneeName.frame.size.height)];
    txtFieldPhoneNumber.leftViewMode = UITextFieldViewModeAlways;
    
    txtFieldLinkedDealerCode.leftView = [[UIView alloc]initWithFrame:CGRectMake(0, 0, 8, txtFieldLinkedDealerCode.frame.size.height)];
    txtFieldLinkedDealerCode.leftViewMode = UITextFieldViewModeAlways;
    
    txtFieldLinkedDealerName.leftView = [[UIView alloc]initWithFrame:CGRectMake(0, 0, 8, txtFieldLinkedDealerName.frame.size.height)];
    txtFieldLinkedDealerName.leftViewMode = UITextFieldViewModeAlways;
    
    txtFieldPhoneNumber.inputAccessoryView = toolbar; 
}

-(void)loadData {
    CMBO = [[CustomerMasterBO alloc]init];
    txtFieldLinkedDealerCode.text = [[NSUserDefaults standardUserDefaults] valueForKey:@"kBelongDealerCode"];
    txtFieldLinkedDealerName.text = [[NSUserDefaults standardUserDefaults] valueForKey:@"kBelongDealerName"];
    [self getCustomerData];
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)btnContinueClicked:(UIButton *)sender {
    
    if (![txtFieldConsigneeName.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]].length) {
        UDShowToastAlertWithTitle(@"Please enter consignee name", 2);
        return;
    }else if (![txtViewConsigneeAddress.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]].length){
        UDShowToastAlertWithTitle(@"Please enter consignee address", 2);
        return;
    }else if (!validateNumber(txtFieldPhoneNumber.text)){
        UDShowToastAlertWithTitle(@"Please enter valid phone number", 2);
        return;
    }
    
    [self performSegueWithIdentifier:@"orderSubDealerToConfirmation" sender:self];
    
}

- (IBAction)resignKeyboard:(UIBarButtonItem *)sender {
    [self.view endEditing:YES];
}

#pragma mark - Segue Method

-(void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender{
    if ([segue.identifier isEqualToString:@"orderSubDealerToConfirmation"]){
        OrderConfirmationViewController *ocvc = segue.destinationViewController;
        ocvc.arrProductList = self.arrProdList;
        ocvc.strName = txtFieldConsigneeName.text;
        ocvc.strAddress = txtViewConsigneeAddress.text;
        //ocvc.strDestinationName = tfFrieghtDestinationAdd.text;
        //ocvc.strDestinationCode = tfFrieghtDestinationAdd.accessibilityHint;
        //ocvc.strFrieghtAddress = tfFrieghtDestinationAdd.text;
        //ocvc.strFrieght = strFrightType;
        ocvc.strPhoneNumber = txtFieldPhoneNumber.text;
        //ocvc.strDumpAddress = txtFieldDumpName.text;
        //ocvc.strDumpStatus = strDumpStatus;
        //ocvc.strDealerTruckStatus = strDealerTruckStatus;
        //ocvc.strSubDealerId = strSubDealerCode;
        //ocvc.strDumpCode = txtFieldDumpName.accessibilityHint;
        //ocvc.strOrderForType = strOrderForType;
        
    }
}

#pragma mark - Database Method

-(void)getCustomerData{
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    
    NSString *strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
    
    if ([db open]) {
        NSString *strQuery = [NSString stringWithFormat:@"SELECT customer_name, address, phone_no FROM customer_master where customer_code = '%@'",strCustCode];
        FMResultSet *s = [db executeQuery:strQuery];
        while ([s next]) {
            CMBO.strCustomerName    = [s stringForColumn:@"customer_name"];
            CMBO.strAddress         = [s stringForColumn:@"address"];
            CMBO.strPhoneNo         = [s stringForColumn:@"phone_no"];
        }
        [db close];
    }
    
    txtFieldConsigneeName.text   = CMBO.strCustomerName;
    txtViewConsigneeAddress.text = CMBO.strAddress;
    txtFieldPhoneNumber.text     = CMBO.strPhoneNo;
    
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

#pragma mark - Keyboard Method

-(void)keyboardWillShow:(NSNotification*)notification {
    NSDictionary* info = [notification userInfo];
    CGSize kbSize = [[info objectForKey:UIKeyboardFrameEndUserInfoKey] CGRectValue].size;
    
    UIEdgeInsets contentInsets = UIEdgeInsetsMake(0.0, 0.0, kbSize.height, 0.0);
    scrollViewOD.contentInset = contentInsets;
    scrollViewOD.scrollIndicatorInsets = contentInsets;
    
    // If active text field is hidden by keyboard, scroll it so it's visible
    // Your app might not need or want this behavior.
    CGRect aRect = self.view.frame;
    aRect.size.height -= kbSize.height;
    if (!CGRectContainsPoint(aRect, activeField.frame.origin) ) {
        [scrollViewOD scrollRectToVisible:activeField.frame animated:YES];
    }
}

-(void)keyboardWillHide:(NSNotification*)notification {
    UIEdgeInsets contentInsets = UIEdgeInsetsZero;
    scrollViewOD.contentInset = contentInsets;
    scrollViewOD.scrollIndicatorInsets = contentInsets;
}

@end
