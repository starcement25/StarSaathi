//
//  PopOrderListCell.h
//  StarCementDealer
//
//  Created by Forcepower Infotech Pvt Ltd on 20/09/23.
//  Copyright © 2023 Coral . All rights reserved.
//

#import <WebKit/WebKit.h>

NS_ASSUME_NONNULL_BEGIN

@interface PopOrderListCell : UICollectionViewCell

@property (weak, nonatomic) IBOutlet UIView *viewBaseImage;
@property (weak, nonatomic) IBOutlet UIButton *btnShowImg;
@property (weak, nonatomic) IBOutlet UIImageView *imgViewProd;
@property (weak, nonatomic) IBOutlet UILabel *lblProdDesc;
@property (weak, nonatomic) IBOutlet UILabel *lblPricePerPiece;
@property (weak, nonatomic) IBOutlet UILabel *lblGst;
@property (weak, nonatomic) IBOutlet UILabel *lblMinOrderQty;
@property (weak, nonatomic) IBOutlet UITextField *txtFieldQty;

@end

NS_ASSUME_NONNULL_END
