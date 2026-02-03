//
//  AgeingViewController.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 25/06/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import "AgeingViewController.h"
#import "AgeingCell.h"

@interface AgeingViewController ()<UITableViewDelegate, UITableViewDataSource>{
    __weak IBOutlet UILabel *lblDate;
    __weak IBOutlet UITableView *tblViewAgeing;
    NSArray *arrAgeing;
}

@end

@implementation AgeingViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    [tblViewAgeing registerNib:[UINib nibWithNibName:@"AgeingCell" bundle:nil] forCellReuseIdentifier:@"cell"];
}

-(void)loadData {
    
    NSDate *dateCurrent = [NSDate date];
    APP_CONSTANTS.dateFormater.dateFormat = @"MMM dd, yyyy";
    lblDate.text = [APP_CONSTANTS.dateFormater stringFromDate:dateCurrent];
    
    [self getAgeing];
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(id)sender {
    [self.navigationController popViewControllerAnimated:true];
}

#pragma mark - Web Service

-(void)getAgeing {
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSString *strSapCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:strSapCode forKey:@"customer_code"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/dealerwise-ageing-data.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    arrAgeing = [dictResponse safeValueeForKey:@"ageing_data"];
                    [tblViewAgeing reloadData];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return arrAgeing.count;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    static NSString *cellIdentifier = @"cell";
    AgeingCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    NSDictionary *dict = arrAgeing[indexPath.row];
    cell.lblDocumentNo.text = [dict safeValueeForKey:@"document_no"];
    cell.lblDocumentDate.text = [dict safeValueeForKey:@"document_date"];
    cell.lblInvoiceAmt.text = [dict safeValueeForKey:@"inv_amount"];
    return cell;
}

@end
