//
//  GraphPopupView.h
//

#import <UIKit/UIKit.h>

@interface GraphPopupView : UIView

@property (nonatomic, strong) UILabel *titleLabel;
@property (nonatomic, strong) UILabel *messageLabel;
@property (nonatomic, strong) UIView *graphContainer;

- (instancetype)initWithTitle:(NSString *)title
                      message:(NSString *)message
                        dates:(NSArray *)dates
                     orderQty:(NSArray *)orderQty
                     yAxisValues:(NSArray *)yAxisValues;
- (void)showInView:(UIView *)view;
- (void)dismiss;

@end
