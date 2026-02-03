//
//  SubDealerListForLiftingAllocation.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 18/06/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import "SubDealerListForLiftingAllocation.h"
#import "SubDealerForLiftingAllocationCell.h"
#import "CustomerMasterBO.h"
#import "AllocationConfirmationVC.h"

@interface SubDealerListForLiftingAllocation ()<UITableViewDelegate, UITableViewDataSource, UISearchBarDelegate>{
    __weak IBOutlet UITableView *tblViewSubDealer;
    NSMutableArray *arrDealerSubDealer;
    NSMutableArray *arrDealerSubDealerCopy;
    NSMutableArray *arrSelectedSubDealers;
}

@end

@implementation SubDealerListForLiftingAllocation

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"back_arrow"] style:UIBarButtonItemStylePlain target:self action:@selector(btnBackClicked:)];
    btnBack.tintColor = [UIColor whiteColor];
    self.navigationItem.leftBarButtonItem = btnBack;
    
    [tblViewSubDealer registerNib:[UINib nibWithNibName:@"SubDealerForLiftingAllocationCell" bundle:nil] forCellReuseIdentifier:@"cell"];
}

-(void)loadData {
    arrDealerSubDealer = [[NSMutableArray alloc] init];
    arrDealerSubDealerCopy = [[NSMutableArray alloc] init];
    arrSelectedSubDealers = [[NSMutableArray alloc] init];
    [self getSubDealerListFromTable];
}

#pragma mark - IBAction's

-(void)btnBackClicked:(UIBarButtonItem*)button{
    [self.navigationController popViewControllerAnimated:true];
}

- (IBAction)btnSubmitClicked:(id)sender {
    if (arrSelectedSubDealers.count == 0) {
        UDShowToastAlertWithTitle(@"Please select atleast one sub dealer", 2);
        return;
    }
    [self performSegueWithIdentifier:@"liftingAllocationSubDealerListToAllocate" sender:self];
}

#pragma mark - Segue

-(void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"liftingAllocationSubDealerListToAllocate"]) {
        AllocationConfirmationVC *acvc = segue.destinationViewController;
        acvc.dictOrder = self.dictOrder;
        acvc.arrSelectedSubDealers = arrSelectedSubDealers;
    }
}

#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return arrDealerSubDealer.count;
}

- (SubDealerForLiftingAllocationCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    static NSString *cellIdentifier = @"cell";
    SubDealerForLiftingAllocationCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    CustomerMasterBO *CMBO = arrDealerSubDealer[indexPath.row];
    cell.lblSubDealerName.text = CMBO.strCustomerName;
    BOOL flag = ([arrSelectedSubDealers containsObject:CMBO]) ? true : false;
    [cell.btnCheckBox setSelected:flag];    
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    
    CustomerMasterBO *CMBO = arrDealerSubDealer[indexPath.row];
    
    if (![arrSelectedSubDealers containsObject:CMBO]) {
        [arrSelectedSubDealers addObject:CMBO];
    }else{
        [arrSelectedSubDealers removeObject:CMBO];
    }
    [tblViewSubDealer reloadData];
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
    [tblViewSubDealer reloadData];
}

- (void)searchBarSearchButtonClicked:(UISearchBar *)searchBar{
    [searchBar resignFirstResponder];
}

#pragma mark - Database Method

-(void)getSubDealerListFromTable{
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        
        NSString *strQuery;
        
//        if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//            strQuery = [NSString stringWithFormat:@"SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type IN( 'Sub Dealer','RSSD') AND rds_tag =  '%@' ORDER BY customer_name ASC",APP_CONSTANTS.strCustCode];
//        }else{
//            strQuery = @"SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type != 'Dealer' ORDER BY customer_name ASC";
//        }
        
        if (checkUserType(kbroker) == true) {
            strQuery = [NSString stringWithFormat:@"SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type IN( 'Sub Dealer','RSSD') AND rds_tag =  '%@' ORDER BY customer_name ASC",APP_CONSTANTS.strCustCode];
        }else{
            strQuery = @"SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type != 'Dealer' ORDER BY customer_name ASC";
        }    
        
        FMResultSet *s = [db executeQuery:strQuery];
        while ([s next]) {
            CustomerMasterBO *CMBO = [[CustomerMasterBO alloc]init];
            CMBO.strCustomerCode = [s stringForColumn:@"customer_code"];
            CMBO.strCustomerName = [s stringForColumn:@"customer_name"];
            CMBO.strAddress = [s stringForColumn:@"address"];
            CMBO.strPhoneNo = [s stringForColumn:@"phone_no"];
            CMBO.strSapCode = [s stringForColumn:@"SAP_code"];
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
    [tblViewSubDealer reloadData];
}


@end
