//
//  PopOrderListViewController.m
//  StarCementDealer
//

#import "PopOrderListViewController.h"
#import "PopOrderListCell.h"
#import "NSString+CharHandling.h"
#import "UIBarButtonItem+Badge.h"
#import "PopOrderConfirmationVC.h"
#import "PopOrderHistoryCell.h"
#import <SVProgressHUD/SVProgressHUD.h>

@interface PopOrderListViewController () <UICollectionViewDelegate, UICollectionViewDataSource, UICollectionViewDelegateFlowLayout, UITextFieldDelegate, UITableViewDelegate, UITableViewDataSource, UIPickerViewDelegate, UIPickerViewDataSource> {
    
    IBOutlet UIToolbar *toolbarDone;
    __weak IBOutlet UICollectionView *collViewPop;
    __weak IBOutlet UITableView *tblViewHistory;
    NSMutableArray *arrPopProd;
    NSMutableArray *arrSelectedProd;
    NSArray *arrOrderHistory;
    UIBarButtonItem *barButtonCart;
    IBOutletCollection(UIButton) NSArray *arrBtnTab;
    IBOutlet UIView *viewer;
    __weak IBOutlet UIImageView *imgViewProd;
    __weak IBOutlet UILabel *lblProdDesc;
    __weak IBOutlet UIButton *btnCheckout;
    
    __weak IBOutlet UIView *viewMonthYearCal;
    __weak IBOutlet UIPickerView *pickerViewMonthYear;
    NSMutableArray *arrMonth;
    NSMutableArray *arrYear;
    NSString *strSelectedMonthYear;
}

@end

@implementation PopOrderListViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillShow:) name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillHide:) name:UIKeyboardWillHideNotification object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(resetPopOrderList:) name:@"resetPopOrder" object:nil];
    
    [self.navigationController setNavigationBarHidden:NO animated:animated];
    self.navigationItem.hidesBackButton = YES;
}

-(void)viewWillDisappear:(BOOL)animated{
    [super viewWillDisappear:animated];
    
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillHideNotification object:nil];
}

- (void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self];
}

#pragma mark - Notification Handler

- (void) resetPopOrderList:(NSNotification *) notification {
    if ([[notification name] isEqualToString:@"resetPopOrder"]) {
        [arrPopProd removeAllObjects];
        [arrSelectedProd removeAllObjects];
        arrOrderHistory = @[];
        barButtonCart.badgeValue = @"0";
        [self getProductList];
    }
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
    NSInteger intCurrentYear = [[APP_CONSTANTS.dateFormater stringFromDate:date] integerValue];
    for (int i = 0; i < 20; i++) {
        [arrYear addObject:[NSString stringWithFormat:@"%d",(int)intCurrentYear - i]];
    }
    
    [pickerViewMonthYear selectRow:[month integerValue] - 1 inComponent:0 animated:true];
    
    viewMonthYearCal.hidden = true;
    viewMonthYearCal.translatesAutoresizingMaskIntoConstraints = NO;
    [self.view addSubview:viewMonthYearCal];
    [NSLayoutConstraint activateConstraints:@[
        [viewMonthYearCal.leadingAnchor constraintEqualToAnchor:self.view.leadingAnchor],
        [viewMonthYearCal.trailingAnchor constraintEqualToAnchor:self.view.trailingAnchor],
        [viewMonthYearCal.bottomAnchor constraintEqualToAnchor:self.view.bottomAnchor],
        [viewMonthYearCal.heightAnchor constraintEqualToConstant:206]
    ]];
    
    [self.view bringSubviewToFront:viewMonthYearCal];
    
    [tblViewHistory registerNib:[UINib nibWithNibName:@"PopOrderHistoryCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewHistory.separatorColor = [UIColor clearColor];
    
    UICollectionViewFlowLayout *layout = [[UICollectionViewFlowLayout alloc] init];
    layout.scrollDirection = UICollectionViewScrollDirectionVertical;
    layout.minimumLineSpacing = 0.0f;
    layout.minimumInteritemSpacing = 0.0f;
    layout.sectionInset = UIEdgeInsetsZero;
    collViewPop.collectionViewLayout = layout;
    [collViewPop registerNib:[UINib nibWithNibName:@"PopOrderListCell" bundle:nil] forCellWithReuseIdentifier:@"cell"];
    
    [self showRightBarButtonItems:101];
}

-(void)loadData {
    arrSelectedProd = [NSMutableArray array];
    arrPopProd = [NSMutableArray array];
    [self getProductList];
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(id)sender {
    [self.navigationController popViewControllerAnimated:true];
}

- (IBAction)btnCheckoutClicked:(UIButton *)sender {
    [self btnCartClicked:nil];
}

- (IBAction)resignKeyboard:(id)sender {
    [self.view endEditing:YES];
}

- (IBAction)btnTabClicked:(UIButton *)sender {
    for (UIButton *btnTab in arrBtnTab) {
        [btnTab setSelected:NO];
    }
    [sender setSelected:YES];
    collViewPop.hidden = (sender.tag != 101);
    btnCheckout.hidden = (sender.tag != 101);
    [self showRightBarButtonItems:(int)sender.tag];
    
    if (sender.tag == 101) viewMonthYearCal.hidden = true;
    
    [self.view endEditing:true];
    
    if (sender.tag != 101) {
        if (!arrOrderHistory.count) {
            [self getOrderHistory];
        } else{
            [tblViewHistory reloadData];
        }
    }
}

- (IBAction)btnCloseViewer:(UIButton *)sender {
    [sender.superview.superview removeFromSuperview];
}

- (IBAction)btnRotateImage:(UIButton *)sender {
    imgViewProd.transform = CGAffineTransformRotate(imgViewProd.transform, M_PI_2);
}

- (IBAction)btnDoneClicked:(UIBarButtonItem *)sender {
    NSInteger intSelectedMonthRow = [pickerViewMonthYear selectedRowInComponent:0] + 1;
    NSString *strSelectedMonth = [NSString stringWithFormat:@"%02ld",(long)intSelectedMonthRow];
    NSString *strSelectedYear = arrYear[[pickerViewMonthYear selectedRowInComponent:1]];
    strSelectedMonthYear = [NSString stringWithFormat:@"%@-%@", strSelectedYear, strSelectedMonth];
    viewMonthYearCal.hidden = true;
    [self getOrderHistory];
}

#pragma mark - Cart Action

- (void)btnCartClicked:(UIButton *)sender {
    [arrSelectedProd removeAllObjects];
    
    for (NSDictionary *dictProd in arrPopProd) {
        if (![[dictProd safeValueeForKey:@"input_value"] isEqualToString:@""]) {
            NSInteger minQty = [[dictProd safeValueeForKey:@"min_order_qty"] integerValue];
            NSInteger inputQty = [[dictProd safeValueeForKey:@"input_value"] integerValue];
            if (inputQty < minQty) {
                UDShowAlertWithTitle(kAppName, @"Please follow minimum qty (Pcs)");
                return;
            }
            [arrSelectedProd addObject:dictProd];
        }
    }
    
    if (arrSelectedProd.count == 0) {
        UDShowAlertWithTitle(kAppName, @"Please select atleast one product");
        return;
    }
    
    // Check if payment_gateway values are same
    NSMutableSet *values = [NSMutableSet set];
    for (NSDictionary *dict in arrSelectedProd) {
        NSString *value = dict[@"payment_gateway"];
        if (value.length) [values addObject:value];
    }
    
    if (values.count != 1) {
        UDShowAlertWithTitle(kAppName, @"Please select only one type of product.");
        return;
    }
    
    [self performSegueWithIdentifier:@"popProdListToConfirmation" sender:self];
}

-(void)barButtonItemFilterClicked:(UIBarButtonItem*)item{
    viewMonthYearCal.hidden = false;
}

#pragma mark - Helper Method

-(void)showRightBarButtonItems:(int)tag {
    UIImage *imgCart = [UIImage imageNamed:@"cart"];
    UIButton *btnCart = [UIButton buttonWithType:UIButtonTypeCustom];
    btnCart.frame = CGRectMake(0,0,imgCart.size.width,imgCart.size.height);
    [btnCart addTarget:self action:@selector(btnCartClicked:) forControlEvents:UIControlEventTouchDown];
    [btnCart setBackgroundImage:imgCart forState:UIControlStateNormal];
    
    barButtonCart = [[UIBarButtonItem alloc] initWithCustomView:btnCart];
    barButtonCart.tintColor = [UIColor whiteColor];
    barButtonCart.badgeValue = @"0";
    barButtonCart.badgeBGColor = [UIColor blackColor];
    
    UIBarButtonItem *barBtnFilterOrderHistory = [[UIBarButtonItem alloc] initWithImage:[UIImage imageNamed:@"filter"] style:UIBarButtonItemStyleDone target:self action:@selector(barButtonItemFilterClicked:)];
    barBtnFilterOrderHistory.tintColor = [UIColor whiteColor];
    
    self.navigationItem.rightBarButtonItems = (tag == 101) ? @[barButtonCart] : @[barButtonCart, barBtnFilterOrderHistory];
}

#pragma mark - Segue

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    if ([segue.identifier isEqualToString:@"popProdListToConfirmation"]) {
        PopOrderConfirmationVC *pocvc = segue.destinationViewController;
        pocvc.arrPickedItems = arrSelectedProd.copy;
    }
}

#pragma mark - Web Service (NSURLSession)

-(void)getProductList{
    if (!APP_DELEGATE.isServerReachable) {
        UDShowToastAlertWithTitle(kNoInternet, 2);
        return;
    }
    
    NSString *strDealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
    NSDictionary *params = @{@"customer_code": strDealerId,
                             @"user_type": [[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"]};
    
    NSError *error;
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:params options:0 error:&error];
    if (error) return;
    
    NSURL *url = [NSURL URLWithString:@"https://starsaathi.com/SAP/pop-product-data-download-v2.php"];
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:url];
    request.HTTPMethod = @"POST";
    [request setValue:@"application/json" forHTTPHeaderField:@"Content-Type"];
    request.HTTPBody = jsonData;
    
    [SVProgressHUD showWithStatus:@"Loading..."];
    
    NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithRequest:request
                                                                 completionHandler:^(NSData * _Nullable data,
                                                                                     NSURLResponse * _Nullable response,
                                                                                     NSError * _Nullable error) {
        dispatch_async(dispatch_get_main_queue(), ^{
            [SVProgressHUD dismiss];
        });
        if (error) {
            dispatch_async(dispatch_get_main_queue(), ^{
                UDShowToastAlertWithTitle(error.localizedDescription, 2);
            });
            return;
        }
        NSDictionary *dictResponse = [NSJSONSerialization JSONObjectWithData:data options:0 error:nil];
        if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
            NSArray *arrProd = [dictResponse safeValueeForKey:@"pop_product_date"];
            for (NSDictionary *dictProd in arrProd) {
                NSMutableDictionary *dictTemp = [dictProd mutableCopy];
                dictTemp[@"input_value"] = @"";
                [arrPopProd addObject:dictTemp];
            }
            dispatch_async(dispatch_get_main_queue(), ^{
                [collViewPop reloadData];
            });
        } else {
            dispatch_async(dispatch_get_main_queue(), ^{
                UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }
    }];
    
    [task resume];
}

-(void)getOrderHistory{
    if (!APP_DELEGATE.isServerReachable) {
        UDShowToastAlertWithTitle(kNoInternet, 2);
        return;
    }
    
    NSDictionary *params = @{@"customer_code": APP_CONSTANTS.strCustCode,
                             @"year_month": strSelectedMonthYear};
    
    NSError *error;
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:params options:0 error:&error];
    if (error) return;
    
    NSURL *url = [NSURL URLWithString:@"https://starsaathi.com/SAP/show-pop-order-list.php"];
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:url];
    request.HTTPMethod = @"POST";
    [request setValue:@"application/json" forHTTPHeaderField:@"Content-Type"];
    request.HTTPBody = jsonData;
    
    [SVProgressHUD showWithStatus:@"Loading..."];
    
    NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithRequest:request
                                                                 completionHandler:^(NSData * _Nullable data,
                                                                                     NSURLResponse * _Nullable response,
                                                                                     NSError * _Nullable error) {
        dispatch_async(dispatch_get_main_queue(), ^{
            [SVProgressHUD dismiss];
        });
        if (error) {
            dispatch_async(dispatch_get_main_queue(), ^{
                UDShowToastAlertWithTitle(error.localizedDescription, 2);
            });
            return;
        }
        NSDictionary *dictResponse = [NSJSONSerialization JSONObjectWithData:data options:0 error:nil];
        if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
            arrOrderHistory = [dictResponse safeValueeForKey:@"track_pop_order_data"];
        } else {
            arrOrderHistory = @[];
            dispatch_async(dispatch_get_main_queue(), ^{
                UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }
        dispatch_async(dispatch_get_main_queue(), ^{
            [tblViewHistory reloadData];
        });
    }];
    
    [task resume];
}

#pragma mark - UICollectionView Delegate and DataSource

- (NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section {
    return arrPopProd.count;
}

- (PopOrderListCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath {
    PopOrderListCell *cell = [collectionView dequeueReusableCellWithReuseIdentifier:@"cell" forIndexPath:indexPath];
    NSDictionary *dictProd = arrPopProd[indexPath.row];
    
    // Load image using NSURLSession
    cell.imgViewProd.image = [UIImage imageNamed:@"default-placeholder"];
    NSString *strImageURL = [NSString handleSpecialSymbol:[dictProd safeValueeForKey:@"prod_image"]];
    if (strImageURL.length) {
        NSURL *url = [NSURL URLWithString:strImageURL];
        NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithURL:url completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
            if (data) {
                UIImage *image = [UIImage imageWithData:data];
                if (image) {
                    dispatch_async(dispatch_get_main_queue(), ^{
                        PopOrderListCell *updateCell = (PopOrderListCell *)[collectionView cellForItemAtIndexPath:indexPath];
                        if (updateCell) updateCell.imgViewProd.image = image;
                    });
                }
            }
        }];
        [task resume];
    }
    
    cell.btnShowImg.tag = indexPath.item;
    [cell.btnShowImg addTarget:self action:@selector(btnShowImageClicked:) forControlEvents:UIControlEventTouchUpInside];
    
    cell.lblProdDesc.text = [dictProd safeValueeForKey:@"prod_desc"];
    cell.lblPricePerPiece.text = [NSString stringWithFormat:@"Price Per Piece ₹ %@",[dictProd safeValueeForKey:@"price_per_piece"]];
    cell.lblGst.text = [NSString stringWithFormat:@"GST %@ %%",[dictProd safeValueeForKey:@"GST_rate"]];
    cell.lblMinOrderQty.text = [NSString stringWithFormat:@"Min Order Qty %@",[dictProd safeValueeForKey:@"min_order_qty"]];
    cell.txtFieldQty.text = [dictProd safeValueeForKey:@"input_value"];
    cell.txtFieldQty.inputAccessoryView = toolbarDone;
    cell.txtFieldQty.delegate = self;
    cell.txtFieldQty.tag = indexPath.row;
    
    return cell;
}

- (CGSize)collectionView:(UICollectionView *)collectionView layout:(UICollectionViewLayout *)collectionViewLayout sizeForItemAtIndexPath:(NSIndexPath *)indexPath {
    return CGSizeMake((self.view.frame.size.width / 2) - 1 , 225);
}

#pragma mark - Cell Action

-(void)btnShowImageClicked:(UIButton*)btn {
    NSDictionary *dict = arrPopProd[btn.tag];
    
    viewer.frame = self.view.bounds;
    lblProdDesc.text = [dict safeValueeForKey:@"prod_desc"];
    imgViewProd.transform = CGAffineTransformIdentity;
    imgViewProd.image = [UIImage imageNamed:@"default-placeholder"];
    
    NSString *strImageURL = [NSString handleSpecialSymbol:[dict safeValueeForKey:@"prod_image"]];
    if (strImageURL.length) {
        NSURL *url = [NSURL URLWithString:strImageURL];
        NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithURL:url completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
            if (data) {
                UIImage *image = [UIImage imageWithData:data];
                if (image) {
                    dispatch_async(dispatch_get_main_queue(), ^{
                        imgViewProd.image = image;
                    });
                }
            }
        }];
        [task resume];
    }
    
    [self.view.window addSubview:viewer];
}

#pragma mark - UITableView Delegate and DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return arrOrderHistory.count;
}

- (PopOrderHistoryCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    PopOrderHistoryCell *cell = [tblViewHistory dequeueReusableCellWithIdentifier:@"cell" forIndexPath:indexPath];
    NSDictionary *dict = arrOrderHistory[indexPath.row];
    
    cell.imgViewProd.image = [UIImage imageNamed:@"default-placeholder"];
    NSString *strImageURL = [NSString handleSpecialSymbol:[dict safeValueeForKey:@"image"]];
    if (strImageURL.length) {
        NSURL *url = [NSURL URLWithString:strImageURL];
        NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithURL:url completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
            if (data) {
                UIImage *image = [UIImage imageWithData:data];
                if (image) {
                    dispatch_async(dispatch_get_main_queue(), ^{
                        PopOrderHistoryCell *updateCell = (PopOrderHistoryCell *)[tableView cellForRowAtIndexPath:indexPath];
                        if (updateCell) updateCell.imgViewProd.image = image;
                    });
                }
            }
        }];
        [task resume];
    }
    
    cell.lblMainOrderId.text = [dict safeValueeForKey:@"main_order_id"];
    cell.lblOrderId.text = [dict safeValueeForKey:@"order_id"];
    cell.lblDate.text = [dict safeValueeForKey:@"order_date"];
    cell.lblProd.text = [dict safeValueeForKey:@"prod_display_name"];
    cell.lblQty.text = [NSString stringWithFormat:@"%@ (Pcs)",[dict safeValueeForKey:@"qty"]];
    cell.lblTotalAmt.text = [NSString stringWithFormat:@"₹ %@.00",[dict safeValueeForKey:@"total_amount"]];
    cell.lblAddress.text = [dict safeValueeForKey:@"address"];
    cell.lblOrderStatus.text = [dict safeValueeForKey:@"order_status"];
    
    return cell;
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath {
    return 173.0f;
}

#pragma mark - Keyboard Method

- (void)keyboardWillShow:(NSNotification *)sender {
    CGFloat height = [[sender.userInfo objectForKey:UIKeyboardFrameEndUserInfoKey] CGRectValue].size.height - 44;
    NSTimeInterval duration = [[sender.userInfo objectForKey:UIKeyboardAnimationDurationUserInfoKey] doubleValue];
    UIViewAnimationOptions curveOption = [[sender.userInfo objectForKey:UIKeyboardAnimationCurveUserInfoKey] unsignedIntegerValue] << 16;
    
    [UIView animateKeyframesWithDuration:duration delay:0 options:UIViewAnimationOptionBeginFromCurrentState|curveOption animations:^{
        UIEdgeInsets edgeInsets = UIEdgeInsetsMake(0, 0, height, 0);
        collViewPop.contentInset = edgeInsets;
        collViewPop.scrollIndicatorInsets = edgeInsets;
    } completion:nil];
}

- (void)keyboardWillHide:(NSNotification *)sender {
    NSTimeInterval duration = [[sender.userInfo objectForKey:UIKeyboardAnimationDurationUserInfoKey] doubleValue];
    UIViewAnimationOptions curveOption = [[sender.userInfo objectForKey:UIKeyboardAnimationCurveUserInfoKey] unsignedIntegerValue] << 16;
    
    [UIView animateKeyframesWithDuration:duration delay:0 options:UIViewAnimationOptionBeginFromCurrentState|curveOption animations:^{
        UIEdgeInsets edgeInsets = UIEdgeInsetsZero;
        collViewPop.contentInset = edgeInsets;
        collViewPop.scrollIndicatorInsets = edgeInsets;
    } completion:nil];
}

#pragma mark - UITextFieldDelegate

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
    NSString *strInputValue = [textField.text stringByReplacingCharactersInRange:range withString:string];
    NSInteger index = textField.tag;
    
    NSMutableDictionary *dictProd = [arrPopProd objectAtIndex:index];
    dictProd[@"input_value"] = strInputValue;
    
    int count = 0;
    for (NSDictionary *dict in arrPopProd) {
        if (![[dict safeValueeForKey:@"input_value"] isEqualToString:@""]) count++;
    }
    barButtonCart.badgeValue = [NSString stringWithFormat:@"%d",count];
    return true;
}

#pragma mark - UIPickerView Delegate and DataSource

- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView { return 2; }

- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component {
    return component == 0 ? arrMonth.count : arrYear.count;
}

- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component {
    return component == 0 ? arrMonth[row] : arrYear[row];
}

@end
