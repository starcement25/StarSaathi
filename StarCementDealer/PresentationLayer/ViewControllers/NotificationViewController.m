//
//  NotificationViewController.m
//  StarCementDealer
//

#import "NotificationViewController.h"
#import "NotificationCell.h"
#import "NotificationBO.h"
#import "NotificationDetailViewController.h"
#import "WebViewController.h"

@interface NotificationViewController ()<UITableViewDelegate, UITableViewDataSource>{
    __weak IBOutlet UITableView *tblViewNotification;
    NSMutableArray *arrNotification;
    NSCache *imageCache;
}

@end

@implementation NotificationViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    arrNotification = [NSMutableArray array];
    imageCache = [[NSCache alloc] init];
    [self loadData];
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
}

#pragma mark - Initialization Method

-(void)designView{
    [tblViewNotification registerNib:[UINib nibWithNibName:@"NotificationCell" bundle:nil] forCellReuseIdentifier:@"cell"];
    tblViewNotification.tableFooterView = [UIView new];
}

-(void)loadData{
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        BOOL success = [db executeUpdate:@"DELETE FROM notification"];
        if (success) {
            [self getNotification:@""];
        }
        [db close];
    }
}

#pragma mark - UITableView Delegate & DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return arrNotification.count;
}

- (NotificationCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    NotificationCell *cell = [tableView dequeueReusableCellWithIdentifier:@"cell" forIndexPath:indexPath];
    NotificationBO *NBO = arrNotification[indexPath.row];
    
    cell.lblTitle.text = NBO.strNotificationTitle;
    cell.lblDescription.text = NBO.strNotificationMsg;
    
    // Image handling using NSURLSession and cache
    if (![NBO.strNotificationType isEqualToString:@"PDF"] && NBO.strNotificationImgLink.length > 0) {
        UIImage *cachedImage = [imageCache objectForKey:NBO.strNotificationImgLink];
        if (cachedImage) {
            cell.imgViewNotification.image = cachedImage;
        } else {
            cell.imgViewNotification.image = [UIImage imageNamed:@"user_placeholder"];
            NSURL *imageURL = [NSURL URLWithString:NBO.strNotificationImgLink];
            if (imageURL) {
                NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithURL:imageURL completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
                    if (data) {
                        UIImage *image = [UIImage imageWithData:data];
                        if (image) {
                            [imageCache setObject:image forKey:NBO.strNotificationImgLink];
                            dispatch_async(dispatch_get_main_queue(), ^{
                                NotificationCell *updateCell = [tableView cellForRowAtIndexPath:indexPath];
                                if (updateCell) {
                                    updateCell.imgViewNotification.image = image;
                                }
                            });
                        }
                    }
                }];
                [task resume];
            }
        }
    } else {
        cell.imgViewNotification.image = [UIImage imageNamed:@""];
    }
    
    cell.backgroundColor = ([NBO.strStatus isEqualToString:@"READ"]) ? UIColorFromRGB(0xDBDBDB) : [UIColor whiteColor];
    return cell;
}

-(CGFloat)tableView:(UITableView *)tableView estimatedHeightForRowAtIndexPath:(NSIndexPath *)indexPath{
    return UITableViewAutomaticDimension;
}

-(CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath{
    return UITableViewAutomaticDimension;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    NotificationBO *nbo = arrNotification[indexPath.row];
    NSString *strNotiStatus = [self getStatusWithNotiId:nbo.strNotificationId];
    if ([strNotiStatus isEqualToString:@"UNREAD"]) {
        [self updateReadStatusWithNotificationId:nbo.strNotificationId];
    }
    
    if ([nbo.strNotificationType isEqualToString:@"PDF"]) {
        [self performSegueWithIdentifier:@"notificationListToWebview" sender:self];
    } else {
        [self performSegueWithIdentifier:@"notificationListToDetails" sender:self];
    }
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - Web Service using NSURLSession

-(void)getNotification:(NSString*)strDatetime{
    if (!APP_DELEGATE.isServerReachable) {
        UDShowToastAlertWithTitle(kNoInternet, 2);
        return;
    }
    
    NSString *strBranchCode = @"";
    if ([APP_CONSTANTS.db open]) {
        strBranchCode = [APP_CONSTANTS.db stringForQuery:@"SELECT branch_code FROM customer_master where customer_code = ?",APP_CONSTANTS.strCustCode];
        [APP_CONSTANTS.db close];
    }
    
    NSMutableDictionary *dict = [NSMutableDictionary dictionary];
    [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
    [dict setValue:strBranchCode forKey:@"the_branch_code"];
    
    NSError *error;
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:dict options:0 error:&error];
    if (!jsonData) {
        AppLog(@"JSON error: %@", error);
        return;
    }
    
    NSURL *url = [NSURL URLWithString:@"https://starsaathi.com/SAP/acedns_show_notifications.php"];
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:url];
    request.HTTPMethod = @"POST";
    [request setValue:@"application/json" forHTTPHeaderField:@"Content-Type"];
    request.HTTPBody = jsonData;
    
    [SVProgressHUD showWithStatus:@"Loading..."];
    
    NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithRequest:request completionHandler:^(NSData * _Nullable data,
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
            [self insertNotification:[dictResponse safeValueeForKey:@"notification_data"] dateTime:@"curr_date_time"];
        } else {
            dispatch_async(dispatch_get_main_queue(), ^{
                UDShowToastAlertWithTitle([dictResponse valueForKey:@"process_message"], 2);
            });
        }
    }];
    
    [task resume];
}

-(void)updateReadStatusWithNotificationId:(NSString*)strNotificationId{
    if (!APP_DELEGATE.isServerReachable) {
        UDShowToastAlertWithTitle(kNoInternet, 2);
        return;
    }
    
    NSMutableDictionary *dict = [NSMutableDictionary dictionary];
    [dict setValue:APP_CONSTANTS.strCustCode forKey:@"the_id"];
    [dict setValue:strNotificationId forKey:@"noti_id"];
    
    NSError *error;
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:dict options:0 error:&error];
    if (!jsonData) return;
    
    NSURL *url = [NSURL URLWithString:@"https://starsaathi.com/SAP/make_notification_read_v3.php"];
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:url];
    request.HTTPMethod = @"POST";
    [request setValue:@"application/json" forHTTPHeaderField:@"Content-Type"];
    request.HTTPBody = jsonData;
    
    NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithRequest:request completionHandler:^(NSData * _Nullable data,
                                                                                                            NSURLResponse * _Nullable response,
                                                                                                            NSError * _Nullable error) {
        if (!error) {
            NSDictionary *dictResponse = [NSJSONSerialization JSONObjectWithData:data options:0 error:nil];
            if ([[dictResponse valueForKey:@"process_status"] isEqualToString:@"YES"]) {
                [self updateStatusWithNotiId:strNotificationId];
            }
        }
    }];
    
    [task resume];
}

#pragma mark - Database Methods (unchanged)

-(void)insertNotification:(NSArray*)arrNotification dateTime:(NSString*)strDatetime{
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        for (NSDictionary *dict in arrNotification) {
            BOOL success = [db executeUpdate:@"INSERT INTO notification(notification_id, notification_title, notification_message, notification_image_link, notification_datetime, status, notification_file_type) VALUES(?, ?, ?, ?, ?, ?, ?)",
                            [dict safeValueeForKey:@"nid"],
                            [dict safeValueeForKey:@"m_title"],
                            [dict safeValueeForKey:@"m_message"],
                            [dict safeValueeForKey:@"m_image_link"],
                            [dict safeValueeForKey:@"n_date_time"],
                            [dict safeValueeForKey:@"the_noti_sts"],
                            [dict safeValueeForKey:@"m_file_type"]];
            if (!success) AppLog(@"error = %@", [db lastErrorMessage]);
        }
        [db close];
        [self showNotification];
    }
}

-(void)showNotification {
    [arrNotification removeAllObjects];
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        FMResultSet *s = [db executeQuery:@"SELECT * FROM notification order by notification_datetime desc"];
        while ([s next]) {
            NotificationBO *NBO = [[NotificationBO alloc] init];
            NBO.strNotificationId = [s stringForColumn:@"notification_id"];
            NBO.strNotificationTitle = [s stringForColumn:@"notification_title"];
            NBO.strNotificationMsg = [s stringForColumn:@"notification_message"];
            NBO.strNotificationImgLink = [s stringForColumn:@"notification_image_link"];
            NBO.strNotificationDatetime = [s stringForColumn:@"notification_datetime"];
            NBO.strStatus = [s stringForColumn:@"status"];
            NBO.strNotificationType = [s stringForColumn:@"notification_file_type"];
            [arrNotification addObject:NBO];
        }
        [db close];
        [tblViewNotification reloadData];
    }
}

-(NSString*)getStatusWithNotiId:(NSString*)strNotificationId {
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        FMResultSet *s = [db executeQuery:@"SELECT status from notification where notification_id = ?",strNotificationId];
        while ([s next]) {
            [db close];
            return [s stringForColumn:@"status"];
        }
        [db close];
    }
    return @"";
}

-(void)updateStatusWithNotiId:(NSString*)strNotiId {
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        BOOL success = [db executeUpdate:@"UPDATE notification SET status = ? WHERE notification_id = ?",@"READ",strNotiId];
        if (success) {
            [arrNotification removeAllObjects];
            [self showNotification];
        }
        [db close];
        AppLog(@"%d",success);
    }
}

#pragma mark - Segue

-(void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender{
    NotificationBO *nbo = [arrNotification objectAtIndex:[tblViewNotification indexPathForSelectedRow].row];
    
    if ([segue.identifier isEqualToString:@"notificationListToDetails"]) {
        NotificationDetailViewController *ndvc = [segue destinationViewController];
        ndvc.NBO = nbo;
    } else {
        WebViewController *wvc = [segue destinationViewController];
        wvc.strWeblink = nbo.strNotificationImgLink;
    }
}

@end
