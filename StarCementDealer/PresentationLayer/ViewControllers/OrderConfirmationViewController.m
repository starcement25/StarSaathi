//
//  OrderConfirmationViewController.m
//  StarCementDealer
//

#import "OrderConfirmationViewController.h"
#import "OrderConfirmationCell.h"
#import "ProductMasterBO.h"
#import "UIAlertView+Category.h"
#import "DashboardViewController.h"
#import "NSString+CharHandling.h"
#import <SVProgressHUD/SVProgressHUD.h>

@interface OrderConfirmationViewController ()<UITableViewDataSource, UITableViewDelegate>{
    __weak IBOutlet UITableView *tblViewProduct;
    __weak IBOutlet UILabel *lblShipTo;
    __weak IBOutlet UILabel *lblConsigneeName;
    __weak IBOutlet UILabel *lblConsigneeAddress;
    __weak IBOutlet UILabel *lblFright;
    __weak IBOutlet UILabel *lblDestinationAddress;
    __weak IBOutlet UILabel *lblPhone;
    __weak IBOutlet UILabel *lblDumpDetails;
    __weak IBOutlet UILabel *lblDumpName;
    __weak IBOutlet NSLayoutConstraint *constraintTblProdHeight;
}

@end

@implementation OrderConfirmationViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    NSLog(@"🔹 THIS PAGE OPEN:");
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView{
    constraintTblProdHeight.constant = _arrProductList.count * 30;
    
    [tblViewProduct registerNib:[UINib nibWithNibName:@"OrderConfirmationCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewProduct.separatorColor = [UIColor clearColor];
    tblViewProduct.tableFooterView = [UIView new];
}

-(void)loadData{
    float fltTotalQuantity = 0;
    for (ProductMasterBO *PMBO in _arrProductList) {
        fltTotalQuantity += [PMBO.strQuantity floatValue];
    }
    
    lblShipTo.text = [NSString stringWithFormat:@"SHIP TO : %@",_strOrderForType];
    lblConsigneeName.text = _strName;
    lblConsigneeAddress.text = _strAddress;
    lblFright.text = [NSString stringWithFormat:@"FRIEGHT : %@",_strFrieght];
    lblDestinationAddress.text = _strFrieghtAddress;
    lblPhone.text = [NSString stringWithFormat:@"PHONE : %@",_strPhoneNumber];
    lblDumpDetails.text = [NSString stringWithFormat:@"DUMP DETAILS : %@",_strDumpStatus];
    lblDumpName.text = _strDumpAddress;
}

#pragma mark - UITableView Delegate & DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return _arrProductList.count;
}

- (OrderConfirmationCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    OrderConfirmationCell *cell = [tableView dequeueReusableCellWithIdentifier:@"cell" forIndexPath:indexPath];
    ProductMasterBO *PMBO = _arrProductList[indexPath.row];
    cell.lblProductName.text = [NSString stringWithFormat:@"%@ : %@ MT", PMBO.strProdDesc, PMBO.strQuantity];
    return cell;
}

#pragma mark - IBAction's

- (IBAction)btnContinueClicked:(UIButton *)sender {
    if (!APP_DELEGATE.isServerReachable) {
        UDShowToastAlertWithTitle(kNoInternet, 2);
        return;
    }
    
    NSMutableDictionary *dict = [NSMutableDictionary dictionary];
    
    if (checkUserType(kSubDealer)) {
        [_arrProductList enumerateObjectsUsingBlock:^(ProductMasterBO *PMBO, NSUInteger idx, BOOL *stop) {
            NSString *strAppOrderNo = [NSString stringWithFormat:@"O%@%@%lu", APP_CONSTANTS.strCustCode, getTimestamp(), (unsigned long)idx];
            NSString *strOrderFor = [NSString stringWithFormat:@"%@, %@", _strName, _strAddress];
            NSString *strBelongDealerCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"kBelongDealerCode"];
            NSString *strBelongDealerDnsCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"kBelongDealerDnsCode"];
            
            [dict setValue:strAppOrderNo forKey:[NSString stringWithFormat:@"order_data[%lu][apporderno]", (unsigned long)idx]];
            [dict setValue:@"" forKey:[NSString stringWithFormat:@"order_data[%lu][erporderdt]", (unsigned long)idx]];
            [dict setValue:strOrderFor forKey:[NSString stringWithFormat:@"order_data[%lu][order_for]", (unsigned long)idx]];
            [dict setValue:APP_CONSTANTS.strCustCode forKey:[NSString stringWithFormat:@"order_data[%lu][sub_dealer_code]", (unsigned long)idx]];
            [dict setValue:@"Sub Dealer" forKey:[NSString stringWithFormat:@"order_data[%lu][order_for_type]", (unsigned long)idx]];
            [dict setValue:strBelongDealerCode forKey:[NSString stringWithFormat:@"order_data[%lu][belong_dealer_code]", (unsigned long)idx]];
            [dict setValue:strBelongDealerDnsCode forKey:[NSString stringWithFormat:@"order_data[%lu][belong_dealer_dns_code]", (unsigned long)idx]];
            [dict setValue:PMBO.strProdCode forKey:[NSString stringWithFormat:@"order_data[%lu][prod_code]", (unsigned long)idx]];
            [dict setValue:PMBO.strQuantity forKey:[NSString stringWithFormat:@"order_data[%lu][qty]", (unsigned long)idx]];
            [dict setValue:_strPhoneNumber forKey:[NSString stringWithFormat:@"order_data[%lu][sub_dealer_phone_no]", (unsigned long)idx]];
        }];
        [dict setValue:@"Sub Dealer" forKey:@"user_type"];
        [dict setValue:@"" forKey:@"login_user_id"];
        
        [self postOrderWithDict:dict urlString:@"https://starsaathi.com/SAP/acedns_star_save_subdealer_app_order.php" sender:sender];
    } else {
        [_arrProductList enumerateObjectsUsingBlock:^(ProductMasterBO *PMBO, NSUInteger idx, BOOL *stop) {
            NSString *strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
            NSString *strAppOrderNo = [NSString stringWithFormat:@"O%@%@%lu", strEmpCode, getTimestamp(), (unsigned long)idx];
            NSString *strOrderFor = [NSString stringWithFormat:@"%@, %@", _strName, _strAddress];
            
            [dict setValue:strAppOrderNo forKey:[NSString stringWithFormat:@"order_data[%lu][apporderno]", (unsigned long)idx]];
            [dict setValue:@"" forKey:[NSString stringWithFormat:@"order_data[%lu][erporderdt]", (unsigned long)idx]];
            [dict setValue:strOrderFor forKey:[NSString stringWithFormat:@"order_data[%lu][order_for]", (unsigned long)idx]];
            [dict setValue:APP_CONSTANTS.strCustCode forKey:[NSString stringWithFormat:@"order_data[%lu][customer_code]", (unsigned long)idx]];
            [dict setValue:PMBO.strProdCode forKey:[NSString stringWithFormat:@"order_data[%lu][prod_code]", (unsigned long)idx]];
            [dict setValue:PMBO.strQuantity forKey:[NSString stringWithFormat:@"order_data[%lu][qty]", (unsigned long)idx]];
            [dict setValue:_strDestinationName forKey:[NSString stringWithFormat:@"order_data[%lu][destination_name]", (unsigned long)idx]];
            [dict setValue:_strDestinationCode forKey:[NSString stringWithFormat:@"order_data[%lu][destination_code]", (unsigned long)idx]];
            [dict setValue:_strFrieght forKey:[NSString stringWithFormat:@"order_data[%lu][freight]", (unsigned long)idx]];
            [dict setValue:_strFrieghtAddress forKey:[NSString stringWithFormat:@"order_data[%lu][destination_address]", (unsigned long)idx]];
            [dict setValue:_strPhoneNumber forKey:[NSString stringWithFormat:@"order_data[%lu][phone_no]", (unsigned long)idx]];
            [dict setValue:_strDumpStatus forKey:[NSString stringWithFormat:@"order_data[%lu][dump_status]", (unsigned long)idx]];
            [dict setValue:_strDumpAddress forKey:[NSString stringWithFormat:@"order_data[%lu][dump_name]", (unsigned long)idx]];
            [dict setValue:_strDealerTruckStatus forKey:[NSString stringWithFormat:@"order_data[%lu][dealer_truck]", (unsigned long)idx]];
            [dict setValue:_strSubDealerId forKey:[NSString stringWithFormat:@"order_data[%lu][sub_dealer_code]", (unsigned long)idx]];
            [dict setValue:_strDumpCode forKey:[NSString stringWithFormat:@"order_data[%lu][dump_code]", (unsigned long)idx]];
            [dict setValue:_strOrderForType forKey:[NSString stringWithFormat:@"order_data[%lu][order_for_type]", (unsigned long)idx]];
            [dict setValue:_strDeliveryPoint forKey:[NSString stringWithFormat:@"order_data[%lu][Delivery_point]", (unsigned long)idx]];
            [dict setValue:_strDeliveryRemarks forKey:[NSString stringWithFormat:@"order_data[%lu][Remarks_text]", (unsigned long)idx]];
        }];
        
        if (checkUserType(kbroker)) {
            [dict setValue:@"BROKER" forKey:@"user_type"];
            [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"dns_broker_id"] forKey:@"login_user_id"];
        } else {
            [dict setValue:@"DEALER" forKey:@"user_type"];
        }
        
        [self postOrderWithDict:dict urlString:@"https://starsaathi.com/SAP/acedns_star_save_online_offline_app_order_new_v4.php" sender:sender];
    }
}

#pragma mark - Network Method using NSURLSession
- (void)postOrderWithDict:(NSDictionary *)dict urlString:(NSString *)urlString sender:(UIButton *)sender {
    // Convert dictionary to form-data string
    NSMutableArray *paramsArray = [NSMutableArray array];
    [dict enumerateKeysAndObjectsUsingBlock:^(id key, id obj, BOOL *stop) {
        NSString *encodedKey = [[NSString stringWithFormat:@"%@", key]
            stringByAddingPercentEncodingWithAllowedCharacters:[NSCharacterSet URLQueryAllowedCharacterSet]];
        NSString *encodedValue = [[NSString stringWithFormat:@"%@", obj ?: @""]
            stringByAddingPercentEncodingWithAllowedCharacters:[NSCharacterSet URLQueryAllowedCharacterSet]];
        NSString *param = [NSString stringWithFormat:@"%@=%@", encodedKey, encodedValue];
        [paramsArray addObject:param];
    }];
    
    NSString *bodyString = [paramsArray componentsJoinedByString:@"&"];
    NSData *bodyData = [bodyString dataUsingEncoding:NSUTF8StringEncoding];
    
    printf("🔹 POST URL: %s\n", [urlString UTF8String]);
    printf("🔹 Request Body (Form Data):\n%s\n", [bodyString UTF8String]);

    // Prepare request
    NSURL *url = [NSURL URLWithString:urlString];
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:url];
    request.HTTPMethod = @"POST";
    [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
    request.HTTPBody = bodyData;
    
    [SVProgressHUD showWithStatus:@"Uploading..."];
    sender.enabled = NO;
    
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
        NSDictionary *responseDict = [NSJSONSerialization JSONObjectWithData:data options:0 error:&jsonError];
        if (jsonError || !responseDict) {
            NSString *rawResponse = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
            printf("🔹 Raw Response:\n%s\n", [rawResponse UTF8String]);
            dispatch_async(dispatch_get_main_queue(), ^{
                UDShowToastAlertWithTitle(@"Invalid server response", 2);
            });
            return;
        }

        dispatch_async(dispatch_get_main_queue(), ^{
            NSData *responseData = [NSJSONSerialization dataWithJSONObject:responseDict options:NSJSONWritingPrettyPrinted error:nil];
            NSString *jsonString1 = [[NSString alloc] initWithData:responseData encoding:NSUTF8StringEncoding];
            printf("🔹 Result : %s\n", [jsonString1 UTF8String]);
            
            if ([[responseDict safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
                UDShowToastAlertWithTitle([responseDict safeValueeForKey:@"process_message"], 2);
                [self performSegueWithIdentifier:@"confirmatonToThankYou" sender:self];
            } else {
                UDShowToastAlertWithTitle([responseDict safeValueeForKey:@"process_message"], 2);
            }
        });
    }];
    
    [task resume];
}


// - (void)postOrderWithDict:(NSDictionary *)dict urlString:(NSString *)urlString sender:(UIButton *)sender {
//     NSError *error;
//     NSData *jsonData = [NSJSONSerialization dataWithJSONObject:dict options:0 error:&error];
//     if (!jsonData) {
//         AppLog(@"JSON error: %@", error);
//         return;
//     }

//     NSString *jsonString = [[NSString alloc] initWithData:jsonData encoding:NSUTF8StringEncoding];
//     printf("🔹 POST URL: %s\n", [urlString UTF8String]);
//     printf("🔹 Request Body:\n%s\n", [jsonString UTF8String]);

//     NSURL *url = [NSURL URLWithString:urlString];
//     NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:url];
//     request.HTTPMethod = @"POST";
//     [request setValue:@"application/json" forHTTPHeaderField:@"Content-Type"];
//     request.HTTPBody = jsonData;
    
//     [SVProgressHUD showWithStatus:@"Uploading..."];
//     sender.enabled = NO;
    
//     NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithRequest:request
//                                                                  completionHandler:^(NSData * _Nullable data,
//                                                                                      NSURLResponse * _Nullable response,
//                                                                                      NSError * _Nullable error) {
//         dispatch_async(dispatch_get_main_queue(), ^{
//             [SVProgressHUD dismiss];
//             sender.enabled = YES;
//         });
        
//         if (error) {
//             dispatch_async(dispatch_get_main_queue(), ^{
//                 UDShowToastAlertWithTitle(error.localizedDescription, 2);
//             });
//             return;
//         }
        
//         NSDictionary *responseDict = [NSJSONSerialization JSONObjectWithData:data options:0 error:nil];
//         dispatch_async(dispatch_get_main_queue(), ^{
//             NSData *responseData = [NSJSONSerialization dataWithJSONObject:responseDict options:NSJSONWritingPrettyPrinted error:nil];
//             NSString *jsonString1 = [[NSString alloc] initWithData:responseData encoding:NSUTF8StringEncoding];
//             printf("🔹 Result : %s\n", [jsonString1 UTF8String]);
//             if ([[responseDict safeValueeForKey:@"process_status"] isEqualToString:@"YES"]) {
//                 UDShowToastAlertWithTitle([responseDict safeValueeForKey:@"process_message"], 2);
//                 [self performSegueWithIdentifier:@"confirmatonToThankYou" sender:self];
//             } else {
//                 UDShowToastAlertWithTitle([responseDict safeValueeForKey:@"process_message"], 2);
//             }
//         });
//     }];
    
//     [task resume];
// }

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

@end
