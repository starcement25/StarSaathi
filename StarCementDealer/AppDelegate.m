//
//  AppDelegate.m
//  StarCementDealer
//

#import "AppDelegate.h"
#import "Reachability.h"
#import <SVProgressHUD/SVProgressHUD.h>
#import <UserNotifications/UserNotifications.h>
#import <CoreLocation/CoreLocation.h>

#define SYSTEM_VERSION_GREATERTHAN_OR_EQUALTO(v)  ([[[UIDevice currentDevice] systemVersion] compare:v options:NSNumericSearch] != NSOrderedAscending)

@interface AppDelegate () <CLLocationManagerDelegate, UNUserNotificationCenterDelegate>

@property (strong, nonatomic) Reachability *serverReachability;
@property (strong, nonatomic) CLLocationManager *locationManager;

@end

@implementation AppDelegate

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions {

    NSLog(@"Documents Directory: %@", [[[NSFileManager defaultManager] URLsForDirectory:NSDocumentDirectory inDomains:NSUserDomainMask] lastObject]);

    // Initialize CoreLocation
    self.locationManager = [[CLLocationManager alloc] init];
    self.locationManager.delegate = self;
    self.locationManager.desiredAccuracy = kCLLocationAccuracyNearestTenMeters;
    self.locationManager.distanceFilter = 10;
    [self.locationManager requestWhenInUseAuthorization];
    [self.locationManager startUpdatingLocation];

    // SVProgressHUD setup
    [SVProgressHUD setDefaultMaskType:SVProgressHUDMaskTypeBlack];

    // Configure Reachability
    [self configureReachability];

    // Check device dimensions
    [self checkDeviceValidation];

    // Register for push notifications
    [self registerForRemoteNotification];

    return YES;
}

#pragma mark - App Lifecycle

- (void)applicationWillResignActive:(UIApplication *)application {}
- (void)applicationDidEnterBackground:(UIApplication *)application {}
- (void)applicationDidBecomeActive:(UIApplication *)application {}

- (void)applicationWillEnterForeground:(UIApplication *)application {
    self.strDeviceAppVersion = [[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleShortVersionString"];
    float fltAppVersion = [self.strDeviceAppVersion floatValue];
    float fltServerVersion = [self.strServerAppVersion floatValue];
    if (fltServerVersion > fltAppVersion) {
        dispatch_async(dispatch_get_main_queue(), ^{
            UIAlertController *alertUpdate = [UIAlertController alertControllerWithTitle:@"Star Saathi"
                                                                                 message:@"Update Application"
                                                                          preferredStyle:UIAlertControllerStyleAlert];
            [alertUpdate addAction:[UIAlertAction actionWithTitle:@"OK" style:UIAlertActionStyleDefault handler:^(UIAlertAction *action){
                NSString *strUrl = [NSString stringWithFormat:@"https://itunes.apple.com/in/app/star-saathi/id%@?mt=8",@"6754075343"];
                [[UIApplication sharedApplication] openURL:[NSURL URLWithString:strUrl] options:@{} completionHandler:nil];
            }]];
            [self.window.rootViewController presentViewController:alertUpdate animated:YES completion:nil];
        });
    }
}

#pragma mark - CLLocationManagerDelegate

- (void)locationManager:(CLLocationManager *)manager didUpdateLocations:(NSArray<CLLocation *> *)locations {
    CLLocation *location = [locations lastObject];
    NSLog(@"Latitude: %f, Longitude: %f", location.coordinate.latitude, location.coordinate.longitude);
}

- (void)locationManager:(CLLocationManager *)manager didFailWithError:(NSError *)error {
    NSLog(@"Location error: %@", error.localizedDescription);
}

#pragma mark - Reachability

- (void)configureReachability {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:kReachabilityChangedNotification object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(reachabilityChanged:)
                                                 name:kReachabilityChangedNotification
                                               object:nil];
    
    self.serverReachability = [Reachability reachabilityForInternetConnection];
    [self.serverReachability startNotifier];
    [self updateInterfaceWithReachability:self.serverReachability];
}

- (void)reachabilityChanged:(NSNotification*)note {
    Reachability* curReach = [note object];
    NSParameterAssert([curReach isKindOfClass:[Reachability class]]);
    [self updateInterfaceWithReachability:curReach];
}

- (void)updateInterfaceWithReachability:(Reachability*)curReach {
    NetworkStatus netStatus = [curReach currentReachabilityStatus];
    switch (netStatus) {
        case NotReachable: self.isServerReachable = NO; break;
        case ReachableViaWWAN:
        case ReachableViaWiFi: self.isServerReachable = YES; break;
    }
}

#pragma mark - Device Bounds

- (void)checkDeviceValidation {
    APP_CONSTANTS.fltAppWidth   = [UIScreen mainScreen].bounds.size.width;
    APP_CONSTANTS.fltAppHeight  = [UIScreen mainScreen].bounds.size.height;
    APP_CONSTANTS.fltExtraWidth  = APP_CONSTANTS.fltAppWidth - 320;
    APP_CONSTANTS.fltExtraHeight = APP_CONSTANTS.fltAppHeight - 568;

    if (UI_USER_INTERFACE_IDIOM() == UIUserInterfaceIdiomPhone) {
        CGSize size = [UIScreen mainScreen].bounds.size;
        if (CGSizeEqualToSize(size, CGSizeMake(320, 568))) APP_CONSTANTS.deviceType = deviceTypeiPhone5;
        else if (CGSizeEqualToSize(size, CGSizeMake(375, 667))) APP_CONSTANTS.deviceType = deviceTypeiPhone6;
        else if (CGSizeEqualToSize(size, CGSizeMake(414, 736))) APP_CONSTANTS.deviceType = deviceTypeiPhone6Plus;
    } else {
        APP_CONSTANTS.deviceType = deviceTypeiPad;
    }
}

#pragma mark - Push Notifications

- (void)registerForRemoteNotification {
    self.strDeviceToken = [[NSUserDefaults standardUserDefaults] objectForKey:kDeviceToken];

    if (SYSTEM_VERSION_GREATERTHAN_OR_EQUALTO(@"10.0")) {
        UNUserNotificationCenter *center = [UNUserNotificationCenter currentNotificationCenter];
        center.delegate = self;
        [center requestAuthorizationWithOptions:(UNAuthorizationOptionSound | UNAuthorizationOptionAlert | UNAuthorizationOptionBadge)
                              completionHandler:^(BOOL granted, NSError * _Nullable error){
            if (!error) {
                dispatch_async(dispatch_get_main_queue(), ^{
                    [[UIApplication sharedApplication] registerForRemoteNotifications];
                });
            }
        }];
    } else {
        UIUserNotificationSettings *settings = [UIUserNotificationSettings settingsForTypes:(UIUserNotificationTypeSound | UIUserNotificationTypeAlert | UIUserNotificationTypeBadge) categories:nil];
        [[UIApplication sharedApplication] registerUserNotificationSettings:settings];
        [[UIApplication sharedApplication] registerForRemoteNotifications];
    }
}

- (void)application:(UIApplication *)application didRegisterUserNotificationSettings:(UIUserNotificationSettings *)notificationSettings {
    [application registerForRemoteNotifications];
}

- (void)application:(UIApplication *)application didRegisterForRemoteNotificationsWithDeviceToken:(NSData *)deviceToken {
    self.strDeviceToken = [self stringFromDeviceToken:deviceToken];
    NSLog(@"DeviceToken: %@", self.strDeviceToken);
    [[NSUserDefaults standardUserDefaults] setObject:self.strDeviceToken forKey:kDeviceToken];
    [[NSUserDefaults standardUserDefaults] synchronize];
}

- (void)application:(UIApplication *)application didFailToRegisterForRemoteNotificationsWithError:(NSError *)error {
    NSLog(@"Device register error: %@", error.debugDescription);
    [[NSUserDefaults standardUserDefaults] setObject:@"dummy_device_token" forKey:kDeviceToken];
    self.strDeviceToken = [[NSUserDefaults standardUserDefaults] objectForKey:kDeviceToken];
}

- (void)application:(UIApplication *)application didReceiveRemoteNotification:(NSDictionary *)userInfo {
    NSLog(@"Push Notification Information: %@", userInfo);
}

#pragma mark - UNUserNotificationCenterDelegate (iOS 10+)

- (void)userNotificationCenter:(UNUserNotificationCenter *)center
       willPresentNotification:(UNNotification *)notification
         withCompletionHandler:(void (^)(UNNotificationPresentationOptions options))completionHandler {
    NSLog(@"User Info = %@", notification.request.content.userInfo);
    completionHandler(UNNotificationPresentationOptionAlert | UNNotificationPresentationOptionBadge | UNNotificationPresentationOptionSound);
}

- (void)userNotificationCenter:(UNUserNotificationCenter *)center
didReceiveNotificationResponse:(UNNotificationResponse *)response
         withCompletionHandler:(void (^)(void))completionHandler {
    NSLog(@"User Info = %@", response.notification.request.content.userInfo);
    completionHandler();
}

#pragma mark - Helpers

- (NSString *)stringFromDeviceToken:(NSData *)deviceToken {
    NSUInteger length = deviceToken.length;
    if (length == 0) return nil;
    
    const unsigned char *buffer = deviceToken.bytes;
    NSMutableString *hexString  = [NSMutableString stringWithCapacity:(length * 2)];
    for (int i = 0; i < length; ++i) {
        [hexString appendFormat:@"%02x", buffer[i]];
    }
    return [hexString copy];
}

@end
