//
//  PerformanceGraphViewController.m
//  StarCementDealer
//
//  Created by Coral  on 02/08/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "PerformanceGraphViewController.h"
#import "ZFChart.h"
#import "SelfAppraisalProductWiseBO.h"
#import "SelfAppraisalCell.h"
#import "SubDealersViewController.h"

@interface PerformanceGraphViewController ()<ZFGenericChartDataSource, ZFBarChartDelegate, UITableViewDelegate, UITableViewDataSource, SubDealerControllerDelegate>{
    NSArray *arrSegment;
    __weak IBOutlet UIButton *btnFY18_19;
    __weak IBOutlet UIButton *btnFY17_18;
    NSMutableArray *arrSelfAppraisalProductWise;
    
    NSInteger intSelectedBtnIdentifier;
    
    IBOutlet UIView *viewHeader;
    IBOutlet UIView *viewFooter;
    __weak IBOutlet UILabel *lblTargetTotal;
    __weak IBOutlet UILabel *lblAchievementTotal;
    __weak IBOutlet UIView *viewBottom;
    
    UIView *viewList;
    UITableView *tblViewList;
    
    NSMutableDictionary *dictMonth;
    NSString *strCustCode;
    NSString *strClearFilterIdentifier;
    NSUserDefaults *defaults;
    
}
@property (nonatomic, strong) ZFBarChart * barChart;
@property (nonatomic, assign) CGFloat height;
@end

@implementation PerformanceGraphViewController


#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    defaults = [NSUserDefaults standardUserDefaults];
    [self setUp];
    [self designView];
    [self loadData];
}

-(void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView{
    
    if (@available(iOS 15, *)) {
        tblViewList.sectionHeaderTopPadding = 0;
    }
    
    arrSegment = @[btnFY18_19, btnFY17_18];
    
    AppLog(@"-->>%f",self.view.frame.size.height);
    
    //self.barChart = [[ZFBarChart alloc] initWithFrame:CGRectMake(0, 40, SCREEN_WIDTH, viewBottom.frame.origin.y - 40)];
    self.barChart = [[ZFBarChart alloc] initWithFrame:CGRectMake(0, 40, SCREEN_WIDTH, self.view.frame.size.height - 200)];
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
}

-(void)loadData{
    
//    if ([[defaults valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//        strCustCode = [defaults valueForKey:@"selected_cust_code"];
//        self.navigationItem.rightBarButtonItem.enabled = false;
//        self.navigationItem.rightBarButtonItem.tintColor = [UIColor clearColor];
//    }else{
//        AppLog(@"-->>CustCode : %@",[defaults valueForKey:@"emp_code"]);
//        strCustCode = [defaults valueForKey:@"emp_code"];
//    }
    
    //NSString *strCustCode;
//    if ([[defaults valueForKey:@"user_type"] isEqualToString:@"broker"]) {
//        strCustCode = [defaults valueForKey:@"selected_cust_code"];
//        self.navigationItem.rightBarButtonItem.enabled = false;
//        self.navigationItem.rightBarButtonItem.tintColor = [UIColor clearColor];
//    }else if ([[defaults valueForKey:@"user_type"] isEqualToString:@"sub dealer"] || [[defaults valueForKey:@"user_type"] isEqualToString:@"rssd"]){
//        strCustCode = [defaults valueForKey:@"emp_code"];
//    }else{
//        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
//        FMDatabase *db = [FMDatabase databaseWithPath:path];
//        if ([db open]) {
//            FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
//            while ([s next]) {
//                strCustCode = [s stringForColumn:@"customer_code"];
//            }
//            [db close];
//        }
//    }
    
    if (checkUserType(kbroker) == true) {
        strCustCode = [defaults valueForKey:@"selected_cust_code"];
        self.navigationItem.rightBarButtonItem.enabled = false;
        self.navigationItem.rightBarButtonItem.tintColor = [UIColor clearColor];
    }else if (checkUserType(kSubDealer) == true || checkUserType(kRssd) == true){
        strCustCode = [defaults valueForKey:@"emp_code"];
    }else{
        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
        FMDatabase *db = [FMDatabase databaseWithPath:path];
        if ([db open]) {
            FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
            while ([s next]) {
                strCustCode = [s stringForColumn:@"customer_code"];
            }
            [db close];
        }
    }
    
    arrSelfAppraisalProductWise = [[NSMutableArray alloc]init];
    
    dictMonth = [[NSMutableDictionary alloc]init];
    [dictMonth setValue:@"January"      forKey:@"01"];
    [dictMonth setValue:@"February"     forKey:@"02"];
    [dictMonth setValue:@"March"        forKey:@"03"];
    [dictMonth setValue:@"April"        forKey:@"04"];
    [dictMonth setValue:@"May"          forKey:@"05"];
    [dictMonth setValue:@"June"         forKey:@"06"];
    [dictMonth setValue:@"July"         forKey:@"07"];
    [dictMonth setValue:@"August"       forKey:@"08"];
    [dictMonth setValue:@"September"    forKey:@"09"];
    [dictMonth setValue:@"October"      forKey:@"10"];
    [dictMonth setValue:@"November"     forKey:@"11"];
    [dictMonth setValue:@"December"     forKey:@"12"];
    
    [self btnSegmentClicked:btnFY18_19];
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)btnSegmentClicked:(UIButton *)sender {
    for (UIButton *btnSegment in arrSegment) {
        [btnSegment setSelected:NO];
    }
    [sender setSelected:YES];
    intSelectedBtnIdentifier = sender.tag;
    if (!arrSelfAppraisalProductWise.count) {
        [self loadSelfAppraisalProductWise];
    }
    [self.barChart strokePath];
    [tblViewList reloadData];
}

- (IBAction)btnFilterClicked:(UIBarButtonItem *)sender {
    
    if ([strClearFilterIdentifier isEqualToString:@"clear_filter"]) {
       
        UIAlertController *alert = [UIAlertController alertControllerWithTitle:@"STAR SAATHI" message:@"Do you want to clear filter?" preferredStyle:UIAlertControllerStyleActionSheet];
        [alert addAction:[UIAlertAction actionWithTitle:@"CLEAR FILTER" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
            
            [self.navigationItem.rightBarButtonItem setBackgroundImage:[UIImage imageNamed:@"filter"] forState:UIControlStateNormal barMetrics:UIBarMetricsDefault];
            strClearFilterIdentifier = @"";
            strCustCode = [defaults valueForKey:@"emp_code"];
            [arrSelfAppraisalProductWise removeAllObjects];
            [self btnSegmentClicked:btnFY18_19];
            
        }]];
        
        [alert addAction:[UIAlertAction actionWithTitle:@"FILTER" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
            [self performSegueWithIdentifier:@"performanceToSubDealer" sender:self];
        }]];
        
        [alert addAction:[UIAlertAction actionWithTitle:@"CANCLE" style:UIAlertActionStyleCancel handler:nil]];
        
        [self presentViewController:alert animated:true completion:nil];
        
    }else{
       [self performSegueWithIdentifier:@"performanceToSubDealer" sender:self];
    }
    
    
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"performanceToSubDealer"]) {
        SubDealersViewController *sdvc = segue.destinationViewController;
        sdvc.delegate = self;
        sdvc.data = @"performance";
    }
}

#pragma mark - SubDealerController Delegate

- (void)dataFromControllerCustCode:(NSString *)strCustomerCode {
    AppLog(@"-->>%@",strCustomerCode);
    [self.navigationItem.rightBarButtonItem setBackgroundImage:[UIImage imageNamed:@"filter_fill"] forState:UIControlStateNormal barMetrics:UIBarMetricsDefault];
    strClearFilterIdentifier = @"clear_filter";
    strCustCode = strCustomerCode;
    [arrSelfAppraisalProductWise removeAllObjects];
    [self btnSegmentClicked:btnFY18_19];
}

#pragma mark - Setup

- (void)setUp{
    if ([[UIApplication sharedApplication] statusBarOrientation] == UIInterfaceOrientationLandscapeLeft || [[UIApplication sharedApplication] statusBarOrientation] == UIInterfaceOrientationLandscapeRight){
        _height = SCREEN_HEIGHT - NAVIGATIONBAR_HEIGHT * 0.5;
    }else{
        _height = SCREEN_HEIGHT - NAVIGATIONBAR_HEIGHT;
    }
}

#pragma mark - ZFGenericChartDataSource

- (NSArray *)valueArrayInGenericChart:(ZFGenericChart *)chart{
    
    NSMutableArray *arrTarget = [[NSMutableArray alloc]init];
    NSMutableArray *arrAchievements = [[NSMutableArray alloc]init];
    
    for (SelfAppraisalProductWiseBO *SAPW in arrSelfAppraisalProductWise) {
        if (intSelectedBtnIdentifier == 101) {
            [arrTarget addObject:SAPW.strTarget];
            [arrAchievements addObject:SAPW.strAchievement];
        }else{
            [arrTarget addObject:SAPW.strPrevYearTarget];
            [arrAchievements addObject:SAPW.strPrevYearAchievement];
        }
    }
    return @[arrTarget, arrAchievements];
}

- (NSArray *)nameArrayInGenericChart:(ZFGenericChart *)chart{
    return @[@"April", @"May", @"June", @"July", @"August", @"September",@"October", @"November", @"December", @"January", @"February", @"March"];
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

- (void)barChart:(ZFBarChart *)barChart didSelectPopoverLabelAtGroupIndex:(NSInteger)groupIndex labelIndex:(NSInteger)labelIndex popoverLabel:(ZFPopoverLabel *)popoverLabel{
    AppLog(@"第%ld组========第%ld个",(long)groupIndex,(long)labelIndex);
}

#pragma mark - Orientation

- (void)viewWillTransitionToSize:(CGSize)size withTransitionCoordinator:(id <UIViewControllerTransitionCoordinator>)coordinator NS_AVAILABLE_IOS(8_0){
    
    if ([[UIApplication sharedApplication] statusBarOrientation] == UIInterfaceOrientationLandscapeLeft || [[UIApplication sharedApplication] statusBarOrientation] == UIInterfaceOrientationLandscapeRight){
        self.barChart.frame = CGRectMake(0, 0, size.width, size.height - NAVIGATIONBAR_HEIGHT * 0.5);
    }else{
        self.barChart.frame = CGRectMake(0, 0, size.width, size.height + NAVIGATIONBAR_HEIGHT * 0.5);
    }
    //[self.barChart strokePath];
}

#pragma mark - Database Method

-(void)loadSelfAppraisalProductWise{
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        FMResultSet *s = [db executeQuery:@"SELECT * FROM self_appraisal_product_wise where emp_code = ?",strCustCode];
        while ([s next]) {
            //retrieve values for each record
            
            SelfAppraisalProductWiseBO *SAPWBO = [[SelfAppraisalProductWiseBO alloc]init];
            SAPWBO.strProdCode              = [s stringForColumn:@"prod_code"];
            SAPWBO.strProdDesc              = [s stringForColumn:@"prod_desc"];
            SAPWBO.strEmpCode               = [s stringForColumn:@"emp_code"];
            SAPWBO.strMonth                 = [s stringForColumn:@"month"];
            SAPWBO.strTarget                = [s stringForColumn:@"target"];
            SAPWBO.strAchievement           = [s stringForColumn:@"achievement"];
            SAPWBO.strPrevYearTarget        = [s stringForColumn:@"prev_y_target"];
            SAPWBO.strPrevYearAchievement   = [s stringForColumn:@"prev_y_achievement"];
            [arrSelfAppraisalProductWise addObject:SAPWBO];
        }
        [db close];
        
        NSArray *arrMonthOrder = @[@"04",@"05",@"06",@"07",@"08",@"09",@"10",@"11",@"12",@"01",@"02",@"03"];
        NSMutableArray *arrSelfAppraisalSorted = [[NSMutableArray alloc]init];
        for (NSString *strMonth in arrMonthOrder) {
            for (SelfAppraisalProductWiseBO *SAPW in arrSelfAppraisalProductWise) {
                if ([strMonth isEqualToString:SAPW.strMonth]) {
                    [arrSelfAppraisalSorted addObject:SAPW];
                    continue;
                }
            }
        }
        
        [arrSelfAppraisalProductWise removeAllObjects];
        arrSelfAppraisalProductWise = arrSelfAppraisalSorted;
    }
}

-(void)showListView {
    
    if (viewList==nil) {
        viewList = [[UIView alloc]initWithFrame:CGRectMake(0, 40, SCREEN_WIDTH, viewBottom.frame.origin.y - 40)];
        viewList.backgroundColor = [UIColor orangeColor];
        tblViewList = [[UITableView alloc]initWithFrame:CGRectMake(0, 0, SCREEN_WIDTH, viewBottom.frame.origin.y - 40) style:UITableViewStylePlain];
        tblViewList.delegate = self;
        tblViewList.dataSource = self;
        [tblViewList registerNib:[UINib nibWithNibName:@"SelfAppraisalCell" bundle:nil] forCellReuseIdentifier:@"cell"];
        tblViewList.bounces = NO;
        [viewList addSubview:tblViewList];
        [self.view addSubview:viewList];
    }else{
        viewList.hidden = NO;
    }
}

#pragma mark - UITableView Delegate and Datasource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return arrSelfAppraisalProductWise.count;
}

- (SelfAppraisalCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *cellIdentifier = @"cell";
    SelfAppraisalCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier forIndexPath:indexPath];
    
    SelfAppraisalProductWiseBO *SAPW = arrSelfAppraisalProductWise[indexPath.row];
    cell.lblMonth.text = [self getMonth:SAPW.strMonth];
    
    if (intSelectedBtnIdentifier == 101) {
        cell.lblTarget.text = SAPW.strTarget;
        cell.lblAchievement.text = SAPW.strAchievement;
    }else{
        cell.lblTarget.text = SAPW.strPrevYearTarget;
        cell.lblAchievement.text = SAPW.strPrevYearAchievement;
    }
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    viewList.hidden = YES;
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath{
    return 30.0f;
}

- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section{
    return 50;
}

- (CGFloat)tableView:(UITableView *)tableView heightForFooterInSection:(NSInteger)section{
    return 50;
}

- (nullable UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section{
    return viewHeader;
}

- (nullable UIView *)tableView:(UITableView *)tableView viewForFooterInSection:(NSInteger)section{
    
    double dblSumTarget = 0;
    double dblSumAchievement = 0;
    for (SelfAppraisalProductWiseBO *SAPW in arrSelfAppraisalProductWise) {
        if (intSelectedBtnIdentifier == 101) {
            dblSumTarget += [SAPW.strTarget doubleValue];
            dblSumAchievement += [SAPW.strAchievement doubleValue];
        }else{
            dblSumTarget += [SAPW.strPrevYearTarget doubleValue];
            dblSumAchievement += [SAPW.strPrevYearAchievement doubleValue];
        }
    }
    
    lblTargetTotal.text = [NSString stringWithFormat:@"%.2f",dblSumTarget];
    lblAchievementTotal.text = [NSString stringWithFormat:@"%.2f",dblSumAchievement];
    return viewFooter;
}

#pragma mark - Helper Method

-(NSString*)getMonth:(NSString*)string{
    return [dictMonth valueForKey:string];
}

@end
