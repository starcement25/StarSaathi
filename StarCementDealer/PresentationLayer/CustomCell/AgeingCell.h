//
//  AgeingCell.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 28/06/24.
//  Copyright © 2024 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface AgeingCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lblDocumentNo;
@property (weak, nonatomic) IBOutlet UILabel *lblDocumentDate;
@property (weak, nonatomic) IBOutlet UILabel *lblInvoiceAmt;

@end

NS_ASSUME_NONNULL_END
