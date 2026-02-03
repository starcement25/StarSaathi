//
//  NotificationDetailViewController.m
//  StarCementDealer
//

#import "NotificationDetailViewController.h"

@interface NotificationDetailViewController () {
    __weak IBOutlet UIImageView   *imgNotification;
    __weak IBOutlet UILabel       *lblTitle;
    __weak IBOutlet UILabel       *lblSubtitle;
    __weak IBOutlet UIScrollView  *scrlView;
}

@end

@implementation NotificationDetailViewController

#pragma mark - View Life Cycle

- (void)viewDidLoad {
    [super viewDidLoad];
    [self designView];
    [self loadData];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
}

#pragma mark - Initialization Method

-(void)designView{
    AppLog(@"-->>%@", _NBO.strNotificationTitle);
}

-(void)loadData{
    [self setData];
}

-(CGFloat)heightForLabel:(UILabel *)label withText:(NSString *)text {
    NSAttributedString *attributedText = [[NSAttributedString alloc] initWithString:text attributes:@{NSFontAttributeName:label.font}];
    CGRect rect = [attributedText boundingRectWithSize:(CGSize){label.frame.size.width, CGFLOAT_MAX}
                                               options:NSStringDrawingUsesLineFragmentOrigin
                                               context:nil];
    return ceil(rect.size.height);
}

#pragma mark - IBAction's

- (IBAction)btnBackClicked:(UIBarButtonItem *)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - Helper Method

-(void)setData {
    lblTitle.text = _NBO.strNotificationTitle;
    lblSubtitle.text = _NBO.strNotificationMsg;

    NSURL *imageURL = [NSURL URLWithString:_NBO.strNotificationImgLink];
    if (!imageURL) return;

    // Async image download using NSURLSession
    NSURLSessionDataTask *task = [[NSURLSession sharedSession] dataTaskWithURL:imageURL completionHandler:^(NSData * _Nullable data, NSURLResponse * _Nullable response, NSError * _Nullable error) {
        if (error) {
            AppLog(@"Image load error: %@", error);
            return;
        }
        UIImage *image = [UIImage imageWithData:data];
        if (!image) return;

        dispatch_async(dispatch_get_main_queue(), ^{
            // Resize image view according to image aspect ratio
            CGFloat screenWidth = self.view.frame.size.width;
            CGFloat ratio = image.size.height / image.size.width;
            CGFloat height = screenWidth * ratio;
            CGFloat yAxis = imgNotification.frame.origin.y;

            imgNotification.frame = CGRectMake(imgNotification.frame.origin.x, yAxis, screenWidth, height);
            imgNotification.image = image;

            yAxis += imgNotification.frame.size.height + 15;

            CGFloat titleHeight = [self heightForLabel:lblTitle withText:self->_NBO.strNotificationTitle];
            lblTitle.frame = CGRectMake(lblTitle.frame.origin.x, yAxis, lblTitle.frame.size.width, titleHeight);

            yAxis += lblTitle.frame.size.height + 15;
            CGFloat subtitleHeight = [self heightForLabel:lblSubtitle withText:self->_NBO.strNotificationMsg];
            lblSubtitle.frame = CGRectMake(lblSubtitle.frame.origin.x, yAxis, lblSubtitle.frame.size.width, subtitleHeight);

            // Update scroll content size
            scrlView.contentSize = CGSizeMake(scrlView.frame.size.width, yAxis + subtitleHeight + 20);
        });
    }];
    [task resume];
}

-(void)updateData {
    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:kSqliteFile];
    FMDatabase *db = [FMDatabase databaseWithPath:path];
    if ([db open]) {
        BOOL success = [db executeUpdate:@"UPDATE notification SET status = ? WHERE notification_id = ?", @"READ", _NBO.strNotificationId];
        if (success) {
            AppLog(@"-->>Record Updated.");
        }
        [db close];
    }
}

@end
