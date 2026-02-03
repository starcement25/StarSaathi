//
//  InvoiceCell.h
//  StarCementDealer
//
//  Created by Sanjeet Kumar on 18/02/21.
//  Copyright © 2021 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface InvoiceCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblInvoiceNo;
@property (weak, nonatomic) IBOutlet UILabel *lblInvoiceDate;
@property (weak, nonatomic) IBOutlet UILabel *lblDeliveryNo;
@property (weak, nonatomic) IBOutlet UILabel *lblSaleOrderNo;
@property (weak, nonatomic) IBOutlet UILabel *lblAppOrderNo;
@property (weak, nonatomic) IBOutlet UILabel *lblProductName;
@property (weak, nonatomic) IBOutlet UILabel *lblInvoiceQty;
@property (weak, nonatomic) IBOutlet UILabel *lblDestination;
@property (weak, nonatomic) IBOutlet UILabel *lblTruckNo;
@property (weak, nonatomic) IBOutlet UIButton *btnPdf;


@end

NS_ASSUME_NONNULL_END
