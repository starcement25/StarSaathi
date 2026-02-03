//
//  OrderViewController.m
//  StarCementDealer
//
//  Created by Coral  on 05/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "OrderViewController.h"
#import "CustomerMasterBO.h"
#import "SubDealersViewController.h"
#import "OrderConfirmationViewController.h"
#import "SelectDestinationViewController.h"

@interface OrderViewController ()<SubDealerControllerDelegate, SelectDestinationControllerDelegate, UITextFieldDelegate, UITextViewDelegate>{
    
    __weak IBOutlet UIButton *btnRadioSelf;
    __weak IBOutlet UIButton *btnRadioSubDealer;
    __weak IBOutlet UITextField *txtFieldConsigneeName;
    __weak IBOutlet UITextView *txtViewConsigneeAddress;
    __weak IBOutlet UITextField *tfFrieghtDestinationAdd;
    IBOutlet UIToolbar *toolbar;
    __weak IBOutlet UIScrollView *scrollViewOD;
    IBOutletCollection(UIButton) NSArray *btnFrieghtGroup;
    __weak IBOutlet UILabel *lblDumpNameStatic;
    __weak IBOutlet UITextField *txtFieldDumpName;
    __weak IBOutlet UITextField *txtFieldPhoneNumber;    
    __weak IBOutlet UITextView *txtViewDeliveryRemarks;
    __weak IBOutlet UILabel *lblDeliveryPoint;
    
    __weak IBOutlet NSLayoutConstraint *constraintLblDumpNameTop;
    __weak IBOutlet NSLayoutConstraint *constraintDumpNameHeight;
    __weak IBOutlet NSLayoutConstraint *constraintLblDumpNameBottom;
    __weak IBOutlet NSLayoutConstraint *constraintTfDumpNameHeight;
    NSArray *arrConstraintDumpNameViews;
    
    NSString *strIdentifier;
    NSArray *arrShipToGroup;
    CustomerMasterBO *CMBO;
    UIView *activeField;
    NSString *strFrightType;
    NSString *strDumpStatus;
    NSString *strDealerTruckStatus;
    NSString *strSubDealerCode;
    NSString *strOrderForType;
    NSArray *arrTruckData;
}

@end

@implementation OrderViewController

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
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillHideNotification object:nil];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView{
    
    arrShipToGroup = @[btnRadioSelf,btnRadioSubDealer];
    
    txtViewConsigneeAddress.inputAccessoryView = toolbar;
    txtFieldConsigneeName.leftView = [[UIView alloc]initWithFrame:CGRectMake(0, 0, 8, txtFieldConsigneeName.frame.size.height)];
    txtFieldConsigneeName.leftViewMode = UITextFieldViewModeAlways;
    
    txtFieldDumpName.leftView = [[UIView alloc]initWithFrame:CGRectMake(0, 0, 8, txtFieldConsigneeName.frame.size.height)];
    txtFieldDumpName.leftViewMode = UITextFieldViewModeAlways;
    
    tfFrieghtDestinationAdd.leftView = [[UIView alloc]initWithFrame:CGRectMake(0, 0, 8, txtFieldConsigneeName.frame.size.height)];
    tfFrieghtDestinationAdd.leftViewMode = UITextFieldViewModeAlways;
    
    txtFieldPhoneNumber.leftView = [[UIView alloc]initWithFrame:CGRectMake(0, 0, 8, txtFieldConsigneeName.frame.size.height)];
    txtFieldPhoneNumber.leftViewMode = UITextFieldViewModeAlways;
    
    txtFieldPhoneNumber.inputAccessoryView = toolbar;
    
    lblDumpNameStatic.hidden = true;
    txtFieldDumpName.hidden = true;
    
    arrConstraintDumpNameViews = @[constraintDumpNameHeight, constraintLblDumpNameTop, constraintTfDumpNameHeight, constraintLblDumpNameBottom];
    
    for (NSLayoutConstraint *constraint in arrConstraintDumpNameViews) {
        constraint.constant = 0;
    }
    
}

-(void)loadData{
    strFrightType = @"";
    strSubDealerCode = @"";
    strOrderForType = @""; // order_for_type = Self or Sub Dealer
    CMBO = [[CustomerMasterBO alloc]init];
    arrTruckData = @[];
    [self wsFpxDealerTruckList];
    //[self btnShipToGroupClicked:btnRadioSelf];
}

#pragma mark - Web Service Method

-(void)wsFpxDealerTruckList {
    if (APP_DELEGATE.isServerReachable) {
        [SVProgressHUD showWithStatus:@"Loading..."];
        
        NSMutableDictionary *dict  = [[NSMutableDictionary alloc]init];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"] forKey:@"customer_code"];
        
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/fpx-delaer-truck-list.php" parameters:dict completion:^(NSDictionary *dictResponse){
            [SVProgressHUD dismiss];
            if ([[dictResponse safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
                arrTruckData = [dictResponse safeValueeForKey:@"truck_data"];
            }else{
                arrTruckData = [dictResponse safeValueeForKey:@"truck_data"];
            }
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - SubDealer Controller Delegate

- (void)dataFromControllerName:(NSString *)strName Address:(NSString*)strAddress Phone:(NSString*)strPhone SubDealerId:(NSString *)strCustomerCode{
    txtFieldConsigneeName.text = strName;
    txtViewConsigneeAddress.text = strAddress;
    txtFieldPhoneNumber.text = strPhone;
    strSubDealerCode = strCustomerCode;
    AppLog(@"-->>%@",strCustomerCode);
    
    //[self setDestinationAddress];
}

-(void)noDealerFound {
    strOrderForType = @"Self";
    strSubDealerCode = @"";
    tfFrieghtDestinationAdd.text = @"";
    [self getCustomerData];
}

#pragma mark - Select Destination Controller Delegate

- (void)dataFromControllerCode:(NSString *)strCode DestinationName:(NSString*)strDestinationName FieldIdentifier:(NSString*)strIdentider{
    AppLog(@"Code : %@ \n Name : %@ \n Identifier : %@",strCode,strDestinationName,strIdentifier);
    
    if ([strIdentifier isEqualToString:@"frieght"]){
        tfFrieghtDestinationAdd.text = strDestinationName;
        tfFrieghtDestinationAdd.accessibilityHint = strCode;
    }else if ([strIdentifier isEqualToString:@"shipTo"]){
        tfFrieghtDestinationAdd.text = strDestinationName;
        tfFrieghtDestinationAdd.accessibilityHint = strCode;
    }else{
        txtFieldDumpName.text = strDestinationName;
        txtFieldDumpName.accessibilityHint = strCode;
    }
}

#pragma mark - UITextField Delegate



- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    return [textField resignFirstResponder];
}

- (void)textFieldDidBeginEditing:(UITextField *)textField {
    activeField = textField;
    if (textField == txtFieldDumpName) {
        [self.view endEditing:true];
        strIdentifier = @"";
        [self performSegueWithIdentifier:@"orderToSelectDestination" sender:self];
    }else if (textField == tfFrieghtDestinationAdd) {
        [self.view endEditing:true];
        strIdentifier = @"shipTo";
        [self performSegueWithIdentifier:@"orderToSelectDestination" sender:self];
    }
}

- (void)textFieldDidEndEditing:(UITextField *)textField {
    activeField = nil;
}

#pragma mark - IBAction's

- (IBAction)btnShipToGroupClicked:(UIButton *)sender {
    for (UIButton *btn in arrShipToGroup) {
        btn.selected = (btn == sender) ? YES : NO;
    }
    
    txtFieldConsigneeName.text = @"";
    txtViewConsigneeAddress.text = @"";
    
    if ([sender.titleLabel.text isEqualToString:@"Self"]) {
        strOrderForType = @"Self";
        strSubDealerCode = @"";
        tfFrieghtDestinationAdd.text = @"";
        [self performSegueWithIdentifier:@"orderToSubDealers" sender:@"dealer"];
    }else{
        //Sub Dealer
        strOrderForType = @"Sub Dealer";
        tfFrieghtDestinationAdd.text = @"";
        [self performSegueWithIdentifier:@"orderToSubDealers" sender:@"sub dealer"];
    }
    
    
//    if ([sender.titleLabel.text isEqualToString:@"Sub Dealer"]) {
//        strOrderForType = @"Sub Dealer";
//        tfFrieghtDestinationAdd.text = @"";
//        [self performSegueWithIdentifier:@"orderToSubDealers" sender:self];
//    }else if ([sender.titleLabel.text isEqualToString:@"Self"]){
//        strOrderForType = @"Self";
//        strSubDealerCode = @"";
//        tfFrieghtDestinationAdd.text = @"";
//        [self getCustomerData];
//    }else{
//        strOrderForType = @"Others";
//        strSubDealerCode = @"";
//        txtFieldPhoneNumber.text = @"";
//        for (UIButton *btnFright in btnFrieghtGroup) {
//            [btnFright setUserInteractionEnabled:true];
//        }
//    }
}

- (IBAction)btnFrieghtGroupClicked:(UIButton *)sender {
    
    for (UIButton *btn in btnFrieghtGroup) {
        btn.selected = (btn == sender) ? YES : NO;
    }
    
    for (UIButton *btn in btnFrieghtGroup) {
        btn.selected = false;
    }
    sender.selected = true;
    
    if ([sender.titleLabel.text isEqualToString:@"EXW"]) {
        
        lblDeliveryPoint.text= @"";
        
        lblDumpNameStatic.hidden = false;
        txtFieldDumpName.hidden = false;
        
        constraintDumpNameHeight.constant = 21;
        constraintLblDumpNameTop.constant = 8;
        constraintTfDumpNameHeight.constant = 25;
        constraintLblDumpNameBottom.constant = 8;
        
    }else{
        lblDumpNameStatic.hidden = true;
        txtFieldDumpName.hidden = true;
        txtFieldDumpName.text = @"";
        
        for (NSLayoutConstraint *constraint in arrConstraintDumpNameViews) {
            constraint.constant = 0;
        }
        
        UIAlertController *alert = [UIAlertController alertControllerWithTitle:@"Point of Delivery" message:nil preferredStyle:UIAlertControllerStyleAlert];
        [alert addAction:[UIAlertAction actionWithTitle:@"Single" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
            lblDeliveryPoint.text = action.title;
        }]];
        [alert addAction:[UIAlertAction actionWithTitle:@"Multiple" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
            lblDeliveryPoint.text = action.title;
        }]];
        
        if (arrTruckData.count) {
            [alert addAction:[UIAlertAction actionWithTitle:@"Dot" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
                lblDeliveryPoint.text = action.title;
            }]];
        }
        [self presentViewController:alert animated:true completion:nil];
        
    }

    strDumpStatus = ([sender.titleLabel.text isEqualToString:@"EXW"]) ? @"YES" : @"NO";
    strFrightType = sender.titleLabel.text;
    
}


- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)btnContinueClicked:(UIButton *)sender {
    
    if (![strOrderForType stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]].length) {
        UDShowToastAlertWithTitle(@"Please choose Ship To", 2);
        return;
    }
    
    if (![txtFieldConsigneeName.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]].length) {
        UDShowToastAlertWithTitle(@"Please enter consignee name", 2);
        return;
    }else if (![txtViewConsigneeAddress.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]].length){
        UDShowToastAlertWithTitle(@"Please enter consignee address", 2);
        return;
    }else if (![tfFrieghtDestinationAdd.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]].length) {
        UDShowToastAlertWithTitle(@"Please select destination address", 2);
        return;
    }else if (![strFrightType stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]].length) {
        UDShowToastAlertWithTitle(@"Please select frieght type", 2);
        return;
    }else if (!validateNumber(txtFieldPhoneNumber.text)){
        UDShowToastAlertWithTitle(@"Please enter valid phone number", 2);
        return;
    }else if ([strFrightType isEqualToString:@"EXW"]){
        if (![txtFieldDumpName.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]].length){
            UDShowToastAlertWithTitle(@"Please select dump detination", 2);
            return;
        }
    }
    [self performSegueWithIdentifier:@"orderDetailsToConfirmation" sender:self];
    
}
- (IBAction)resignKeyboard:(UIBarButtonItem *)sender {
    [self.view endEditing:YES];
}

#pragma mark - Gesture

-(void)destinationAddressFieldTapped:(UITapGestureRecognizer*)tapGesture{
    AppLog(@"field tapped.");
    strIdentifier = @"shipTo";
    [self performSegueWithIdentifier:@"orderToSelectDestination" sender:self];
}

#pragma mark - Segue Method

-(void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender{
    if ([segue.identifier isEqualToString:@"orderToSubDealers"]) {
        SubDealersViewController *sdvc = segue.destinationViewController;
        sdvc.delegate = self;
        sdvc.strType = (NSString*)sender;
        sdvc.data = @"order";
    }else if ([segue.identifier isEqualToString:@"orderDetailsToConfirmation"]){
        OrderConfirmationViewController *ocvc = segue.destinationViewController;
        ocvc.arrProductList = self.arrProdList;
        ocvc.strName = txtFieldConsigneeName.text;
        ocvc.strAddress = txtViewConsigneeAddress.text;
        ocvc.strDestinationName = tfFrieghtDestinationAdd.text;
        ocvc.strDestinationCode = tfFrieghtDestinationAdd.accessibilityHint;
        ocvc.strFrieghtAddress = tfFrieghtDestinationAdd.text;
        ocvc.strFrieght = strFrightType;
        ocvc.strPhoneNumber = txtFieldPhoneNumber.text;
        ocvc.strDumpAddress = txtFieldDumpName.text;
        ocvc.strDumpStatus = strDumpStatus;
        ocvc.strDealerTruckStatus = @"";
        ocvc.strSubDealerId = strSubDealerCode;
        ocvc.strDumpCode = txtFieldDumpName.accessibilityHint;
        ocvc.strOrderForType = strOrderForType;
        ocvc.strDeliveryPoint = lblDeliveryPoint.text;
        ocvc.strDeliveryRemarks = txtViewDeliveryRemarks.text;
    }else if ([segue.identifier isEqualToString:@"orderToSelectDestination"]){
        SelectDestinationViewController *sdvc = segue.destinationViewController;
        sdvc.delegate = self;
        sdvc.strIdentifier = strIdentifier;
        sdvc.strFrieghtType = strFrightType;
    }
}

#pragma mark - Database Method

-(void)getCustomerData{
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    
    if ([db open]) {
        NSString *strQuery = [NSString stringWithFormat:@"SELECT customer_name, address, phone_no FROM customer_master where customer_code = '%@'",APP_CONSTANTS.strCustCode];
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

-(void)setDestinationAddress {
    
    txtFieldDumpName.text = @"";
    txtFieldDumpName.accessibilityHint = @"";
    if ([APP_CONSTANTS.db open]) {
        FMResultSet *s = [APP_CONSTANTS.db executeQuery:@"SELECT * from destination_master"];
        while ([s next]) {
            NSString *strDestCode = [s stringForColumn:@"destination_code"];
            NSString *strDestName = [s stringForColumn:@"destination_name"];
            tfFrieghtDestinationAdd.text = strDestName;
            tfFrieghtDestinationAdd.accessibilityHint = strDestCode;
            break;
        }
        [APP_CONSTANTS.db close];
    }
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
