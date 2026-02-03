//
//  AllocationListWithSubDealerVC.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 20/06/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import "AllocationListWithSubDealerVC.h"
#import "CustomerMasterBO.h"

@interface AllocationListWithSubDealerVC ()<UITextFieldDelegate>{
    __weak IBOutlet UILabel *lblProdName;
    __weak IBOutlet UITextField *txtFieldInputQty;
    __weak IBOutlet UILabel *lblRemainingAllocationQty;
    __weak IBOutlet UILabel *lblDispatchQty;
    float fltTotalAvailableAllocationQty;
    IBOutlet UIToolbar *toolbar;
    NSString *strChallanNo;
    float fltTotalDispatchQty;
}

@end

@implementation AllocationListWithSubDealerVC

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

- (void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    if (APP_CONSTANTS.fltAvailableAllocationQty > 0.0) {
        lblRemainingAllocationQty.text = [NSString stringWithFormat:@"Remaining Allocation Qty %.2f",APP_CONSTANTS.fltAvailableAllocationQty];
        fltTotalAvailableAllocationQty = APP_CONSTANTS.fltAvailableAllocationQty;
    }
}

#pragma mark - Initialization Method

-(void)designView {
    txtFieldInputQty.inputAccessoryView = toolbar;
}

-(void)loadData {
    
    //    {
    //        "available_allocation_qty" = "38.000000";
    //        "challan_no" = "8000688049,8000687388";
    //        "dispatch_qty" = "40.000000";
    //        "dns_prod_code" = 14000092;
    //        "order_date" = "6th Jul 2024 08:07 AM";
    //        "prod_display_name" = "STAR CEMENT PPC (ADSTAR) TRADE";
    //    }
    
    
    
    
    //    fltTotalDispatchQty = [[self.dictOrder safeValueeForKey:@"dispatch_qty"] floatValue];
    //    fltTotalAvailableAllocationQty = [[self.dictOrder safeValueeForKey:@"available_allocation_qty"] floatValue];
    
    fltTotalDispatchQty = [[self.dictOrderFiltered safeValueeForKey:@"inv_qty"] floatValue];
    fltTotalAvailableAllocationQty = [[self.dictOrderFiltered safeValueeForKey:@"available_allocation_qty"] floatValue];
    
    
    //lblProdName.text = [self.dictOrder safeValueeForKey:@"prod_display_name"];
    
    lblProdName.text = [self.dictOrder safeValueeForKey:@"prod_display_name"];
    
    //    NSArray *arrDispatchedChallanData = [self.dictOrder safeValueeForKey:@"dispatched_challan_data"];
    //
    //    //float fltTotalDispatchQty = 0.0;
    //
    //    NSMutableArray *arrChallanNo = [[NSMutableArray alloc] init];
    //    for (NSDictionary *dictChallanData in arrDispatchedChallanData) {
    //        float fltDispatchQty = [[dictChallanData safeValueeForKey:@"dispatch_qty"] floatValue];
    //        float fltAvailableAllocationQty = [[dictChallanData safeValueeForKey:@"available_allocation_qty"] floatValue];
    //        [arrChallanNo addObject:[dictChallanData safeValueeForKey:@"challanno"]];
    //
    //        fltTotalDispatchQty += fltDispatchQty;
    //        fltTotalAvailableAllocationQty += fltAvailableAllocationQty;
    //    }
    //strChallanNo = [self.dictOrder safeValueeForKey:@"challan_no"];
    
    strChallanNo = [self.dictOrderFiltered safeValueeForKey:@"INVNO"];
    
    lblRemainingAllocationQty.text = [NSString stringWithFormat:@"Remaining Allocation Qty %.2f",fltTotalAvailableAllocationQty];
    lblDispatchQty.text = [NSString stringWithFormat:@"Dispatch Qty %.2f",fltTotalDispatchQty];
}

#pragma mark - IBAction's

- (IBAction)btnAllocateClicked:(UIButton *)sender {
    
    if (![[txtFieldInputQty.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] length]) {
        UDShowToastAlertWithTitle(@"Please input value", 2);
        return;
    }
    
    if (APP_DELEGATE.isServerReachable) {
        
        CustomerMasterBO *CMBO = self.arrSelectedSubDealers[self.idx];
        
        /*
         
         NSString *strOrderDate = [self.dictOrder safeValueeForKey:@"order_date"];
         //        NSUInteger length = ([strOrderDate length] == 22) ? 13 : 12;
         //        NSArray *arrOrderDate = [[strOrderDate substringToIndex:length] componentsSeparatedByString:@" "];
         //        NSString *strDate = [arrOrderDate objectAtIndex:0];
         //        strDate = [strDate substringToIndex:[strDate length] - 2];
         //        NSString *strFormattedDate = [NSString stringWithFormat:@"%@-%@-%@",strDate, arrOrderDate[1], arrOrderDate[2]];
         //
         //        APP_CONSTANTS.dateFormater.dateFormat = @"dd-MMM-yyyy";
         //        NSDate *dateDispatch = [APP_CONSTANTS.dateFormater dateFromString:strFormattedDate];
         //        APP_CONSTANTS.dateFormater.dateFormat = @"yyyy-MM-dd";
         //        NSString *strDispatchDate = [APP_CONSTANTS.dateFormater stringFromDate:dateDispatch];
         
         // Define the substrings to be replaced and their replacements
         NSDictionary *replacements = @{
         @"st": @"",
         @"nd": @"",
         @"rd": @"",
         @"th": @""
         };
         
         // Iterate over the dictionary and replace occurrences
         for (NSString *key in replacements) {
         NSString *replacement = replacements[key];
         strOrderDate = [strOrderDate stringByReplacingOccurrencesOfString:key withString:replacement];
         }
         
         // Output the resulting string
         AppLog(@"%@", strOrderDate);
         
         
         APP_CONSTANTS.dateFormater.dateFormat = @"dd MMM yyyy hh:mm a";
         NSDate *date = [APP_CONSTANTS.dateFormater dateFromString:strOrderDate];
         APP_CONSTANTS.dateFormater.dateFormat = @"yyyy-MM-dd HH:mm:ss"; //2024-07-06 12:52:32
         NSString *strDate = [APP_CONSTANTS.dateFormater stringFromDate:date];
         
         */
        
        
        NSString *strCustomerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
        NSString *strOrderId = [NSString stringWithFormat:@"L_%@%@_%@",strCustomerId,getTimestamp(),self.dictOrder[@"order_id"]];
        NSString *strDispatchQty = [NSString stringWithFormat:@"%.2f",fltTotalDispatchQty];
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        
        //        allocation_data[0][APPORDERNO]=SS0490435
        //        &allocation_data[0][customer_id]=1000000341
        //        &allocation_data[0][inv_date]=9th Oct 2024
        //        &allocation_data[0][inv_no]=F23600059931
        //        &allocation_data[0][inv_qty]=3.0
        //        &allocation_data[0][order_id]=L__20241029125252_SS0490435
        //        &allocation_data[0][prod_desc]=STAR CEMENT PPC TRADE
        //        &allocation_data[0][qty]=1.0
        //        &allocation_data[0][sub_dealer_id]=1500015878
        
        //        allocation_data[0][APPORDERNO]=SS0506040
        //        &allocation_data[0][customer_id]=1000000341
        //        &allocation_data[0][inv_date]=2024-11-06
        //        &allocation_data[0][inv_no]=F23600069057
        //        &allocation_data[0][inv_qty]=40.0
        //        &allocation_data[0][order_id]=INV__20241111171712_SS0506040
        //        &allocation_data[0][prod_desc]=STAR CEMENT PPC TRADE
        //        &allocation_data[0][qty]=1.0
        //        &allocation_data[0][sub_dealer_id]=1500015878
        
        [dict setValue:[self.dictOrder safeValueeForKey:@"order_id"]    forKey:@"allocation_data[0][APPORDERNO]"];
        [dict setValue:strCustomerId                                    forKey:@"allocation_data[0][customer_id]"];
        
        //        NSString *formattedDate = [self formatDateString:[self.dictOrder safeValueeForKey:@"invoice_date"]];
        //        NSLog(@"%@", formattedDate); // Output: "9th Oct 2024"
        
        
        [dict setValue:[self.dictOrder safeValueeForKey:@"invoice_date"]    forKey:@"allocation_data[0][inv_date]"];
        [dict setValue:[self.dictOrder safeValueeForKey:@"invoice_no"]      forKey:@"allocation_data[0][inv_no]"];
        [dict setValue:strDispatchQty                                       forKey:@"allocation_data[0][inv_qty]"];
        
        [dict setValue:strOrderId                                           forKey:@"allocation_data[0][order_id]"];
        [dict setValue:self.dictOrder[@"prod_display_name"]                 forKey:@"allocation_data[0][prod_desc]"];
        [dict setValue:txtFieldInputQty.text                                forKey:@"allocation_data[0][qty]"];
        [dict setValue:CMBO.strSapCode                                      forKey:@"allocation_data[0][sub_dealer_id]"];
        
        [SVProgressHUD showWithStatus:@"Submitting..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/save_allocation_details_invoicewise.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    
                    fltTotalAvailableAllocationQty = fltTotalAvailableAllocationQty - [txtFieldInputQty.text floatValue];
                    txtFieldInputQty.text = @"";
                    lblRemainingAllocationQty.text = [NSString stringWithFormat:@"Remaining Allocation Qty %.2f",fltTotalAvailableAllocationQty];
                    APP_CONSTANTS.fltAvailableAllocationQty = fltTotalAvailableAllocationQty;
                    
                    [[NSNotificationCenter defaultCenter] postNotificationName:@"updateLiftingAllocation"
                                                                        object:nil
                                                                      userInfo:nil];
                    
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                    
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
}

- (IBAction)dismissKeyboard:(id)sender {
    [self.view endEditing:true];
}

#pragma mark - UITextFieldDelegate

//- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
//
//    NSString *newString = [textField.text stringByReplacingCharactersInRange:range withString:string];
//    NSArray *arrDotValue = [newString componentsSeparatedByString:@"."];
//    float fltInputValue = [newString floatValue];
//
//    if (fltInputValue > fltTotalAvailableAllocationQty) {
//        txtFieldInputQty.layer.borderColor = [[UIColor redColor] CGColor];
//        txtFieldInputQty.layer.borderWidth = 1.0;
//    }else{
//        txtFieldInputQty.layer.borderColor = [[UIColor clearColor] CGColor];
//        txtFieldInputQty.layer.borderWidth = 0.0;
//    }
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
//        }else{
//            return NO;
//        }
//    }
//
//    return (newString.length<=6);
//}

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
    NSString *currentText = textField.text ?: @"";
    NSString *newText = [currentText stringByReplacingCharactersInRange:range withString:string];
    
    // Allow deletion of characters
    if ([string isEqualToString:@""]) {
        return YES;
    }
    
    // Restrict to a maximum value (Remaining allocation quantity)
    //double maxValue = 9999.99;
    double inputValue = [newText doubleValue];
    if (inputValue > fltTotalAvailableAllocationQty) {
        UDShowToastAlertWithTitle([NSString stringWithFormat:@"Max input value is %.2f ",fltTotalAvailableAllocationQty], 2);
        return NO;
    }
    
    // Allow only two digits after the decimal point
    NSArray *components = [newText componentsSeparatedByString:@"."];
    if (components.count > 1 && [components.lastObject length] > 2) {
        UDShowToastAlertWithTitle(@"Only 2 digit allowed after decimal point", 2);
        return NO;
    }
    
    return YES;
}

#pragma mark - Helper Method

- (NSString *)formatDateString:(NSString *)dateString {
    // Step 1: Create a date formatter for the input format
    
    [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
    
    // Step 2: Convert the string to an NSDate object
    NSDate *date = [APP_CONSTANTS.dateFormater dateFromString:dateString];
    
    // Step 3: Check if the date conversion succeeded
    if (!date) {
        return nil; // Return nil if the date string was invalid
    }
    
    // Step 4: Create an output date formatter for the desired format
    [APP_CONSTANTS.dateFormater setDateFormat:@"d MMM yyyy"]; // Output format: "9 Oct 2024"
    
    // Step 5: Convert the NSDate to the output string format
    NSString *formattedDate = [APP_CONSTANTS.dateFormater stringFromDate:date];
    
    // Step 6: Append the ordinal suffix (st, nd, rd, th) to the day
    NSString *dayWithSuffix = [self addOrdinalSuffixToDate:formattedDate];
    
    return dayWithSuffix;
}

- (NSString *)addOrdinalSuffixToDate:(NSString *)dateString {
    // Extract the day from the formatted string
    NSArray *dateComponents = [dateString componentsSeparatedByString:@" "];
    NSInteger day = [[dateComponents firstObject] integerValue];
    
    NSString *suffix;
    if (day % 10 == 1 && day != 11) {
        suffix = @"st";
    } else if (day % 10 == 2 && day != 12) {
        suffix = @"nd";
    } else if (day % 10 == 3 && day != 13) {
        suffix = @"rd";
    } else {
        suffix = @"th";
    }
    
    // Replace the day component with the suffixed version
    NSString *dayWithSuffix = [NSString stringWithFormat:@"%ld%@", (long)day, suffix];
    NSArray *remainingComponents = [dateComponents subarrayWithRange:NSMakeRange(1, dateComponents.count - 1)];
    NSString *finalDate = [[@[dayWithSuffix] arrayByAddingObjectsFromArray:remainingComponents] componentsJoinedByString:@" "];
    
    return finalDate;
}


@end
