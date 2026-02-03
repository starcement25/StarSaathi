//
//  SelectDestinationViewController.m
//  StarCementDealer
//
//  Created by Apple on 28/09/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "SelectDestinationViewController.h"
#import "DestinationMasterBO.h"

@interface SelectDestinationViewController ()<UITableViewDelegate, UITableViewDataSource>{    
    __weak IBOutlet UISearchBar *searchBarDestination;
    __weak IBOutlet UITableView *tblViewDestination;
    NSMutableArray *arrDestination;
    NSMutableArray *arrDestinationTemp;
}

@end

@implementation SelectDestinationViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [self designView];
    [self loadData];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView{
    tblViewDestination.tableFooterView = [UIView new];
}

-(void)loadData{
    arrDestination = [[NSMutableArray alloc]init];
    arrDestinationTemp = [[NSMutableArray alloc]init];
    if ([self.strIdentifier isEqualToString:@"shipTo"]) {
        [self showDestination];
    }else if ([self.strIdentifier isEqualToString:@"frieght"]){
        [self showDestinationForFrieght];
    }else{
        [self showDestinationForDump];
    }
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - UISearchBar Delegate

- (void)searchBar:(UISearchBar *)searchBar textDidChange:(NSString *)searchText{
    
    [arrDestination removeAllObjects];
    for (DestinationMasterBO *dmbo in arrDestinationTemp) {
        if ([dmbo.strDestinationName rangeOfString:searchText options:NSCaseInsensitiveSearch].location != NSNotFound){
            [arrDestination addObject:dmbo];
        }
    }
    if (!searchText.length) {
        arrDestination = arrDestinationTemp.mutableCopy;
    }
    [tblViewDestination reloadData];
}

- (void)searchBarSearchButtonClicked:(UISearchBar *)searchBar{
    [searchBar resignFirstResponder];
}

#pragma mark - UITableView Delegate and Datasource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return arrDestination.count;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *cellIdentifier = @"cell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    DestinationMasterBO *dmbo = arrDestination[indexPath.row];
    cell.textLabel.text = dmbo.strDestinationName;
    cell.textLabel.font = [UIFont systemFontOfSize:14.0 weight:UIFontWeightMedium];
    cell.textLabel.textColor = UIColorFromRGB(0x3C4648);
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    DestinationMasterBO *dmbo = arrDestination[indexPath.row];
    if (_delegate && [_delegate respondsToSelector:@selector(dataFromControllerCode:DestinationName:FieldIdentifier:)]) {
        [_delegate dataFromControllerCode:dmbo.strDestinationCode DestinationName:dmbo.strDestinationName FieldIdentifier:self.strIdentifier];
    }
    
    [self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - Database Method

-(void)showDestination{
    
    if ([APP_CONSTANTS.db open]) {
        FMResultSet *s = [APP_CONSTANTS.db executeQuery:@"SELECT * from destination_master"];
        while ([s next]) {
            DestinationMasterBO *dmbo = [[DestinationMasterBO alloc]init];
            dmbo.strDestinationCode = [s stringForColumn:@"destination_code"];
            dmbo.strDestinationName = [s stringForColumn:@"destination_name"];
            [arrDestination addObject:dmbo];
            [arrDestinationTemp addObject:dmbo];
        }
        [APP_CONSTANTS.db close];
    }
    [tblViewDestination reloadData];
}

-(void)showDestinationForFrieght{
    
    if ([APP_CONSTANTS.db open]) {
        
        NSString *strQuery = [NSString stringWithFormat:@"SELECT * from destination_master where ex_for_type = '%@'",self.strFrieghtType];
        FMResultSet *s = [APP_CONSTANTS.db executeQuery:strQuery];
        while ([s next]) {
            DestinationMasterBO *dmbo = [[DestinationMasterBO alloc]init];
            dmbo.strDestinationCode = [s stringForColumn:@"destination_code"];
            dmbo.strDestinationName = [s stringForColumn:@"destination_name"];
            [arrDestination addObject:dmbo];
            [arrDestinationTemp addObject:dmbo];
        }
        [APP_CONSTANTS.db close];
    }
    [tblViewDestination reloadData];
}

-(void)showDestinationForDump{
    
    if ([APP_CONSTANTS.db open]) {
        
        NSString *strQuery = [NSString stringWithFormat:@"SELECT * from branch_dump"];
        FMResultSet *s = [APP_CONSTANTS.db executeQuery:strQuery];
        while ([s next]) {
            DestinationMasterBO *dmbo = [[DestinationMasterBO alloc]init];
            dmbo.strDestinationCode = [s stringForColumn:@"dump_code"];
            dmbo.strDestinationName = [s stringForColumn:@"dump_name"];
            [arrDestination addObject:dmbo];
            [arrDestinationTemp addObject:dmbo];
        }
        [APP_CONSTANTS.db close];
    }
    [tblViewDestination reloadData];
}


@end
