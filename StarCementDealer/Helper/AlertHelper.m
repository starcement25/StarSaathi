//
//  AlertHelper.m
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 06/12/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import "AlertHelper.h"

@implementation AlertHelper

+ (void)showAlertWithTitle:(NSString *)title
                   message:(NSString *)message
       fromViewController:(UIViewController *)viewController
                okHandler:(void (^)(void))okHandler {
    
    UIAlertController *alertController = [UIAlertController alertControllerWithTitle:title
                                                                             message:message
                                                                      preferredStyle:UIAlertControllerStyleAlert];

    // Add OK action with a handler
    UIAlertAction *okAction = [UIAlertAction actionWithTitle:@"OK"
                                                       style:UIAlertActionStyleDefault
                                                     handler:^(UIAlertAction *action) {
                                                         if (okHandler) {
                                                             okHandler();
                                                         }
                                                     }];
    [alertController addAction:okAction];

    // Present the alert
    [viewController presentViewController:alertController animated:YES completion:nil];
}

+ (void)showAlertWithTitle:(NSString *)title
                   message:(NSString *)message
       fromViewController:(UIViewController *)viewController
                okHandler:(void (^)(void))okHandler
            cancelHandler:(void (^)(void))cancelHandler {
    
    UIAlertController *alertController = [UIAlertController alertControllerWithTitle:title
                                                                             message:message
                                                                      preferredStyle:UIAlertControllerStyleAlert];

    UIAlertAction *okAction = [UIAlertAction actionWithTitle:@"OK"
                                                       style:UIAlertActionStyleDefault
                                                     handler:^(UIAlertAction *action) {
                                                         if (okHandler) {
                                                             okHandler();
                                                         }
                                                     }];
    UIAlertAction *cancelAction = [UIAlertAction actionWithTitle:@"Cancel"
                                                           style:UIAlertActionStyleCancel
                                                         handler:^(UIAlertAction *action) {
                                                             if (cancelHandler) {
                                                                 cancelHandler();
                                                             }
                                                         }];

    [alertController addAction:okAction];
    [alertController addAction:cancelAction];

    [viewController presentViewController:alertController animated:YES completion:nil];
}

@end
