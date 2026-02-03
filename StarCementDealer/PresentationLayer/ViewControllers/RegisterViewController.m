//
//  RegisterViewController.m
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 21/05/21.
//  Copyright © 2021 Coral . All rights reserved.
//

#import "RegisterViewController.h"

@interface RegisterViewController ()<UITextFieldDelegate>{
    
    __weak IBOutlet UITextField *txtFieldName;
    __weak IBOutlet UITextField *txtFieldMobileNumber;
    IBOutlet UIToolbar *toolbar;
}

@end

@implementation RegisterViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [self.navigationController setNavigationBarHidden:NO animated:animated];
    self.navigationController.navigationBar.tintColor = [UIColor whiteColor];
    //self.navigationItem.hidesBackButton = YES;
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
}

#pragma mark - Initialization Method

-(void)designView{
    txtFieldMobileNumber.inputAccessoryView = toolbar;
}

-(void)loadData{
    
}

#pragma mark - UITextField Delegate

- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    return [textField resignFirstResponder];
}

#pragma mark - IBAction's

- (IBAction)resignKeyboard:(id)sender {
    [self.view endEditing:true];
}

- (IBAction)btnRegisterClicked:(UIButton *)sender {
    
    if (![txtFieldName.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]].length) {
        UDShowToastAlertWithTitle(@"Please enter name", 2);
        return;
    }else if (!validateNumber(txtFieldMobileNumber.text)){
        UDShowToastAlertWithTitle(@"Please enter mobile number.", 2);
        return;
    }
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:txtFieldName.text        forKey:@"name"];
        [dict setValue:txtFieldMobileNumber.text   forKey:@"mobile"];
        
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/app_reg_api.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 5);
                    [self.navigationController popViewControllerAnimated:true];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
}


@end
