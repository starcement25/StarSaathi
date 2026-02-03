//
//  AllocationConfirmationVC.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 20/06/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import "AllocationConfirmationVC.h"
#import "CustomTabButton.h"
#import "CustomerMasterBO.h"
#import "AllocationListWithSubDealerVC.h"
#import "AssignRssdViewController.h"

@interface AllocationConfirmationVC ()<UIPageViewControllerDataSource, UIPageViewControllerDelegate>{
    NSInteger intLastSelectedTabIndex;
    NSArray *arrTabTitles;
    NSDictionary *dictFiltered;
    NSMutableArray *tabButtons;
    __weak IBOutlet UIView *viewSubDealerNames;
    __weak IBOutlet UILabel *lblSubDealerNames;
}

@property (strong, nonatomic) UIPageViewController *pageViewController;
@property (strong, nonatomic) NSArray *viewControllersArray;
@property (strong, nonatomic) UIView *tabBarView;
@property (strong, nonatomic) UIScrollView *scrollView;

@end

@implementation AllocationConfirmationVC

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

- (void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
    
    NSString *strNames = @"";
    NSMutableArray *arrTitles = [[NSMutableArray alloc] init];
    int i = 1;
    for (CustomerMasterBO *CMBO in self.arrSelectedSubDealers) {
        [arrTitles addObject:CMBO.strCustomerName];
        strNames = [strNames stringByAppendingFormat:@"%d - %@\n",i,CMBO.strCustomerName];
        i++;
    }
    strNames = [strNames substringToIndex:[strNames length] - 2];
    lblSubDealerNames.text = strNames;
    
    arrTabTitles = arrTitles.copy;
    tabButtons = [[NSMutableArray alloc] init];
    
    [self getDispatchedOrderList];
    
    
    
//    dispatch_after(dispatch_time(DISPATCH_TIME_NOW, (int64_t)(0.5 * NSEC_PER_SEC)), dispatch_get_main_queue(), ^{
//        // Setup tab bar
//        [self setupTabBar];
//        [self setUpPageViewController];
//        
//        intLastSelectedTabIndex = 0;
//        [self selectTabAtIndex:0]; // Initially select the first tab
//    });
    
    
}

#pragma mark - Initialization Method

-(void)designView {
    
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"back_arrow"] style:UIBarButtonItemStylePlain target:self action:@selector(btnBackClicked:)];
    btnBack.tintColor = [UIColor whiteColor];
    self.navigationItem.leftBarButtonItem = btnBack;
}

-(void)loadData {
    APP_CONSTANTS.fltAvailableAllocationQty = 0.0;
}

#pragma mark - Web Service

-(void)getDispatchedOrderList{
    
    if (APP_DELEGATE.isServerReachable) {
        
        //NSString *strDealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[self.dictOrder safeValueeForKey:@"invoice_no"] forKey:@"invoice_no"];
        //[dict setValue:strDealerId forKey:@"customer_code"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/dispatched-order-list-download-invoicewise-v2.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
//                    NSString *strOrderId = [self.dictOrder safeValueeForKey:@"order_id"];
//                    
//                    // Define the key and value for filtering
//                    NSString *key = @"APPORDERNO";
//
//                    // Create an NSPredicate to filter the array
//                    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"%K == %@", key, strOrderId];
//
//                    // Filter the array
//                    NSArray *arrFiltered = [[dictResponse safeValueeForKey:@"dispatched_order_data"] filteredArrayUsingPredicate:predicate];
//
//                    // Output the filtered array
//                    AppLog(@"Filtered Array: %@", arrFiltered);
//                    
//                    if (arrFiltered.count) {
//                        dictFiltered = arrFiltered[0];
//                    }
                    
                    dictFiltered = [[dictResponse safeValueeForKey:@"dispatched_invoice_data"] objectAtIndex:0];
                    
                    // Setup tab bar
                    [self setupTabBar];
                    [self setUpPageViewController];
                    
                    intLastSelectedTabIndex = 0;
                    [self selectTabAtIndex:0]; // Initially select the first tab
                    
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
}

#pragma mark - IBAction's

-(void)btnBackClicked:(UIBarButtonItem*)button{
    //[self.navigationController popViewControllerAnimated:true];
    
    for (UIViewController *controller in self.navigationController.viewControllers) {
        //Do not forget to import DesiredViewController.h
        if ([controller isKindOfClass:[AssignRssdViewController class]]) {
            [self.navigationController popToViewController:controller animated:true];
            return;
        }
    }
}

- (void)tabButtonPressed:(CustomTabButton *)sender {
    NSInteger index = sender.tag;
    [self selectTabAtIndex:index];
    UIViewController *viewController = self.viewControllersArray[index];
    
    UIPageViewControllerNavigationDirection direction = (index > intLastSelectedTabIndex) ? UIPageViewControllerNavigationDirectionForward : UIPageViewControllerNavigationDirectionReverse;
    intLastSelectedTabIndex = index;
    [self.pageViewController setViewControllers:@[viewController] direction:direction animated:YES completion:nil];
    [self centerTabButton:sender];
}

#pragma mark - UIPageViewControllerDataSource

- (UIViewController *)pageViewController:(UIPageViewController *)pageViewController viewControllerBeforeViewController:(UIViewController *)viewController {
    NSUInteger index = [self.viewControllersArray indexOfObject:viewController];
    if (index == 0) {
        return nil;
    }
    index--;
    
    return self.viewControllersArray[index];
}

- (UIViewController *)pageViewController:(UIPageViewController *)pageViewController viewControllerAfterViewController:(UIViewController *)viewController {
    NSUInteger index = [self.viewControllersArray indexOfObject:viewController];
    index++;
    if (index == self.viewControllersArray.count) {
        return nil;
    }
    return self.viewControllersArray[index];
}

#pragma mark - UIPageViewControllerDelegate

- (void)pageViewController:(UIPageViewController *)pageViewController didFinishAnimating:(BOOL)finished previousViewControllers:(NSArray<UIViewController *> *)previousViewControllers transitionCompleted:(BOOL)completed {
    if (completed) {
        UIViewController *currentViewController = pageViewController.viewControllers.firstObject;
        NSInteger index = [self.viewControllersArray indexOfObject:currentViewController];
        [self selectTabAtIndex:index];
        [self centerTabButton:tabButtons[index]];
    }
} 

#pragma mark - Helper Method

- (void)setupTabBar {
    
    self.tabBarView = [[UIView alloc] initWithFrame:CGRectMake(0, viewSubDealerNames.frame.size.height + 20, self.view.bounds.size.width, 50)];
    self.tabBarView.backgroundColor = [UIColor whiteColor];
    
    self.scrollView = [[UIScrollView alloc] initWithFrame:CGRectMake(0, 0, self.view.bounds.size.width, 50)];
    
    int tag = 0;
    NSInteger xPos = 0;
    for (NSString *strTitles in arrTabTitles) {
        
        UIFont *font = [UIFont systemFontOfSize:14];
        NSDictionary *attributes = @{NSFontAttributeName: font};
        CGSize textSize = [strTitles sizeWithAttributes:attributes];
        
        CustomTabButton *btnTab = [[CustomTabButton alloc] initWithFrame:CGRectMake(xPos, 0, textSize.width + 20, 50)];
        [btnTab.titleLabel setFont:[UIFont systemFontOfSize:14]];
        [btnTab setTitle:strTitles forState:UIControlStateNormal];
        [btnTab setTitleColor:[UIColor blackColor] forState:UIControlStateNormal];
        [btnTab setTitleColor:UIColorFromRGB(0xEC2427) forState:UIControlStateSelected];
        [btnTab addTarget:self action:@selector(tabButtonPressed:) forControlEvents:UIControlEventTouchUpInside];
        btnTab.tag = tag;
        
        xPos += textSize.width + 20;
        tag++;
        
        [self.scrollView addSubview:btnTab];
        [tabButtons addObject:btnTab];
    }
    
    self.scrollView.contentSize = CGSizeMake(xPos, 50);
    [self.tabBarView addSubview:self.scrollView];
    [self.view addSubview:self.tabBarView];
    
}

-(void)setUpPageViewController {
    
    self.pageViewController = [[UIPageViewController alloc] initWithTransitionStyle:UIPageViewControllerTransitionStyleScroll navigationOrientation:UIPageViewControllerNavigationOrientationHorizontal options:nil];
    self.pageViewController.dataSource = self;
    self.pageViewController.delegate = self;
    
    NSMutableArray *arr = [[NSMutableArray alloc] init];
    int i = 0;
    for (NSString *strTitle in arrTabTitles) {
        AppLog(@"-->>%@",strTitle);
        AllocationListWithSubDealerVC *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"allocationListWithSubDealerVC"];
        vc.dictOrder = self.dictOrder;
        vc.dictOrderFiltered = dictFiltered;
        vc.arrSelectedSubDealers = self.arrSelectedSubDealers;
        vc.idx = i;
        i++;
        [arr addObject:vc];
    }
    
    self.viewControllersArray = arr.copy;
    
    [self.pageViewController setViewControllers:@[arr[0]] direction:UIPageViewControllerNavigationDirectionForward animated:YES completion:nil];
    self.pageViewController.view.frame = CGRectMake(0, CGRectGetMaxY(self.tabBarView.frame), self.view.bounds.size.width, self.view.bounds.size.height - CGRectGetMaxY(self.tabBarView.frame));
    [self addChildViewController:self.pageViewController];
    [self.view addSubview:self.pageViewController.view];
    [self.pageViewController didMoveToParentViewController:self];
    
}

- (void)selectTabAtIndex:(NSInteger)index {
    AppLog(@"-->>%ld",(long)index);
    for (CustomTabButton *button in tabButtons) {
        button.selected = (button.tag == index) ? true : false;
    }
}

- (void)centerTabButton:(CustomTabButton *)button {
    CGFloat buttonMidX = button.frame.origin.x + button.frame.size.width / 2;
    CGFloat offsetX = buttonMidX - self.self.scrollView.frame.size.width / 2;
    offsetX = MAX(0, MIN(self.scrollView.contentSize.width - self.scrollView.frame.size.width, offsetX));
    [self.scrollView setContentOffset:CGPointMake(offsetX, 0) animated:YES];
}

@end
