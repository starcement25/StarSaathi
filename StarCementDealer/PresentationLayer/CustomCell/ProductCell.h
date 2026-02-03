//
//  ProductCell.h
//  StarCementDealer
//
//  Created by Coral  on 09/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@interface ProductCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblProductName;
@property (weak, nonatomic) IBOutlet UITextField *txtFieldUnit;
@end
