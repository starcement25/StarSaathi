//
//  TrackOrderCell.h
//  StarCementDealer
//
//  Created by Coral  on 17/07/18.
//  Copyright © 2018 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

@interface TrackOrderCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblChallanNo;
@property (weak, nonatomic) IBOutlet UILabel *lblChallanDate;
@property (weak, nonatomic) IBOutlet UILabel *lblChallanQty;
@property (weak, nonatomic) IBOutlet UILabel *lblTruckNo;
@property (weak, nonatomic) IBOutlet UILabel *lblDriverContact;
@property (weak, nonatomic) IBOutlet UIButton *btnFeedback;
@property (weak, nonatomic) IBOutlet UILabel *lblTransporterName;
@property (weak, nonatomic) IBOutlet UILabel *lblCHStatus;

/* 🔹 NEW ROW */
@property (weak, nonatomic) IBOutlet UILabel *lblErpOrderNo;

@end
