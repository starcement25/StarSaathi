//
//  MaterialReceivedViewController.m
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 17/05/21.
//  Copyright © 2021 Coral . All rights reserved.
//

#import "MaterialReceivedViewController.h"

@interface MaterialReceivedViewController (){
    NSString *strQuality;
    NSString *strQuantity;
    //__weak IBOutlet UITextView *txtViewRemarks;
    IBOutletCollection(UIButton) NSArray *btnCollQuantity;
    IBOutletCollection(UIButton) NSArray *btnCollQuality;
    __weak IBOutlet UITextField *tfNoOfBagsShort;
    __weak IBOutlet UITextField *tfNoOfDamagedBags;
}

@end

@implementation MaterialReceivedViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

- (void)designView {
    [self setUpToolbar];
}

-(void)loadData {
    strQuality = @"OK";
    strQuantity = @"OK";
}

#pragma mark - IBAction's

- (IBAction)btnQuantityClicked:(UIButton *)sender {
    for (UIButton *btn in btnCollQuantity) {
        [btn setSelected:false];
    }
    [sender setSelected:true];
    strQuantity = sender.titleLabel.text;
}

- (IBAction)btnQualityClicked:(UIButton *)sender {
    
    for (UIButton *btn in btnCollQuality) {
        [btn setSelected:false];
    }
    [sender setSelected:true];
    strQuality = sender.titleLabel.text;    
}

- (IBAction)btnReceivedClicked:(UIButton *)sender {
    
    NSString *strNoOfBagsShort = [tfNoOfBagsShort.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
    NSString *strNoOfDamagedBags = [tfNoOfDamagedBags.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
    
    if ([strQuantity isEqualToString:@"NOT OK"]) {
        if (!strNoOfBagsShort.length) {
            UDShowToastAlertWithTitle(@"Please enter quantity.", 2);
            return;
        }
    }
    
    if ([strQuality isEqualToString:@"NOT OK"]){
        if (!strNoOfDamagedBags.length) {
            UDShowToastAlertWithTitle(@"Please enter quantity.", 2);
            return;
        }
    }
    
    if (APP_DELEGATE.isServerReachable) {
        
//        NSString *strCustCode;
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
//        }else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"sub dealer"]){
//            strCustCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
//        }else{
//            NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
//            FMDatabase *db = [FMDatabase databaseWithPath:path];
//            if ([db open]) {
//                FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
//                while ([s next]) {
//                    strCustCode = [s stringForColumn:@"customer_code"];
//                }
//                [db close];
//            }
//        }
        //the_id,apporderno,quantity_checking,quality_checking,remarks
        //the_id,apporderno,ch_uid,challanno,quantity_checking,quality_checking,remarks
        
//        NSString *strNoOfBagsShort = [tfNoOfBagsShort.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
//        NSString *strNoOfDamagedBags = [tfNoOfDamagedBags.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
        [dict setValue:_arrChallanDetails[0]     forKey:@"apporderno"];
        [dict setValue:_arrChallanDetails[1]     forKey:@"ch_uid"];
        [dict setValue:_arrChallanDetails[2]     forKey:@"challanno"];
        [dict setValue:strQuantity               forKey:@"quantity_checking"];
        [dict setValue:strQuality                forKey:@"quality_checking"];
        [dict setValue:strNoOfBagsShort          forKey:@"ch_quantity_no_of_bags"];
        [dict setValue:strNoOfDamagedBags        forKey:@"ch_quality_no_of_damaged_bags"];
        //[dict setValue:txtViewRemarks.text      forKey:@"remarks"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_star_update_material_receive_confirmation_v2.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                [self showAlertWithStatus:[dictResponse valueForKey:@"process_status"] message:[dictResponse valueForKey:@"process_message"]];
//                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
//                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
//                    [self.navigationController popViewControllerAnimated:true];
//                }else
//                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
}

-(void)showAlertWithStatus:(NSString*)strStatus message:(NSString*)strMsg{
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil message:strMsg preferredStyle:UIAlertControllerStyleAlert];
    [alert addAction:[UIAlertAction actionWithTitle:@"OK" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
        if ([strStatus isEqualToString:@"YES"]) {
            [self.navigationController popViewControllerAnimated:true];
        }
    }]];
    [self presentViewController:alert animated:true completion:nil];
}

-(IBAction)btnBackClicked:(id)sender {
    [self.navigationController popViewControllerAnimated:true];
}

#pragma mark - Helper Method

-(void)setUpToolbar {
    
    UIToolbar *toolbar = [[UIToolbar alloc] initWithFrame:CGRectZero];
    toolbar.barStyle = UIBarStyleDefault;
    [toolbar sizeToFit];
    UIBarButtonItem *flexSpace = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:self action:nil];
    UIBarButtonItem *doneBtn = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone target:self action:@selector(resignPicker)];
    [toolbar setItems:@[flexSpace, doneBtn] animated:YES];
    
    tfNoOfBagsShort.inputAccessoryView = toolbar;
    tfNoOfDamagedBags.inputAccessoryView = toolbar;
    //txtViewRemarks.inputAccessoryView = toolbar;
}

-(void)resignPicker{
    [self.view endEditing:YES];
}




@end
