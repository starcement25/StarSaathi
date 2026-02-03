//
//  TrackOrderInvoiceCell.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 26/10/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface TrackOrderInvoiceCell : UITableViewCell

@property (weak, nonatomic) IBOutlet UILabel *lblInvoiceNo;
@property (weak, nonatomic) IBOutlet UILabel *lblInvoiceDate;
@property (weak, nonatomic) IBOutlet UILabel *lblInvoiceQty;
@property (weak, nonatomic) IBOutlet UILabel *lblDriverNumber;
@property (weak, nonatomic) IBOutlet UILabel *lblTruckNo;
@property (weak, nonatomic) IBOutlet UILabel *lblDriverNo;
@property (weak, nonatomic) IBOutlet UILabel *lblDestination;

@end

NS_ASSUME_NONNULL_END
