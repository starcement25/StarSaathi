//
//  SplashViewController.m
//  StarCementDealer
//
//  Created by Coral  on 04/06/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import "SplashViewController.h"
#import "XMLParser.h"
#import "XMLReader.h"
#import "FMDB.h"
#import "BaseJSONParser.h"
#import "DeviceId.h"

@interface SplashViewController (){
    NSUserDefaults *defaults;
}
@property(strong, nonatomic)NSString *fileName;
@property(strong, nonatomic)NSString *filePath;

@end

@implementation SplashViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [self.navigationController setNavigationBarHidden:YES animated:animated];
    [self createAndCheckDatabase];
    [self designView];

    [self checkAppStatusFromServer];

    // [self loadData];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
}

- (void)checkAppStatusFromServer {
    NSURL *url = [NSURL URLWithString:@"https://starsaathi.com/SAP/saathi_app_api_dowmtime.php"];
    NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithURL:url
                                                             completionHandler:^(NSData * _Nullable data,
                                                                                 NSURLResponse * _Nullable response,
                                                                                 NSError * _Nullable error) {
        if (error || !data) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showAlert:@"Connection error. Please try again." link:nil];
            });
            return;
        }

        NSError *jsonError = nil;
        NSDictionary *json = [NSJSONSerialization JSONObjectWithData:data options:0 error:&jsonError];
        
        if (jsonError || ![json isKindOfClass:[NSDictionary class]]) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showAlert:@"Invalid server response." link:nil];
            });
            return;
        }

        NSString *appStatus = json[@"app_status"];
        NSString *bodyMessage = json[@"body_message"];
        NSString *isLinkAvailable = json[@"is_link_available"];
        NSString *bodyLink = json[@"body_link"];

        dispatch_async(dispatch_get_main_queue(), ^{
            if ([appStatus.lowercaseString isEqualToString:@"stop"]) {
                NSString *link = [isLinkAvailable isEqualToString:@"Y"] ? bodyLink : nil;
                [self showAlert:bodyMessage link:link];
            } else {
                [self checkAppVersionFromServer]; // continue normal flow
            }
        });
    }];
    [task resume];
}

- (void)checkAppVersionFromServer {
    NSURL *url = [NSURL URLWithString:@"https://starsaathi.com/SAP/show_latest_app_version_v2.php"];
    NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithURL:url
                                                             completionHandler:^(NSData * _Nullable data,
                                                                                 NSURLResponse * _Nullable response,
                                                                                 NSError * _Nullable error) {
        if (error || !data) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showAlert:@"Connection error. Please try again." link:nil];
            });
            return;
        }

        NSError *jsonError = nil;
        NSDictionary *json = [NSJSONSerialization JSONObjectWithData:data options:0 error:&jsonError];
        
        if (jsonError || ![json isKindOfClass:[NSDictionary class]]) {
            dispatch_async(dispatch_get_main_queue(), ^{
                [self showAlert:@"Invalid server response." link:nil];
            });
            return;
        }

        NSString *processStatus = json[@"process_status"];
        NSString *iosAppVersion = json[@"ios_app_version"];

        dispatch_async(dispatch_get_main_queue(), ^{
            if ([processStatus.lowercaseString isEqualToString:@"yes"]) {
                if([iosAppVersion.lowercaseString isEqualToString:@"6.2"]){
                    NSString *link = @"https://apps.apple.com/us/app/star-saathi/id6754075343";
                    NSString *bodyMessage = @"New version available. Please update your app first.";
                    [self showAlertUpdate:bodyMessage link:link];
                }else{
                    [self loadData]; 
                }
            } else {
                [self loadData]; // continue normal flow
            }
        });
    }];
    [task resume];
}

- (void)showAlertUpdate:(NSString *)message link:(NSString *)link {
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:@"Update"
                                                                   message:message
                                                            preferredStyle:UIAlertControllerStyleAlert];

    if (link != nil && link.length > 0) {
        UIAlertAction *openLink = [UIAlertAction actionWithTitle:@"Open Link"
                                                           style:UIAlertActionStyleDefault
                                                         handler:^(UIAlertAction * _Nonnull action) {
            [[UIApplication sharedApplication] openURL:[NSURL URLWithString:link] options:@{} completionHandler:nil];
        }];
        [alert addAction:openLink];
    }
    [self presentViewController:alert animated:YES completion:nil];
}

- (void)showAlert:(NSString *)message link:(NSString *)link {
    UIAlertController *alert = [UIAlertController alertControllerWithTitle:@"Maintenance"
                                                                   message:message
                                                            preferredStyle:UIAlertControllerStyleAlert];

    if (link != nil && link.length > 0) {
        UIAlertAction *openLink = [UIAlertAction actionWithTitle:@"Open Link"
                                                           style:UIAlertActionStyleDefault
                                                         handler:^(UIAlertAction * _Nonnull action) {
            [[UIApplication sharedApplication] openURL:[NSURL URLWithString:link] options:@{} completionHandler:nil];
        }];
        [alert addAction:openLink];
    }
    [self presentViewController:alert animated:YES completion:nil];
}

#pragma mark - Initialization Method

-(void)designView{
    UINavigationBar *bar = [self.navigationController navigationBar];
    [bar setBackgroundColor:UIColorFromRGB(0xEB2228)];
    
    CGFloat statusBarHeight;
    if (@available(iOS 11.0, *)) {
        statusBarHeight = UIApplication.sharedApplication.keyWindow.safeAreaInsets.top;
    } else {
        statusBarHeight = [UIApplication sharedApplication].statusBarFrame.size.height;
    }
    
    if (@available(iOS 13.0, *)) {
        UIView *statusBar = [[UIView alloc]initWithFrame:CGRectMake(0, 0, APP_CONSTANTS.fltAppWidth, statusBarHeight)] ;
        statusBar.backgroundColor = UIColorFromRGB(0xEB2228);
        [[UIApplication sharedApplication].keyWindow addSubview:statusBar];
    }
}

-(void)loadData{
    AppLog(@"-->>>%@",[DeviceId GetDeviceID]); 
    defaults = [NSUserDefaults standardUserDefaults];
    BOOL isTableStructureDownloaded = [defaults boolForKey:@"tablestructure"];
    
    if (isTableStructureDownloaded) {
        BOOL flag = [[NSUserDefaults standardUserDefaults] boolForKey:@"datadownloaded"];
        if (flag) {
            [self performSegueWithIdentifier:@"splashToDashboard" sender:self];
        }else{
            [self performSegueWithIdentifier:@"splashToLogin" sender:self];
        }
    }else{
        [self checkNicknameAndDownloadTableStructure];
    }
}

#pragma mark - Web Service Method

-(void)checkNicknameAndDownloadTableStructure{
    
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"https://starsaathi.com/SAP/user-details-incremental-6.0.0.php?nick_name=%@&mode=%@", @"START", @"SETUP"]];
    
    [XMLParser downloadDataFromURL:url withCompletionHandler:^(NSData *data){
        if (data != nil) {
            NSError *parseError = nil;
            NSDictionary *xmlDictionary = [XMLReader dictionaryForXMLData:data error:&parseError];
            AppLog(@"-->>%@",xmlDictionary);
        }
        
        NSURL *urlTblStructure = [NSURL URLWithString:[NSString stringWithFormat:@"https://starsaathi.com/SAP/table-structure-details-6.0.2.php?nick_name=%@&mode=%@&device_id=%@emp_code=%@", @"START", @"INSTALL", [DeviceId GetDeviceID], @""]];
        
        [XMLParser downloadDataFromURL:urlTblStructure withCompletionHandler:^(NSData *data){
            if (data != nil) {
                NSError *parseError = nil;
                NSDictionary *xmlDictionary = [XMLReader dictionaryForXMLData:data error:&parseError];
                AppLog(@"%@",xmlDictionary);
                NSDictionary *dict = [XMLReader dictionaryForXMLData:data
                                                             options:XMLReaderOptionsProcessNamespaces
                                                               error:&parseError];
                
                NSArray *arr = dict[@"recordset"][@"data"];
                AppLog(@"%@",arr);
                
                NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
                FMDatabase *db = [FMDatabase databaseWithPath:path];
                
                for (NSDictionary *dictt in arr) {
                    AppLog(@"-->>%@",dictt[@"table_structure"][@"text"]);
                    NSString *strQuery = dictt[@"table_structure"][@"text"];
                    if ([db open]) {
                        BOOL success = [db executeStatements:strQuery];
                        AppLog(@"-->>%d",success);
                    }
                }
                
                [defaults setBool:YES forKey:@"tablestructure"];
                [defaults synchronize];
                [self performSegueWithIdentifier:@"splashToLogin" sender:self];
            }
        }];
    }];
}

#pragma mark - Database Method

- (void)createAndCheckDatabase {
    
    BOOL success;
    NSError *error;
    
    NSFileManager *fileManager = [NSFileManager defaultManager];
    
    NSString *documentsDirectory = [NSHomeDirectory() stringByAppendingPathComponent:@"Documents"];
    NSString *filePath = [documentsDirectory stringByAppendingPathComponent:@"StarSaathi.db"];
    
    success = [fileManager fileExistsAtPath:filePath];
    if (!success) {
        NSString *path = [[NSBundle mainBundle] pathForResource:@"StarSaathi" ofType:@"db"];
        success = [fileManager copyItemAtPath:path toPath:filePath error:&error];
        
    }
}

@end
