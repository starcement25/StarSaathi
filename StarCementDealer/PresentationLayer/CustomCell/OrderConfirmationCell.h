//
//  OrderConfirmationCell.h
//  StarCementDealer
//
//  Created by Coral  on 04/08/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@interface OrderConfirmationCell : UITableViewCell

@property (weak, nonatomic) IBOutlet UILabel *lblProductName;
@property (weak, nonatomic) IBOutlet UILabel *lblQuantity;

@end
