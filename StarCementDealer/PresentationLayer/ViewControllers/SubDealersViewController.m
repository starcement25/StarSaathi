//
//  SubDealersViewController.m
//  StarCementDealer
//
//  Created by Coral  on 05/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "SubDealersViewController.h"
#import "SubDealerCell.h"
#import "CustomerMasterBO.h"
#import "NSString+CharHandling.h"
#import "XMLParser.h"

@interface SubDealersViewController ()<UITableViewDelegate, UITableViewDataSource, UISearchBarDelegate>{
    __weak IBOutlet UISearchBar *searchBarSubDealer;
    __weak IBOutlet UITableView *tblViewSubDealers;
    NSMutableArray *arrDealerSubDealer;
    NSMutableArray *arrDealerSubDealerCopy;
    NSString *strDataDownloadTime;
    NSIndexPath *indexPathSelected;
}

@end

@implementation SubDealersViewController

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
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView{
    [tblViewSubDealers registerNib:[UINib nibWithNibName:@"SubDealerCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewSubDealers.separatorColor = [UIColor clearColor];
}

-(void)loadData{
    
    arrDealerSubDealer = [[NSMutableArray alloc] init];
    arrDealerSubDealerCopy = [[NSMutableArray alloc] init];
    if ([self.data isEqualToString:@"order"]) {
        [self wsShipToPartyMaster];
    }else{
        //Performance
        [self getSubDealerListFromTable];
    }
}

#pragma mark - Web Service

-(void)wsShipToPartyMaster {
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc] init];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"emp_code"];
        [dict setValue:self.strType forKey:@"user_type"];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"login_type"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/ship-to-party-master-txt-V2.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    
                    if ([self.strType isEqualToString:@"dealer"]) {
                        //self
                        NSArray *arrDealerList = [dictResponse valueForKey:@"dealer_data"];
                        for (NSDictionary *dict in arrDealerList) {
                            CustomerMasterBO *CMBO = [[CustomerMasterBO alloc]init];
                            CMBO.strCustomerCode = [dict safeValueeForKey:@"customer_code"];
                            CMBO.strCustomerName = [dict safeValueeForKey:@"customer_name"];
                            CMBO.strAddress = [dict safeValueeForKey:@"address"];
                            CMBO.strPhoneNo = [dict safeValueeForKey:@"phone_no"];
                            [arrDealerSubDealer addObject:CMBO];
                            [arrDealerSubDealerCopy addObject:CMBO];
                        }
                        
                        arrDealerSubDealer = [[arrDealerSubDealer sortedArrayUsingComparator:^NSComparisonResult(CustomerMasterBO *a, CustomerMasterBO *b) {
                            return [a.strCustomerName compare:b.strCustomerName];
                        }]mutableCopy];
                        
                        arrDealerSubDealerCopy = [[arrDealerSubDealerCopy sortedArrayUsingComparator:^NSComparisonResult(CustomerMasterBO *a, CustomerMasterBO *b) {
                            return [a.strCustomerName compare:b.strCustomerName];
                        }]mutableCopy];
                        
                        [tblViewSubDealers reloadData];
                    }else{
                        //sub-dealer
                        NSArray *arrSubDealerList = [dictResponse valueForKey:@"sub_dealer_data"];
                        for (NSDictionary *dict in arrSubDealerList) {
                            CustomerMasterBO *CMBO = [[CustomerMasterBO alloc]init];
                            CMBO.strCustomerCode = [dict safeValueeForKey:@"customer_code"];
                            CMBO.strCustomerName = [dict safeValueeForKey:@"customer_name"];
                            CMBO.strAddress = [dict safeValueeForKey:@"address"];
                            CMBO.strPhoneNo = [dict safeValueeForKey:@"phone_no"];
                            [arrDealerSubDealer addObject:CMBO];
                            [arrDealerSubDealerCopy addObject:CMBO];
                        }
                        [self getSubDealerListFromTable];
                    }
                    
                }else{
                    if ([self.strType isEqualToString:@"dealer"]) {
                        //self
                        if (_delegate && [_delegate respondsToSelector:@selector(noDealerFound)]) {
                            [_delegate noDealerFound];
                        }
                        UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                        [self.navigationController popViewControllerAnimated:true];
                    }else{
                        //sub-dealer
                        UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                        [self getSubDealerListFromTable];
                        
                    }
                }
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return arrDealerSubDealer.count;
}

- (SubDealerCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *cellIdentifier =  @"cell";
    SubDealerCell *cell = [tblViewSubDealers dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    CustomerMasterBO *CMBO = arrDealerSubDealer[indexPath.row];
    cell.lblSubDealer.text = CMBO.strCustomerName;
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    
    CustomerMasterBO *CMBO = arrDealerSubDealer[indexPath.row];
    if ([_data isEqualToString:@"performance"]) {
        if (_delegate && [_delegate respondsToSelector:@selector(dataFromControllerCustCode:)]) {
            [_delegate  dataFromControllerCustCode:CMBO.strCustomerCode];
        }
    }else{
        if (_delegate && [_delegate respondsToSelector:@selector(dataFromControllerName:Address:Phone:SubDealerId:)]) {
            
            [self wsDetinationMaster:CMBO.strCustomerCode];
            [self wsDumpMaster:CMBO.strCustomerCode];
            
            [_delegate dataFromControllerName:CMBO.strCustomerName Address:CMBO.strAddress Phone:CMBO.strPhoneNo SubDealerId:CMBO.strCustomerCode];
        }
    }
    [self.navigationController popViewControllerAnimated:YES];
}

-(CGFloat)tableView:(UITableView *)tableView estimatedHeightForRowAtIndexPath:(NSIndexPath *)indexPath{
    return UITableViewAutomaticDimension;
}

-(CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath{
    return UITableViewAutomaticDimension;
}

#pragma mark - UIScrollView Delegate

- (void)scrollViewWillBeginDragging:(UIScrollView *)scrollView {
    [searchBarSubDealer resignFirstResponder];
}

#pragma mark - UISearchBar Delegate

- (void)searchBar:(UISearchBar *)searchBar textDidChange:(NSString *)searchText{
    
    if (searchText.length != 0) {
        NSPredicate *bPredicate = [NSPredicate predicateWithFormat:@"SELF.strCustomerName contains[cd] %@",searchText];
        arrDealerSubDealer = [[arrDealerSubDealerCopy filteredArrayUsingPredicate:bPredicate] mutableCopy];
        AppLog(@"HERE %@",arrDealerSubDealer);
    }else{
        arrDealerSubDealer = arrDealerSubDealerCopy;
    }
    [tblViewSubDealers reloadData];
}

- (void)searchBarSearchButtonClicked:(UISearchBar *)searchBar{    
    [searchBar resignFirstResponder];
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - Database Method

-(void)getSubDealerListFromTable{
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        
        NSString *strQuery;
        
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strQuery = [NSString stringWithFormat:@"SELECT address, customer_name, customer_code, phone_no FROM customer_master WHERE  cust_type IN( 'Sub Dealer','RSSD') AND rds_tag =  '%@' ORDER BY customer_name ASC",APP_CONSTANTS.strCustCode];
//        }else{
//            strQuery = @"SELECT address, customer_name, customer_code, phone_no FROM customer_master WHERE  cust_type != 'Dealer' ORDER BY customer_name ASC";
//        }
        
        if (checkUserType(kbroker) == true) {
            strQuery = [NSString stringWithFormat:@"SELECT address, customer_name, customer_code, phone_no FROM customer_master WHERE  cust_type IN( 'Sub Dealer','RSSD') AND rds_tag =  '%@' ORDER BY customer_name ASC",APP_CONSTANTS.strCustCode];
        }else{
            strQuery = @"SELECT address, customer_name, customer_code, phone_no FROM customer_master WHERE  cust_type != 'Dealer' ORDER BY customer_name ASC";
        }
        
        
        FMResultSet *s = [db executeQuery:strQuery];
        while ([s next]) {
            CustomerMasterBO *CMBO = [[CustomerMasterBO alloc]init];
            CMBO.strCustomerCode = [s stringForColumn:@"customer_code"];
            CMBO.strCustomerName = [s stringForColumn:@"customer_name"];
            CMBO.strAddress = [s stringForColumn:@"address"];
            CMBO.strPhoneNo = [s stringForColumn:@"phone_no"];
            [arrDealerSubDealer addObject:CMBO];
            [arrDealerSubDealerCopy addObject:CMBO];
        }
        [db close];
    }
    arrDealerSubDealer = [[arrDealerSubDealer sortedArrayUsingComparator:^NSComparisonResult(CustomerMasterBO *a, CustomerMasterBO *b) {
        return [a.strCustomerName compare:b.strCustomerName];
    }]mutableCopy];
    
    arrDealerSubDealerCopy = [[arrDealerSubDealerCopy sortedArrayUsingComparator:^NSComparisonResult(CustomerMasterBO *a, CustomerMasterBO *b) {
        return [a.strCustomerName compare:b.strCustomerName];
    }]mutableCopy];
    [tblViewSubDealers reloadData];
}

-(void)wsDetinationMaster:(NSString*)strCustomerCode {
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    
    NSString *strLastUpdateTime;
    if ([db open]) {
        
        [db executeUpdate:@"DELETE from destination_master"];
        
        FMResultSet *s = [db executeQuery:@"SELECT last_download_time FROM data_download_log where table_name = 'destination_master'"];
        while ([s next]) {
            strLastUpdateTime = [s stringForColumn:@"last_download_time"];
            strLastUpdateTime = [NSString handleSpecialSymbol:strLastUpdateTime]; // Handlin euro sign
        }
        
        FMResultSet *set = [db executeQuery:@"SELECT last_download_time FROM data_download_log where table_name = 'download_dictionary'"];
        while ([set next]) {
            strDataDownloadTime = [set stringForColumn:@"last_download_time"];
            strDataDownloadTime = [NSString handleSpecialSymbol:strDataDownloadTime];
        }
        
        [db close];
    }
    
    NSMutableDictionary *dictParam = [[NSMutableDictionary alloc]init];
    //[dictParam setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"user_type"];
    [dictParam setValue:@"START"                                forKey:@"nick_name"];
    [dictParam setValue:strCustomerCode                         forKey:@"emp_code"];
    [dictParam setValue:@"no"                                   forKey:@"incremental_download"];
    [dictParam setValue:strLastUpdateTime                       forKey:@"last_update_time"];
    [dictParam setValue:strDataDownloadTime                     forKey:@"data_download_time"];
    
//    if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//        [dictParam setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"dns_broker_id"] forKey:@"broker_id"];
//    }
    
    if (checkUserType(kbroker) == true) {
        [dictParam setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"dns_broker_id"] forKey:@"broker_id"];
    }
    
    [SVProgressHUD show];
    NSString *strFinalUrl = @"https://starsaathi.com/SAP/destination-master-txt_v2-6.0.4.php";
    [XMLParser callServiceWithPostData:strFinalUrl withParam:dictParam success:^(NSData *data){
        [SVProgressHUD dismiss];
        if (data) {
            NSArray  *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
            NSString *documentsDirectory = [paths objectAtIndex:0];
            NSString *fileName = [NSString stringWithFormat:@"%@.txt",@"destination_master"];
            NSString *filePath = [NSString stringWithFormat:@"%@/%@", documentsDirectory,fileName];
            [data writeToFile:filePath atomically:YES];
            [self insertDestinationMasterWithFilePath:filePath];
            
            if (_delegate && [_delegate respondsToSelector:@selector(setDestinationAddress)]) {
                [_delegate setDestinationAddress];
            }
        }else{
            //AppLog(@"->aabrakadaabra : %@",strTblName);
        }
    }failed:^(NSString *strErrorMsg){
        AppLog(@"Send to the login page.");
        [SVProgressHUD dismiss];
        UDShowToastAlertWithTitle(strErrorMsg, 2);
        [self.navigationController popViewControllerAnimated:YES];
    }];
}

-(void)wsDumpMaster:(NSString*)strCustomerCode {
    
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    
    NSString *strLastUpdateTime;
    if ([db open]) {
        
        [db executeUpdate:@"DELETE from branch_dump"];
        
        FMResultSet *s = [db executeQuery:@"SELECT last_download_time FROM data_download_log where table_name = 'branch_dump'"];
        while ([s next]) {
            strLastUpdateTime = [s stringForColumn:@"last_download_time"];
            strLastUpdateTime = [NSString handleSpecialSymbol:strLastUpdateTime]; // Handlin euro sign
        }
        
        FMResultSet *set = [db executeQuery:@"SELECT last_download_time FROM data_download_log where table_name = 'download_dictionary'"];
        while ([set next]) {
            strDataDownloadTime = [set stringForColumn:@"last_download_time"];
            strDataDownloadTime = [NSString handleSpecialSymbol:strDataDownloadTime];
        }
        
        [db close];
    }
    
    NSMutableDictionary *dictParam = [[NSMutableDictionary alloc]init];
    //[dictParam setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] forKey:@"user_type"];
    [dictParam setValue:@"START"                                forKey:@"nick_name"];
    [dictParam setValue:strCustomerCode                         forKey:@"emp_code"];
    [dictParam setValue:@"no"                                   forKey:@"incremental_download"];
    [dictParam setValue:strLastUpdateTime                       forKey:@"last_update_time"];
    [dictParam setValue:strDataDownloadTime                     forKey:@"data_download_time"];
    
    [SVProgressHUD show];
    NSString *strFinalUrl = @"https://starsaathi.com/SAP/branch-dump-master-txt_v2-6.0.3.php";
    [XMLParser callServiceWithPostData:strFinalUrl withParam:dictParam success:^(NSData *data){
        [SVProgressHUD dismiss];
        if (data) {
            NSArray  *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
            NSString *documentsDirectory = [paths objectAtIndex:0];
            NSString *fileName = [NSString stringWithFormat:@"%@.txt",@"branch_dump"];
            NSString *filePath = [NSString stringWithFormat:@"%@/%@", documentsDirectory,fileName];
            [data writeToFile:filePath atomically:YES];
            [self insertBranchDumpWithFilePath:filePath];
        }else{
            //AppLog(@"->aabrakadaabra : %@",strTblName);
        }
    }failed:^(NSString *strErrorMsg){
        AppLog(@"Send to the login page.");
        [SVProgressHUD dismiss];
        UDShowToastAlertWithTitle(strErrorMsg, 2);
        [self.navigationController popViewControllerAnimated:YES];
    }];
}

-(void)insertDestinationMasterWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        FMDatabase *db = [FMDatabase databaseWithPath:path];
        [db open];
        
        //CREATE TABLE destination_master (destination_code TEXT NOT NULL,destination_name TEXT NOT NULL)
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrDM = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrDM.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO destination_master(destination_code, destination_name, ex_for_type) VALUES (?, ?, ?)",arrDM[0],arrDM[1],arrDM[2]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}

-(void)insertBranchDumpWithFilePath:(NSString*)filePath{
    
    NSString *stringContent = [NSString stringWithContentsOfFile:filePath encoding:NSASCIIStringEncoding error:nil];
    NSArray *arrContent = [stringContent componentsSeparatedByString:@"\n"];
    AppLog(@"-->>%@",arrContent);
    AppLog(@"\n Result = %@",stringContent);
    
    NSString *dataCount = arrContent[0];
    int intRowCount     = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:0] intValue];
    int intColumnCount  = [[[dataCount componentsSeparatedByString:@"¥"] objectAtIndex:1] intValue];
    
    if (intRowCount != 0) {
        //Insert records
        NSString *strDataTime = arrContent[1];
        AppLog(@"-->>%@",strDataTime);
        
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        FMDatabase *db = [FMDatabase databaseWithPath:path];
        [db open];
        
        //CREATE TABLE branch_dump (branch_code TEXT NOT NULL,dump_code TEXT NOT NULL,dump_name TEXT NOT NULL,acedns TEXT NOT NULL,is_plant TEXT NOT NULL,download_time TEXT NOT NULL)
        
        for (int i = 2; i < intRowCount + 2; i++) {
            NSArray *arrBD = [[arrContent objectAtIndex:i] componentsSeparatedByString:@"^"];
            if (arrBD.count == intColumnCount) {
                // procced to insert data.
                
                BOOL success = [db executeUpdate:@"INSERT INTO branch_dump(branch_code, dump_code, dump_name, acedns, is_plant, download_time) VALUES (?, ?, ?, ?, ?, ?)",arrBD[0],arrBD[1],arrBD[2],arrBD[3],arrBD[4],arrBD[5]];
                if (!success) {
                    AppLog(@"error = %@", [db lastErrorMessage]);
                }
                
            }else{
                // Mismatch column data.
            }
        }
        [db close];
        
    }else{
        AppLog(@"Thier is no records to insert.");
    }
}


@end
