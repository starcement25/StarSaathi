//
//  RateDealerViewController.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 15/01/25.
//  Copyright © 2025 Coral . All rights reserved.
//

#import "RateDealerViewController.h"
#import "HCSStarRatingView.h"

@interface RateDealerViewController (){
    __weak IBOutlet HCSStarRatingView *starRatingView;
    __weak IBOutlet UILabel *lblHello;
    __weak IBOutlet UITextView *txtViewReview;
    IBOutlet UIToolbar *toolbar;
}

@end

@implementation RateDealerViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

#pragma mark - Initialization Method

-(void)designView {
    self.title = @"Rate Us";
    
    UINavigationBar *bar = [self.navigationController navigationBar];
    [bar setBackgroundColor:UIColorFromRGB(0xEB2228)];
    
    if (@available(iOS 13.0, *)) {
        UIView *statusBar = [[UIView alloc]initWithFrame:[UIApplication sharedApplication].keyWindow.windowScene.statusBarManager.statusBarFrame] ;
        statusBar.backgroundColor = UIColorFromRGB(0xEB2228);
        [[UIApplication sharedApplication].keyWindow addSubview:statusBar];
    } else {
        // Fallback on earlier versions
    }
   
    UIBarButtonItem *btnBack = [[UIBarButtonItem alloc]initWithImage:[UIImage imageNamed:@"back_arrow"] style:UIBarButtonItemStylePlain target:self action:@selector(btnBackClicked:)];
    btnBack.tintColor = [UIColor whiteColor];
    self.navigationItem.leftBarButtonItem = btnBack;
    
    txtViewReview.inputAccessoryView = toolbar;
    
    // Add border similar to UITextField
    txtViewReview.layer.borderWidth = 1.0;
    txtViewReview.layer.borderColor = [[UIColor lightGrayColor] CGColor];
    txtViewReview.layer.cornerRadius = 5.0; // Adjust for rounded corners
        
    // Optional: Add padding similar to UITextField
    txtViewReview.textContainerInset = UIEdgeInsetsMake(8, 5, 8, 5);    
    
    //Star Rating View
    starRatingView.maximumValue = 5;
    starRatingView.minimumValue = 1;
    starRatingView.value = 1;
    starRatingView.tintColor = [UIColor redColor];
    starRatingView.emptyStarColor = UIColorFromRGB(0xD7D7D7);
    starRatingView.allowsHalfStars = YES;
    [starRatingView addTarget:self action:@selector(didChangeValue:) forControlEvents:UIControlEventValueChanged];
}

-(void)loadData {    
    lblHello.text = [NSString stringWithFormat:@"Hello,\nBased on your overall experience you had with our sales person %@,\nPlease rate us",[self.dictDealer safeValueeForKey:@"emp_name"]];
}

#pragma mark - IBAction's

-(void)btnBackClicked:(UIButton*)btn{
    [self.navigationController popViewControllerAnimated:true];
}

- (void)didChangeValue:(HCSStarRatingView *)sender {
    NSLog(@"Changed rating to %.1f", sender.value);
}

- (IBAction)btnSubmitClicked:(UIButton *)sender {
    
    NSString *strReviewText = [txtViewReview.text stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];
    if (strReviewText.length == 0) {
        UDShowToastAlertWithTitle(@"Please write your review", 2);
        return;
    }
    
    if (APP_DELEGATE.isServerReachable) {
        
        NSMutableDictionary *dict = [[NSMutableDictionary alloc]init];
        [dict setValue:[[NSUserDefaults standardUserDefaults] valueForKey:@"kUserLoginId"] forKey:@"customer_id"];
        [dict setValue:[self.dictDealer safeValueeForKey:@"emp_code"] forKey:@"emp_code"];
        [dict setValue:[self.dictDealer safeValueeForKey:@"visit_datetime"] forKey:@"visit_datetime"];
        [dict setValue:[NSString stringWithFormat:@"%.1f",starRatingView.value] forKey:@"survey_rating"];
        [dict setValue:txtViewReview.text forKey:@"remarks"];
        
        [SVProgressHUD showWithStatus:@"Loading..."];
        [JsonParser callAPIWithURL:@"https://starsaathi.com/SAP/save_dealer_sales_team_visit_details.php" parameters:dict completion:^(NSDictionary *dictResponse){
            dispatch_async(dispatch_get_main_queue(), ^(void) {
                [SVProgressHUD dismiss];
                if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                    
                    if (self.reloadAPIBlock) {
                        self.reloadAPIBlock();
                    }
                    
                    UIAlertController *alert = [UIAlertController alertControllerWithTitle:nil message:[dictResponse valueForKey:@"process_message"] preferredStyle:UIAlertControllerStyleAlert];
                    [alert addAction:[UIAlertAction actionWithTitle:@"OK" style:UIAlertActionStyleDefault handler:^(UIAlertAction * _Nonnull action) {
                        [self.navigationController popViewControllerAnimated:true];
                    }]];
                    [self presentViewController:alert animated:true completion:nil];
                }else
                    UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }];
    }else
        UDShowToastAlertWithTitle(kNoInternet, 2);
    
}

- (IBAction)btnDoneClicked:(id)sender {
    [self.view endEditing:true];
}

@end
