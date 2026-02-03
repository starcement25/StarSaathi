//
//  SchemeViewController.m
//  StarCementDealer
//
//  Created by Apple on 12/02/19.
//  Copyright © 2019 Coral . All rights reserved.
//

#import "SchemeViewController.h"
#import "SchemeCell.h"
#import "BranchSchemePDFBO.h"
#import "WebViewController.h"
#import "GraphPopupView.h"

@interface SchemeViewController ()<UICollectionViewDelegate, UICollectionViewDataSource, UICollectionViewDelegateFlowLayout>{    
    __weak IBOutlet UICollectionView *collViewScheme;
    UIActivityIndicatorView *activityIndicator;
    NSMutableArray *arrBSPDF;
}

@end

@implementation SchemeViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
    [self setupActivityIndicator]; 
    // UDShowToastAlertWithTitle(@"Coming soon!", 2);
    // NSArray *dates = @[@"01", @"02", @"03", @"04", @"05", @"06"];
    // NSArray *qty   = @[@10, @30, @20, @50, @40, @60];
    // GraphPopupView *popup = [[GraphPopupView alloc] 
    // initWithTitle:@"Welcome!" 
    // message:@"Here is your performance graph."
    //          dates:dates
    //       orderQty:qty
    // ];
    // [popup showInView:self.view];
}
- (void)setupActivityIndicator {
    activityIndicator = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleLarge];
    activityIndicator.center = self.view.center;
    activityIndicator.hidesWhenStopped = YES;
    [self.view addSubview:activityIndicator];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Initialization Method

-(void)designView {
    [collViewScheme registerNib:[UINib nibWithNibName:@"SchemeCell" bundle:nil] forCellWithReuseIdentifier:@"cell"];
}

-(void)loadData {
    arrBSPDF = [[NSMutableArray alloc]init];
    // [self loadBranchSchemePDF];
    [self requestForSchemeList];
}

#pragma mark - Database Method

- (void)requestForSchemeList {
    [activityIndicator startAnimating];
    [[UIApplication sharedApplication] beginIgnoringInteractionEvents];
    // NSString *strDealerId = [[NSUserDefaults standardUserDefaults] valueForKey:@"kDealerId"];
    NSString *strDealerId = @"1000001497";
    NSString *urlString = [NSString stringWithFormat:@"https://starsaathi.com/SAP/dealer_schemes.php?status=active&dealer_id=%@", strDealerId];
    NSURL *url = [NSURL URLWithString:urlString];
    NSURLSessionDataTask *task = [[NSURLSession sharedSession]
        dataTaskWithURL:url
      completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
        if (error || data == nil) {
            [activityIndicator stopAnimating];
            [[UIApplication sharedApplication] endIgnoringInteractionEvents];
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showAlertWithTitle:@"Error" message:@"Failed to fetch data contact to ADMIN."];
            });
            return;
        }
        NSError *jsonError;
       NSDictionary *json = [NSJSONSerialization JSONObjectWithData:data
                                                             options:0
                                                               error:&jsonError];
        if (jsonError || [json[@"code"] intValue] != 200) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [activityIndicator stopAnimating];
                [[UIApplication sharedApplication] endIgnoringInteractionEvents];
                [self showAlertWithTitle:@"Error" message:@"Invalid data received."];
            });
            return;
        }

        NSDictionary *dataDict = json[@"data"];
        NSArray *schemes = dataDict[@"schemes"];
        if (![schemes isKindOfClass:[NSArray class]]) return;
        [arrBSPDF removeAllObjects];
        for (NSDictionary *scheme in schemes) {
            BranchSchemePDFBO *BSBO = [[BranchSchemePDFBO alloc] init];
            BSBO.strBranchCode  = dataDict[@"dealer_id"];
            BSBO.strPDFFileName = scheme[@"pdf_url"];
            BSBO.strAcedns      = scheme[@"pdf_url"];
            BSBO.schemeObject   = scheme;
            [arrBSPDF addObject:BSBO];
        }

        dispatch_async(dispatch_get_main_queue(), ^{
            [activityIndicator stopAnimating];
            [[UIApplication sharedApplication] endIgnoringInteractionEvents];
            [collViewScheme reloadData];
        });
    }];
    [task resume];
}
- (void)showAlertWithTitle:(NSString *)title message:(NSString *)message {
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:title
                                                                   message:message
                                                            preferredStyle:UIAlertControllerStyleAlert];

    UIAlertAction *ok = [UIAlertAction actionWithTitle:@"OK"
                                                 style:UIAlertActionStyleDefault
                                               handler:nil];

    [alert addAction:ok];
    [self presentViewController:alert animated:YES completion:nil];
}



-(void)loadBranchSchemePDF {
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    
    //    NSString *strEmpCode;
    //    if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
    //        strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
    //    }else{
    //        strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
    //    }
    //    if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"broker"]) {
    //        strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"selected_cust_code"];
    //    }else if ([[[NSUserDefaults standardUserDefaults] valueForKey:@"user_type"] isEqualToString:@"sub dealer"]){
    //        strEmpCode = [[NSUserDefaults standardUserDefaults] valueForKey:@"emp_code"];
    //    }else{
    //        NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    //        FMDatabase *db = [FMDatabase databaseWithPath:path];
    //        if ([db open]) {
    //            FMResultSet *s = [db executeQuery:@"SELECT customer_code FROM customer_master where cust_type = 'Dealer'"];
    //            while ([s next]) {
    //                strEmpCode = [s stringForColumn:@"customer_code"];
    //            }
    //            [db close];
    //        }
    //    }
    
    if ([APP_CONSTANTS.db open]) {
        NSString *strBranchCode = [APP_CONSTANTS.db stringForQuery:@"SELECT branch_code FROM customer_master where customer_code = ?",APP_CONSTANTS.strCustCode];
        AppLog(@"-->>%@",strBranchCode);
        
        if ([db open]) {
            //NSString *strQuery = @"select * from branch_schemes_PDF";
            NSString *strQuery = [NSString stringWithFormat:@"select * from branch_schemes_PDF where branch_code = '%@'",strBranchCode];
            FMResultSet *bs = [db executeQuery:strQuery];
            while ([bs next]) {
                //retrieve values for each record
                BranchSchemePDFBO *BSBO = [[BranchSchemePDFBO alloc]init];
                BSBO.strBranchCode      = [bs stringForColumn:@"branch_code"];
                BSBO.strPDFFileName     = [bs stringForColumn:@"PDF_file_name"];
                BSBO.strAcedns          = [bs stringForColumn:@"acedns"];
                [arrBSPDF addObject:BSBO];
            }
            [collViewScheme reloadData];
        }
        [APP_CONSTANTS.db close];
    }
    
}

#pragma mark - UICollectionView Delegate and DataSource

- (NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section{
    return arrBSPDF.count;
}

- (SchemeCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *cellIdentifier = @"cell";
    SchemeCell *cell = [collectionView dequeueReusableCellWithReuseIdentifier:cellIdentifier forIndexPath:indexPath];
    cell.lblScheme.text = [NSString stringWithFormat:@"SCHEME %ld",(long)indexPath.row + 1];
    return cell;
}

- (void)collectionView:(UICollectionView *)collectionView didSelectItemAtIndexPath:(NSIndexPath *)indexPath{
    WebViewController *wvc = [self.storyboard instantiateViewControllerWithIdentifier:@"webvc"];
    BranchSchemePDFBO *bspdf = arrBSPDF[indexPath.item];
    NSString *strPDFLink = [NSString stringWithFormat:@"https://starsaathi.com/SAP/%@",bspdf.strPDFFileName];
    wvc.strWeblink = strPDFLink;
    wvc.strCategory = @"scheme";
    wvc.schemeData = bspdf.schemeObject;
    wvc.title = [NSString stringWithFormat:@"SCHEME %ld",indexPath.item + 1];
    [self.navigationController pushViewController:wvc animated:YES];
}

- (CGSize)collectionView:(UICollectionView *)collectionView layout:(UICollectionViewLayout*)collectionViewLayout sizeForItemAtIndexPath:(NSIndexPath *)indexPath{
    float size = (APP_CONSTANTS.fltAppWidth - 17) / 2;
    return CGSizeMake(size, size);
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}


@end
