//
//  PendingInvoicesViewController.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 25/06/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import "PendingInvoicesViewController.h"
#import "InvoiceCell.h"
#import "WebViewController.h"

@interface PendingInvoicesViewController ()<UITableViewDelegate, UITableViewDataSource>{
    __weak IBOutlet UILabel *lblStartDate;
    __weak IBOutlet UILabel *lblEndDate;
    __weak IBOutlet UIButton *btnStartDate;
    __weak IBOutlet UIButton *btnEndDate;
    __weak IBOutlet UITableView *tblViewInvoice;
    __weak IBOutlet UIView *viewDatePicker;
    __weak IBOutlet UIDatePicker *datePickerInvoice;
    
    NSString *strCustCode;
    NSString *strFromDate;
    NSString *strToDate;
    NSMutableArray *arrInvoices;
    int integerBtnDateIdentifier;
}


@end

@implementation PendingInvoicesViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    
    datePickerInvoice.maximumDate = [NSDate date];
    viewDatePicker.hidden = true;
    [tblViewInvoice registerNib:[UINib nibWithNibName:@"InvoiceCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewInvoice.tableFooterView = [UIView new];
}

-(void)loadData {
    
    NSDate *dateFrom = [[NSCalendar currentCalendar] dateByAddingUnit:NSCalendarUnitDay value:-7 toDate:[NSDate date] options:0];
    NSDate *dateTo = [NSDate date];
    
    [APP_CONSTANTS.dateFormater setDateFormat:@"MMM dd, yyyy"];
    
    lblStartDate.text = [APP_CONSTANTS.dateFormater stringFromDate:dateFrom];
    lblEndDate.text = [APP_CONSTANTS.dateFormater stringFromDate:dateTo];
    
    [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
    
    strFromDate = [APP_CONSTANTS.dateFormater stringFromDate:dateFrom];
    strToDate = [APP_CONSTANTS.dateFormater stringFromDate:dateTo];
    
    //intPageNo = 1;
    arrInvoices = [[NSMutableArray alloc]init];
    
    [self getInvoices];
}

#pragma mark - Web Service

-(void)getInvoices {
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:APP_CONSTANTS.strCustCode forKey:@"customer_code"];
        [dict setValue:strFromDate forKey:@"from_date"];
        [dict setValue:strToDate forKey:@"to_date"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/acedns_show_invoice_list.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    [arrInvoices removeAllObjects];
                    [arrInvoices addObjectsFromArray:[dictResponse safeValueeForKey:@"data"]];
                    [tblViewInvoice reloadData];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:true];
}

- (IBAction)btnStartDateClicked:(UIButton *)sender {
    integerBtnDateIdentifier = (int)sender.tag;
    btnStartDate.userInteractionEnabled = false;
    btnEndDate.userInteractionEnabled = false;
    viewDatePicker.hidden = false;
}

- (IBAction)btnEndDateClicked:(UIButton *)sender {
    integerBtnDateIdentifier = (int)sender.tag;
    btnStartDate.userInteractionEnabled = false;
    btnEndDate.userInteractionEnabled = false;
    viewDatePicker.hidden = false;
}

- (IBAction)btnDoneButtonClicked:(UIBarButtonItem *)sender {
    
    btnStartDate.userInteractionEnabled = true;
    btnEndDate.userInteractionEnabled = true;
    viewDatePicker.hidden = true;
    
    if (integerBtnDateIdentifier == 101) {
        [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
        strFromDate = [APP_CONSTANTS.dateFormater stringFromDate:datePickerInvoice.date];
        [APP_CONSTANTS.dateFormater setDateFormat:@"MMM dd, yyyy"];
        lblStartDate.text = [APP_CONSTANTS.dateFormater stringFromDate:datePickerInvoice.date];
    }else{
        [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
        strToDate = [APP_CONSTANTS.dateFormater stringFromDate:datePickerInvoice.date];
        [APP_CONSTANTS.dateFormater setDateFormat:@"MMM dd, yyyy"];
        lblEndDate.text = [APP_CONSTANTS.dateFormater stringFromDate:datePickerInvoice.date];
    }
    
    if (![strFromDate isEqualToString:@""] && ![strToDate isEqualToString:@""]) {
        AppLog(@"Start and End Date selected");
        [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy-MM-dd"];
        NSDate *dateStart = [APP_CONSTANTS.dateFormater dateFromString:strFromDate];
        NSDate *dateEnd = [APP_CONSTANTS.dateFormater dateFromString:strToDate];
        
        NSComparisonResult result;
        //has three possible values: NSOrderedSame,NSOrderedDescending, NSOrderedAscending
        
        result = [dateStart compare:dateEnd]; // comparing two dates
        
        if(result==NSOrderedAscending){
            AppLog(@"start date is less");
        }else if(result==NSOrderedDescending){
            AppLog(@"end date is less");
            UDShowToastAlertWithTitle(@"End date can not be less than start date", 2);
            return;
        }else{
            AppLog(@"Both dates are same");
        }
        
        //intPageNo = 1;
        [arrInvoices removeAllObjects];
        [self getInvoices];
    }
}

- (void)btnSortClicked:(UITapGestureRecognizer *)gesture {
    
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil message:@"Sort by Due Date" preferredStyle:UIAlertControllerStyleActionSheet];
    [alert addAction:[UIAlertAction actionWithTitle:@"Ascending" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
        NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] initWithKey:@"due_date_sort" ascending:true];
        [arrInvoices sortUsingDescriptors:[NSArray arrayWithObject:sortDescriptor]];
        [tblViewInvoice reloadData];
    }]];
    [alert addAction:[UIAlertAction actionWithTitle:@"Descending" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
        NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] initWithKey:@"due_date_sort" ascending:false];
        [arrInvoices sortUsingDescriptors:[NSArray arrayWithObject:sortDescriptor]];
        [tblViewInvoice reloadData];
    }]];
    [alert addAction:[UIAlertAction actionWithTitle:@"Cancel" style:UIAlertActionStyleCancel handler:nil]];
    [self presentViewController:alert animated:true completion:nil];
}

#pragma mark - UITableView Delegate and Datasource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return arrInvoices.count;
}

- (InvoiceCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    static NSString *cellIdentifier = @"cell";
    InvoiceCell *cell = [tblViewInvoice dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    NSDictionary *dictInvoice = arrInvoices[indexPath.row];
    
    cell.lblInvoiceNo.text      = [dictInvoice safeValueeForKey:@"invoice_no"];
    cell.lblInvoiceDate.text    = [dictInvoice safeValueeForKey:@"invoice_date"];
    cell.lblDeliveryNo.text     = [dictInvoice safeValueeForKey:@"delivery_no"];
    cell.lblSaleOrderNo.text    = [dictInvoice safeValueeForKey:@"sale_order_no"];
    cell.lblAppOrderNo.text     = [dictInvoice safeValueeForKey:@"app_order_no"];
    cell.lblProductName.text    = [dictInvoice safeValueeForKey:@"product_name"];
    cell.lblInvoiceQty.text     = [dictInvoice safeValueeForKey:@"invoice_qty"];
    cell.lblDestination.text    = [dictInvoice safeValueeForKey:@"destination"];
    cell.lblTruckNo.text        = [dictInvoice safeValueeForKey:@"truck_no"];
    
    cell.btnPdf.accessibilityValue = [dictInvoice safeValueeForKey:@"dwd_url"];
    cell.btnPdf.hidden = ([[dictInvoice safeValueeForKey:@"dwd_url"] isEqualToString:@""]) ? true : false;
    [cell.btnPdf addTarget:self action:@selector(btnInvoiceClicked:) forControlEvents:UIControlEventTouchUpInside];
    
    return cell;
}

#pragma mark - Cell Event

-(void)btnInvoiceClicked:(UIButton*)sender {
    if (![sender.accessibilityValue isEqualToString:@""]) {
        [self performSegueWithIdentifier:@"invoiceToWebview" sender:sender.accessibilityValue];
    }else
        UDShowToastAlertWithTitle(@"Invoice not available", 2);
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"invoiceToWebview"]) {
        WebViewController *wvc = [segue destinationViewController];
        wvc.strWeblink = (NSString*)sender;
        wvc.strTitle = @"PDF";
    }
}

@end
