//
//  AlertHelper.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 06/12/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import <Foundation/Foundation.h>

NS_ASSUME_NONNULL_BEGIN

@interface AlertHelper : NSObject

+ (void)showAlertWithTitle:(NSString *)title
                   message:(NSString *)message
       fromViewController:(UIViewController *)viewController
                okHandler:(void (^)(void))okHandler;

+ (void)showAlertWithTitle:(NSString *)title
                   message:(NSString *)message
       fromViewController:(UIViewController *)viewController
                okHandler:(void (^)(void))okHandler
            cancelHandler:(void (^)(void))cancelHandler;


//Example for Ok Action

//[AlertHelper showAlertWithTitle:@"Alert Title"
//                        message:@"This is a reusable alert message."
//            fromViewController:self
//                     okHandler:^{
//                         // Task to perform on OK button click
//                         NSLog(@"OK button was clicked!");
//                     }];



//Example for Ok-Cancel Action

//[AlertHelper showAlertWithTitle:@"Alert"
//                        message:@"This alert has OK and Cancel."
//            fromViewController:self
//                     okHandler:^{
//                         NSLog(@"OK button clicked.");
//                     }
//                 cancelHandler:^{
//                     NSLog(@"Cancel button clicked.");
//                 }];



@end

NS_ASSUME_NONNULL_END
