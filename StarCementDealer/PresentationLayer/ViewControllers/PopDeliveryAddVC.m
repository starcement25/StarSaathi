//
//  PopDeliveryAddVC.m
//  StarCementDealer
//

#import "PopDeliveryAddVC.h"
#import "PopOrderWebVC.h"
#import "PopOrderListViewController.h"
#import <SVProgressHUD/SVProgressHUD.h>

#define ACCEPTABLE_CHARACTERS @"0123456789 abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ - _ ,"

@interface PopDeliveryAddVC ()<UITextFieldDelegate, UITextViewDelegate>{
    __weak IBOutlet UIScrollView *fpScrollView;
    IBOutlet UIToolbar *toolbarDone;
    UIView *activeField;
    __weak IBOutlet UITextView *txtViewAddPincode;
    __weak IBOutlet UITextField *txtFieldContact;
    __weak IBOutlet UITextView *txtViewDelAdd;
    __weak IBOutlet UITextField *txtViewPin;
    __weak IBOutlet UITextView *txtViewRemarks;
    __weak IBOutlet UIButton *btnTermsConditions;
}

@end

@implementation PopDeliveryAddVC

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
    txtViewAddPincode.inputAccessoryView = toolbarDone;
    txtFieldContact.inputAccessoryView = toolbarDone;
    txtViewDelAdd.inputAccessoryView = toolbarDone;
    txtViewPin.inputAccessoryView = toolbarDone;
    txtViewRemarks.inputAccessoryView = toolbarDone;
    
    txtViewAddPincode.text = @"Enter address, pin code";
    txtViewAddPincode.textColor = [UIColor lightGrayColor];
    
    txtViewDelAdd.text = @"Please enter";
    txtViewDelAdd.textColor = [UIColor lightGrayColor];
    
    txtViewRemarks.text = @"Please enter";
    txtViewRemarks.textColor = [UIColor lightGrayColor];
}

-(void)loadData {
    // Load initial data if needed
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(id)sender {
    [self.navigationController popViewControllerAnimated:true];
}

- (IBAction)btnDoneClicked:(id)sender {
    [self.view endEditing:true];
}

- (IBAction)btnSameAsAboveClicked:(UIButton*)sender {
    sender.selected = !sender.selected;
    if (sender.isSelected) {
        txtViewDelAdd.textColor = [UIColor blackColor];
        txtViewDelAdd.text = txtViewAddPincode.text;
    } else {
        txtViewDelAdd.text = @"Please enter";
        txtViewDelAdd.textColor = [UIColor lightGrayColor];
    }
}

- (IBAction)btnTermsConditionClicked:(id)sender {
    btnTermsConditions.selected = !btnTermsConditions.isSelected;
}

- (IBAction)btnContinueClicked:(UIButton*)sender {
    
    // Validation
    if (txtViewAddPincode.textColor == [UIColor lightGrayColor] || ![[txtViewAddPincode.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] length]) {
        UDShowToastAlertWithTitle(@"Please enter address and pincode", 2); return;
    }
    if (!validateNumber(txtFieldContact.text)) {
        UDShowToastAlertWithTitle(@"Please enter valid phone number", 2); return;
    }
    if (txtViewDelAdd.textColor == [UIColor lightGrayColor] || ![[txtViewDelAdd.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] length]) {
        UDShowToastAlertWithTitle(@"Please enter delivery address", 2); return;
    }
    if ([[txtViewPin.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] length] != 6) {
        UDShowToastAlertWithTitle(@"Please enter 6 digit pincode", 2); return;
    }
    if (txtViewRemarks.textColor == [UIColor lightGrayColor] || ![[txtViewRemarks.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] length]) {
        UDShowToastAlertWithTitle(@"Please enter remarks", 2); return;
    }
    if (!btnTermsConditions.isSelected) {
        UDShowToastAlertWithTitle(@"Please accept terms & conditions", 2); return;
    }
    
    if (!APP_DELEGATE.isServerReachable) {
        UDShowToastAlertWithTitle(kNoInternet, 2);
        return;
    }
    
    sender.enabled = NO;
    
    NSMutableDictionary *dict = [NSMutableDictionary dictionary];
    [dict setValue:APP_CONSTANTS.strCustCode forKey:@"customer_code"];
    [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"user_type"];
    [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"dealer_id"] forKey:@"dns_customer_code"];
    [dict setValue:txtViewDelAdd.text forKey:@"address"];
    [dict setValue:txtViewPin.text forKey:@"pin"];
    [dict setValue:txtViewRemarks.text forKey:@"remarks"];
    [dict setValue:txtViewAddPincode.text forKey:@"printed_address_pin"];
    [dict setValue:txtFieldContact.text forKey:@"contact_num_printed"];
    [dict setValue:self.strPaymentBy forKey:@"payment_by"];
    
    [_arrPickedItems enumerateObjectsUsingBlock:^(NSDictionary *dictItems, NSUInteger idx, BOOL *stop){
        [dict setValue:[dictItems safeValueeForKey:@"dns_prod_code"] forKey:[NSString stringWithFormat:@"order_data[%lu][dns_prod_code]", (unsigned long)idx]];
        [dict setValue:[dictItems safeValueeForKey:@"prod_desc"] forKey:[NSString stringWithFormat:@"order_data[%lu][prod_desc]", (unsigned long)idx]];
        [dict setValue:[dictItems safeValueeForKey:@"input_value"] forKey:[NSString stringWithFormat:@"order_data[%lu][qty]", (unsigned long)idx]];
        [dict setValue:[dictItems safeValueeForKey:@"prod_image"] forKey:[NSString stringWithFormat:@"order_data[%lu][prod_image]", (unsigned long)idx]];
        
        [dict setValue:APP_CONSTANTS.strCustCode forKey:[NSString stringWithFormat:@"order_data[%lu][customer_code]", (unsigned long)idx]];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"dealer_id"] forKey:[NSString stringWithFormat:@"order_data[%lu][dns_customer_code]", (unsigned long)idx]];
        [dict setValue:txtViewAddPincode.text forKey:[NSString stringWithFormat:@"order_data[%lu][printed_address_pin]", (unsigned long)idx]];
        [dict setValue:txtFieldContact.text forKey:[NSString stringWithFormat:@"order_data[%lu][contact_num_printed]", (unsigned long)idx]];
        [dict setValue:txtViewDelAdd.text forKey:[NSString stringWithFormat:@"order_data[%lu][address]", (unsigned long)idx]];
        [dict setValue:txtViewPin.text forKey:[NSString stringWithFormat:@"order_data[%lu][pin]", (unsigned long)idx]];
        [dict setValue:txtViewRemarks.text forKey:[NSString stringWithFormat:@"order_data[%lu][remarks]", (unsigned long)idx]];
    }];
    
    AppLog(@"-->>%@", dict);
    
    NSError *error;
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:dict options:0 error:&error];
    if (!jsonData) {
        AppLog(@"Got an error: %@", error);
    } else {
        NSString *jsonString = [[NSString alloc] initWithData:jsonData encoding:NSUTF8StringEncoding];
        AppLog(@"-->>%@", jsonString);
    }
    
    [SVProgressHUD showWithStatus:@"Uploading..."];
    
    // Native NSURLSession POST
    NSURL *url = [NSURL URLWithString:@"https://starsaathi.com/SAP/save_pop_order_date_v1.php"];
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:url];
    request.HTTPMethod = @"POST";
    [request setValue:@"application/json" forHTTPHeaderField:@"Content-Type"];
    request.HTTPBody = jsonData;
    
    NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithRequest:request
                                                                 completionHandler:^(NSData * _Nullable data,
                                                                                     NSURLResponse * _Nullable response,
                                                                                     NSError * _Nullable error) {
        dispatch_async(dispatch_get_main_queue(), ^{
            [SVProgressHUD dismiss];
            sender.enabled = YES;
        });
        
        if (error) {
            dispatch_async(dispatch_get_main_queue(), ^{
                UDShowToastAlertWithTitle(error.localizedDescription, 2);
            });
            return;
        }
        
        NSError *jsonError;
        NSDictionary *responseObject = [NSJSONSerialization JSONObjectWithData:data options:0 error:&jsonError];
        
        if (jsonError) {
            dispatch_async(dispatch_get_main_queue(), ^{
                UDShowToastAlertWithTitle(@"Failed to parse response", 2);
            });
            return;
        }
        
        dispatch_async(dispatch_get_main_queue(), ^{
            AppLog(@"-->>%@", responseObject);
            
            if ([[responseObject safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
                
                if (self.flagForPG) {
                    [self performSegueWithIdentifier:@"popOrderToWebview"
                                              sender:[responseObject safeValueeForKey:@"the_payment_url"]];
                } else {
                    UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil
                                                                                   message:@"The POP order successfully received."
                                                                            preferredStyle:UIAlertControllerStyleAlert];
                    [alert addAction:[UIAlertAction actionWithTitle:@"Ok"
                                                              style:UIAlertActionStyleDefault
                                                            handler:^(UIAlertAction * _Nonnull action) {
                        for (UIViewController *controller in self.navigationController.viewControllers) {
                            if ([controller isKindOfClass:[PopOrderListViewController class]]) {
                                [[NSNotificationCenter defaultCenter] postNotificationName:@"resetPopOrder" object:self];
                                [self.navigationController popToViewController:controller animated:YES];
                                return;
                            }
                        }
                    }]];
                    [self presentViewController:alert animated:YES completion:nil];
                }
                
            } else {
                UDShowToastAlertWithTitle([responseObject safeValueeForKey:@"process_message"], 2);
            }
        });
    }];
    
    [task resume];
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"popOrderToWebview"]) {
        PopOrderWebVC *powvc = segue.destinationViewController;
        powvc.strPaymentUrl = (NSString*)sender;
    }
}

#pragma mark - UITextField Delegate

- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    return [textField resignFirstResponder];
}

- (void)textFieldDidBeginEditing:(UITextField *)textField {
    activeField = textField;
}

- (void)textFieldDidEndEditing:(UITextField *)textField {
    activeField = nil;
}

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
    NSString *resultText = [textField.text stringByReplacingCharactersInRange:range withString:string];
    if (textField == txtFieldContact) return resultText.length <= 10;
    if (textField == txtViewPin) return resultText.length <= 6;
    return YES;
}

#pragma mark - UITextView Delegate

- (void)textViewDidBeginEditing:(UITextView *)textView {
    activeField = textView;
    if (textView.textColor == [UIColor lightGrayColor]) {
        textView.text = @"";
        textView.textColor = [UIColor blackColor];
    }
}

- (void)textViewDidEndEditing:(UITextView *)textView {
    activeField = nil;
    if (![[textView.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] length]) {
        textView.textColor = [UIColor lightGrayColor];
        if (textView == txtViewAddPincode) textView.text = @"Enter address, pin code";
        else if (textView == txtViewDelAdd) textView.text = @"Please enter";
        else if (textView == txtViewRemarks) textView.text = @"Please enter";
    }
}

- (BOOL)textView:(UITextView *)textView shouldChangeTextInRange:(NSRange)range replacementText:(NSString *)text {
    NSCharacterSet *cs = [[NSCharacterSet characterSetWithCharactersInString:ACCEPTABLE_CHARACTERS] invertedSet];
    NSString *filtered = [[text componentsSeparatedByCharactersInSet:cs] componentsJoinedByString:@""];
    return [text isEqualToString:filtered];
}

#pragma mark - Keyboard Method

-(void)keyboardWillShow:(NSNotification*)notification {
    NSDictionary* info = [notification userInfo];
    CGSize kbSize = [[info objectForKey:UIKeyboardFrameEndUserInfoKey] CGRectValue].size;
    
    UIEdgeInsets contentInsets = UIEdgeInsetsMake(0.0, 0.0, kbSize.height, 0.0);
    fpScrollView.contentInset = contentInsets;
    fpScrollView.scrollIndicatorInsets = contentInsets;
    
    CGRect aRect = self.view.frame;
    aRect.size.height -= kbSize.height;
    if (!CGRectContainsPoint(aRect, activeField.frame.origin) ) {
        [fpScrollView scrollRectToVisible:activeField.frame animated:YES];
    }
}

-(void)keyboardWillHide:(NSNotification*)notification {
    UIEdgeInsets contentInsets = UIEdgeInsetsZero;
    fpScrollView.contentInset = contentInsets;
    fpScrollView.scrollIndicatorInsets = contentInsets;
}

@end
