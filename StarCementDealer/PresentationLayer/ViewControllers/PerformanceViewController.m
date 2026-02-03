//
//  PerformanceViewController.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 02/02/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import "PerformanceViewController.h"
#import "ZFChart.h"
#import "SelfAppraisalProdWiseCell.h"

@interface PerformanceViewController ()<ZFGenericChartDataSource, ZFBarChartDelegate, UITableViewDelegate, UITableViewDataSource, UIPickerViewDelegate, UIPickerViewDataSource>{
    NSArray *arrTarAch;
    NSMutableArray *arrItemNames;
    __weak IBOutlet UIView *viewMonthYearCal;
    __weak IBOutlet UIPickerView *pickerViewMonthYear;
    NSMutableArray *arrMonth;
    NSMutableArray *arrYear;
    NSString *strSelectedMonthYear;
    IBOutlet UIView *viewHeader;
    IBOutlet UIView *viewFooter;
    __weak IBOutlet UILabel *lblTargetTotal;
    __weak IBOutlet UILabel *lblAchievementTotal;
    __weak IBOutlet UIView *viewBottom;
    UIView *viewList;
    UITableView *tblViewList;
}

@property (nonatomic, strong) ZFBarChart * barChart;
@property (nonatomic, assign) CGFloat height;

@end

@implementation PerformanceViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    
    NSDate *date = [NSDate date];
    [APP_CONSTANTS.dateFormater setDateFormat:@"MM"];
    NSString *month = [APP_CONSTANTS.dateFormater stringFromDate:date];
    
    strSelectedMonthYear = @"";
    
    arrMonth = [[NSMutableArray alloc] init];
    for (int monthNumber = 1; monthNumber <= 12; monthNumber++) {
        NSString *strMonthName = [[APP_CONSTANTS.dateFormater monthSymbols] objectAtIndex:(monthNumber-1)];
        [arrMonth addObject:strMonthName];
    }
    
    arrYear = [[NSMutableArray alloc] init];
    [APP_CONSTANTS.dateFormater setDateFormat:@"yyyy"];
    NSString *strYear = [APP_CONSTANTS.dateFormater stringFromDate:[NSDate date]];
    NSInteger intCurrentYear = [strYear integerValue];
    
    for (int i = 0; i < 20; i++) {
        int year = (int)intCurrentYear - i;
        [arrYear addObject:[NSString stringWithFormat:@"%d",year]];
    }
    
    [pickerViewMonthYear selectRow:[month integerValue] - 1 inComponent:0 animated:true];
    
    self.title = [NSString stringWithFormat:@"PERFORMANCE %@, %@",[[[APP_CONSTANTS.dateFormater shortMonthSymbols] objectAtIndex:[month intValue] - 1] uppercaseString], strYear];
    viewMonthYearCal.hidden = true;
    
    // Setup ZFBarChart
    self.barChart = [[ZFBarChart alloc] initWithFrame:CGRectMake(0, 40, SCREEN_WIDTH, self.view.frame.size.height - 205)];
    self.barChart.dataSource = self;
    self.barChart.delegate = self;
    self.barChart.topicLabel.text = @"";
    self.barChart.unit = @"Point";
    self.barChart.valueLabelPattern = kPopoverLabelPatternBlank;
    self.barChart.isShowYLineSeparate = YES;
    self.barChart.unitColor = ZFBlack;
    self.barChart.backgroundColor = ZFWhite;
    self.barChart.valueType = kValueTypeInteger;
    [self.barChart strokePath];
    [self.view addSubview:self.barChart];
    
    [self.view bringSubviewToFront:viewMonthYearCal];
    
    UIBarButtonItem *barButtonFilter = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"filter"] style:UIBarButtonItemStylePlain target:self action:@selector(btnFilterClicked:)];
    barButtonFilter.tintColor = [UIColor whiteColor];
    self.navigationItem.rightBarButtonItem = barButtonFilter;
}

-(void)loadData {
    
    arrItemNames = [[NSMutableArray alloc] init];
    
    APP_CONSTANTS.dateFormater.dateFormat = @"yyyy-MM-dd";
    NSDate *date = [NSDate date];
    NSString *strTodayDate = [APP_CONSTANTS.dateFormater stringFromDate:date];
    NSArray *arrMonthYear = [strTodayDate componentsSeparatedByString:@"-"];
    [self getTargetAchievementWithMonth:arrMonthYear[1] Year:arrMonthYear[0]];
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:true];
}

- (void)btnFilterClicked:(UIBarButtonItem *)sender {
    viewMonthYearCal.hidden = false;
}

- (IBAction)btnDoneClicked:(UIBarButtonItem *)sender {
    
    NSInteger intSelectedMonthRow = [pickerViewMonthYear selectedRowInComponent:0];
    intSelectedMonthRow += 1;
    
    NSString *strSelectedMonth = [NSString stringWithFormat:@"%02ld",(long)intSelectedMonthRow];
    NSString *strSelectedYear = arrYear[[pickerViewMonthYear selectedRowInComponent:1]];
    
    strSelectedMonthYear = [NSString stringWithFormat:@"%@-%@", strSelectedYear, strSelectedMonth];
    viewMonthYearCal.hidden = true;
    
    self.title = [NSString stringWithFormat:@"PERFORMANCE %@, %@",[[[APP_CONSTANTS.dateFormater shortMonthSymbols] objectAtIndex:intSelectedMonthRow - 1] uppercaseString], strSelectedYear];
    
    [self getTargetAchievementWithMonth:strSelectedMonth Year:strSelectedYear];
}

#pragma mark - Web Service

-(void)getTargetAchievementWithMonth:(NSString *)strMonth Year:(NSString *)strYear {
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"] forKey:@"customer_code"];
        [dict setValue:strYear forKey:@"year"];
        [dict setValue:strMonth forKey:@"month"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/item_wise_target_achievement_SAP_data.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                
                if ([dictResponse isKindOfClass:[NSArray class]]) {
                    UDShowToastAlertWithTitle(@"No data available", 2);
                    return;
                }
                
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    [arrItemNames removeAllObjects];
                    arrTarAch = [dictResponse safeValueeForKey:@"target_ach_data"];
                    for (NSDictionary *dict in arrTarAch) {
                        if ([[dict safeValueeForKey:@"itemname"] isEqualToString:@""]) {
                            [arrItemNames addObject:[dict safeValueeForKey:@"itemcode"]];
                        }else{
                            [arrItemNames addObject:[dict safeValueeForKey:@"itemname"]];
                        }
                    }
                    [self.barChart strokePath];
                    [tblViewList reloadData]; // reload table if open
                } else {
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
                }
            });
        }];
    } else {
        UDShowToastAlertWithTitle(kNoInternet, 2);
    }
}

#pragma mark - ZFGenericChartDataSource

- (NSArray *)valueArrayInGenericChart:(ZFGenericChart *)chart {
    
    NSMutableArray *arrTarget = [[NSMutableArray alloc]init];
    NSMutableArray *arrAchievements = [[NSMutableArray alloc]init];
    
    for (NSDictionary *dict in arrTarAch) {
        [arrTarget addObject:[NSString stringWithFormat:@"%.02f",[[dict safeValueeForKey:@"TGTQTY"] floatValue]]];
        [arrAchievements addObject:[NSString stringWithFormat:@"%.02f",[[dict safeValueeForKey:@"ACHQTY"] floatValue]]];
    }
    
    return @[arrTarget, arrAchievements];
}

- (NSArray *)nameArrayInGenericChart:(ZFGenericChart *)chart{
    return arrItemNames.copy;
}

- (NSArray *)colorArrayInGenericChart:(ZFGenericChart *)chart{
    return @[[UIColor colorWithRed:0.196 green:0.706 blue:0.898 alpha:1.00], [UIColor colorWithRed:0.290 green:0.592 blue:0.227 alpha:1.00]];
}

- (CGFloat)axisLineMaxValueInGenericChart:(ZFGenericChart *)chart{
    return 500;
}

- (NSUInteger)axisLineSectionCountInGenericChart:(ZFGenericChart *)chart{
    return 10;
}

#pragma mark - ZFBarChartDelegate

- (id)valueTextColorArrayInBarChart:(ZFBarChart *)barChart{
    return ZFBlack;
}

- (void)barChart:(ZFBarChart *)barChart didSelectBarAtGroupIndex:(NSInteger)groupIndex barIndex:(NSInteger)barIndex bar:(ZFBar *)bar popoverLabel:(ZFPopoverLabel *)popoverLabel{
    AppLog(@"Group Index : %ld Bar Index : %ld",(long)groupIndex,(long)barIndex);
    [self showListView];
}

#pragma mark - Orientation

- (void)viewWillTransitionToSize:(CGSize)size withTransitionCoordinator:(id <UIViewControllerTransitionCoordinator>)coordinator{
    
    if ([[UIApplication sharedApplication] statusBarOrientation] == UIInterfaceOrientationLandscapeLeft || [[UIApplication sharedApplication] statusBarOrientation] == UIInterfaceOrientationLandscapeRight){
        self.barChart.frame = CGRectMake(0, 0, size.width, size.height - NAVIGATIONBAR_HEIGHT * 0.5);
    } else {
        self.barChart.frame = CGRectMake(0, 0, size.width, size.height + NAVIGATIONBAR_HEIGHT * 0.5);
    }
}

#pragma mark - UIPickerView Delegate and DataSource

- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView {
    return 2;
}

- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component {
    return (component == 0) ? arrMonth.count : arrYear.count;
}

- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component {
    return (component == 0) ? arrMonth[row] : arrYear[row];
}

#pragma mark - UITableView Delegate and Datasource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return arrTarAch.count;
}

- (SelfAppraisalProdWiseCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *cellIdentifier = @"cell";
    SelfAppraisalProdWiseCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    NSDictionary *dict = arrTarAch[indexPath.row];
    
    // Configure cell text
    cell.lblMonth.numberOfLines = 0;
    cell.lblMonth.lineBreakMode = NSLineBreakByWordWrapping;
    cell.lblType.numberOfLines = 0;
    cell.lblType.lineBreakMode = NSLineBreakByWordWrapping;
    cell.lblTarget.numberOfLines = 0;
    cell.lblTarget.lineBreakMode = NSLineBreakByWordWrapping;
    cell.lblAchievement.numberOfLines = 0;
    cell.lblAchievement.lineBreakMode = NSLineBreakByWordWrapping;
    
    if ([[dict safeValueeForKey:@"itemname"] isEqualToString:@""]) {
        cell.lblMonth.text = [[dict safeValueeForKey:@"itemcode"] uppercaseString];
    } else {
        cell.lblMonth.text = [[dict safeValueeForKey:@"itemname"] uppercaseString];
    }
    
    cell.lblType.text = [dict safeValueeForKey:@"item_type"];
    cell.lblTarget.text = [NSString stringWithFormat:@"%.02f",[[dict safeValueeForKey:@"TGTQTY"] floatValue]];
    cell.lblAchievement.text = [NSString stringWithFormat:@"%.02f",[[dict safeValueeForKey:@"ACHQTY"] floatValue]];
    
    // Color formatting for total
    if ([cell.lblMonth.text.uppercaseString containsString:@"TOTAL QTY"]) {
        cell.backgroundColor = UIColorFromRGB(0xEC2427);
        cell.lblMonth.textColor = [UIColor whiteColor];
        cell.lblType.textColor = [UIColor whiteColor];
        cell.lblTarget.textColor = [UIColor whiteColor];
        cell.lblAchievement.textColor = [UIColor whiteColor];
    } else {
        cell.backgroundColor = [UIColor whiteColor];
        cell.lblMonth.textColor = UIColorFromRGB(0x7F7F7F);
        cell.lblType.textColor = UIColorFromRGB(0x7F7F7F);
        cell.lblTarget.textColor = UIColorFromRGB(0x7F7F7F);
        cell.lblAchievement.textColor = UIColorFromRGB(0x7F7F7F);
    }
    
    return cell;
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath{
    return UITableViewAutomaticDimension;
}

- (CGFloat)tableView:(UITableView *)tableView estimatedHeightForRowAtIndexPath:(NSIndexPath *)indexPath{
    return 50.0f;
}

- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section{
    return 50;
}

- (nullable UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section{
    return viewHeader;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    viewList.hidden = YES;
}

#pragma mark - Show List View

-(void)showListView {
    
    if (viewList == nil) {
        viewList = [[UIView alloc]initWithFrame:CGRectMake(0, 0, SCREEN_WIDTH, viewBottom.frame.origin.y)];
        viewList.backgroundColor = [UIColor orangeColor];
        
        tblViewList = [[UITableView alloc]initWithFrame:CGRectMake(0, 0, SCREEN_WIDTH, viewBottom.frame.origin.y) style:UITableViewStylePlain];
        if (@available(iOS 15, *)) {
            tblViewList.sectionHeaderTopPadding = 0;
        }
        tblViewList.delegate = self;
        tblViewList.dataSource = self;
        [tblViewList registerNib:[UINib nibWithNibName:@"SelfAppraisalProdWiseCell" bundle:nil] forCellReuseIdentifier:@"cell"];
        tblViewList.bounces = NO;
//        tblViewList.estimatedRowHeight = 50;
        tblViewList.rowHeight = UITableViewAutomaticDimension;
        
        [viewList addSubview:tblViewList];
        [self.view addSubview:viewList];
    } else {
        [tblViewList reloadData];
        viewList.hidden = NO;
    }
}

@end
