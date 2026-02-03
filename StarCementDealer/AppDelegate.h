#import <WebKit/WebKit.h>
#import <CoreLocation/CoreLocation.h>
#import <CoreLocation/CoreLocation.h>
#import <UserNotifications/UserNotifications.h>

@interface AppDelegate : UIResponder <UIApplicationDelegate,UNUserNotificationCenterDelegate,CLLocationManagerDelegate>

@property (strong, nonatomic) UIWindow          *window;
@property (assign, nonatomic) BOOL              isServerReachable;
@property (nonatomic, strong) CLLocationManager *locationManageger;
@property (strong, nonatomic) NSString          *strDeviceToken;
@property (strong, nonatomic) NSString          *strServerAppVersion;
@property (strong, nonatomic) NSString          *strDeviceAppVersion;
@property (strong, nonatomic) NSString          *strBranchWisePGRollOut;
@property (strong, nonatomic) NSString          *strDateForNewOrderEnq;


@end

