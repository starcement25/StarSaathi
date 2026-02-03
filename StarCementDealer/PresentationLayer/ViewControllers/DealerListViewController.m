//
//  DealerListViewController.m
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 30/03/20.
//  Copyright © 2020 Coral . All rights reserved.
//

#import "DealerListViewController.h"
#import "DealerCell.h"
#import "CustomerMasterBO.h"

@interface DealerListViewController (){
    __weak IBOutlet UISearchBar *searchBarSubDealer;
    __weak IBOutlet UITableView *tblViewDealer;
    NSMutableArray *arrDealer;
}

@end

@implementation DealerListViewController

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
    
    AppLog(@"-->>%@",[[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"]);
    AppLog(@"-->>%lu",(unsigned long)[[[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"] length]);
    
    if (([[[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"] length] == 0)) {
        self.navigationItem.leftBarButtonItem.enabled = false;
        self.navigationItem.leftBarButtonItem.tintColor = [UIColor clearColor];
    }
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView{
    [tblViewDealer registerNib:[UINib nibWithNibName:@"DealerCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewDealer.separatorColor = [UIColor clearColor];
}

-(void)loadData{
    arrDealer = [[NSMutableArray alloc]init];
    [self getSubDealerList];
}

#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return arrDealer.count;
}

- (DealerCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *cellIdentifier =  @"cell";
    DealerCell *cell = [tblViewDealer dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    CustomerMasterBO *CMBO = arrDealer[indexPath.row];
    cell.lblDealer.text = CMBO.strCustomerName;
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    
    CustomerMasterBO *CMBO = arrDealer[indexPath.row];
    
    if (_delegate && [_delegate respondsToSelector:@selector(dataFromControllerName:CustomerCode:SapCode:)]) {
        [_delegate dataFromControllerName:CMBO.strCustomerName CustomerCode:CMBO.strCustomerCode SapCode:CMBO.strSapCode];
    }
    
//    if (_delegate && [_delegate respondsToSelector:@selector(dataFromControllerName:CustomerCode:)]) {
//        [_delegate dataFromControllerName:CMBO.strCustomerName CustomerCode:CMBO.strCustomerCode];
//    } 
    [self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - UIScrollView Delegate

- (void)scrollViewWillBeginDragging:(UIScrollView *)scrollView {
    [searchBarSubDealer resignFirstResponder];
}

#pragma mark - UISearchBar Delegate

- (void)searchBar:(UISearchBar *)searchBar textDidChange:(NSString *)searchText{
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        
        NSString *strQuery = [NSString stringWithFormat:@"select address, customer_name, phone_no, customer_code, SAP_code FROM customer_master WHERE  cust_type == 'Dealer' AND customer_name LIKE '%%%@%%'",searchBar.text];
        [arrDealer removeAllObjects];
        FMResultSet *s = [db executeQuery:strQuery];
        while ([s next]) {
            CustomerMasterBO *CMBO = [[CustomerMasterBO alloc]init];
            CMBO.strCustomerCode = [s stringForColumn:@"customer_code"];
            CMBO.strCustomerName = [s stringForColumn:@"customer_name"];
            CMBO.strAddress = [s stringForColumn:@"address"];
            CMBO.strPhoneNo = [s stringForColumn:@"phone_no"];
            CMBO.strSapCode = [s stringForColumn:@"SAP_code"];
            [arrDealer addObject:CMBO];
        }
        [db close];
    }
    [tblViewDealer reloadData];
}

- (void)searchBarSearchButtonClicked:(UISearchBar *)searchBar{
    [searchBar resignFirstResponder];
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - Database Method

-(void)getSubDealerList{
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        FMResultSet *s = [db executeQuery:@"SELECT address, customer_name, phone_no, customer_code, SAP_code FROM customer_master WHERE cust_type == 'Dealer' ORDER BY customer_name ASC"];
        while ([s next]) {
            CustomerMasterBO *CMBO = [[CustomerMasterBO alloc]init];
            CMBO.strCustomerCode = [s stringForColumn:@"customer_code"];
            CMBO.strCustomerName = [s stringForColumn:@"customer_name"];
            CMBO.strAddress = [s stringForColumn:@"address"];
            CMBO.strPhoneNo = [s stringForColumn:@"phone_no"];
            CMBO.strSapCode = [s stringForColumn:@"SAP_code"];
            [arrDealer addObject:CMBO];
        }
        [db close];
    }
    [tblViewDealer reloadData];
}

@end
