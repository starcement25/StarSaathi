//
//  SelectDestinationViewController.h
//  StarCementDealer
//
//  Created by Apple on 28/09/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@protocol SelectDestinationControllerDelegate <NSObject>
@required
- (void)dataFromControllerCode:(NSString *)strCode DestinationName:(NSString*)strDestinationName FieldIdentifier:(NSString*)strIdentider;
@end

@interface SelectDestinationViewController : UIViewController
@property (nonatomic , strong)NSString *strIdentifier;
@property (nonatomic , strong)NSString *strFrieghtType;
@property (nonatomic, weak) id<SelectDestinationControllerDelegate> delegate;
@end
