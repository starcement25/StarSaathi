#import "GraphPopupView.h"
#import "SimpleGraphView.h"

@interface GraphPopupView ()
@property (nonatomic, strong) UIView *backgroundView;
@property (nonatomic, strong) UIView *popupContainer;
@end

@implementation GraphPopupView

- (instancetype)initWithTitle:(NSString *)title
                      message:(NSString *)message
                        dates:(NSArray *)dates
                     orderQty:(NSArray *)orderQty
                  yAxisValues:(NSArray *)yAxisValues {

    self = [super initWithFrame:CGRectZero];
    if (self) {

        // Background
        _backgroundView = [[UIView alloc] init];
        _backgroundView.backgroundColor = [[UIColor whiteColor] colorWithAlphaComponent:0.5];
        _backgroundView.alpha = 0;
        _backgroundView.translatesAutoresizingMaskIntoConstraints = NO;
        [self addSubview:_backgroundView];

        // Popup container
        _popupContainer = [[UIView alloc] init];
        _popupContainer.backgroundColor = UIColor.whiteColor;
        _popupContainer.layer.cornerRadius = 12;
        _popupContainer.layer.shadowOpacity = 0.3;
        _popupContainer.layer.shadowRadius = 8;
        _popupContainer.translatesAutoresizingMaskIntoConstraints = NO;
        [self addSubview:_popupContainer];

        // Title
        _titleLabel = [[UILabel alloc] init];
        _titleLabel.text = title;
        _titleLabel.font = [UIFont boldSystemFontOfSize:18];
        _titleLabel.textAlignment = NSTextAlignmentCenter;
        _titleLabel.translatesAutoresizingMaskIntoConstraints = NO;
        [_popupContainer addSubview:_titleLabel];

        // Message
        _messageLabel = [[UILabel alloc] init];
        _messageLabel.text = message;
        _messageLabel.font = [UIFont systemFontOfSize:14];
        _messageLabel.textAlignment = NSTextAlignmentCenter;
        _messageLabel.numberOfLines = 0;
        _messageLabel.translatesAutoresizingMaskIntoConstraints = NO;
        [_popupContainer addSubview:_messageLabel];

        // Graph container
        _graphContainer = [[UIView alloc] init];
        _graphContainer.backgroundColor = UIColor.whiteColor;
        _graphContainer.translatesAutoresizingMaskIntoConstraints = NO;
        [_popupContainer addSubview:_graphContainer];

        // Close button
        UIButton *closeButton = [UIButton buttonWithType:UIButtonTypeSystem];
        [closeButton setTitle:@"Close" forState:UIControlStateNormal];
        closeButton.translatesAutoresizingMaskIntoConstraints = NO;
        [closeButton addTarget:self action:@selector(dismiss)
              forControlEvents:UIControlEventTouchUpInside];
        [_popupContainer addSubview:closeButton];

        // Constraints
        [NSLayoutConstraint activateConstraints:@[
            [_backgroundView.topAnchor constraintEqualToAnchor:self.topAnchor],
            [_backgroundView.bottomAnchor constraintEqualToAnchor:self.bottomAnchor],
            [_backgroundView.leadingAnchor constraintEqualToAnchor:self.leadingAnchor],
            [_backgroundView.trailingAnchor constraintEqualToAnchor:self.trailingAnchor],

            [_popupContainer.centerXAnchor constraintEqualToAnchor:self.centerXAnchor],
            [_popupContainer.centerYAnchor constraintEqualToAnchor:self.centerYAnchor],
            [_popupContainer.widthAnchor constraintEqualToConstant:300],

            [_titleLabel.topAnchor constraintEqualToAnchor:_popupContainer.topAnchor constant:16],
            [_titleLabel.leadingAnchor constraintEqualToAnchor:_popupContainer.leadingAnchor constant:16],
            [_titleLabel.trailingAnchor constraintEqualToAnchor:_popupContainer.trailingAnchor constant:-16],

            [_messageLabel.topAnchor constraintEqualToAnchor:_titleLabel.bottomAnchor constant:8],
            [_messageLabel.leadingAnchor constraintEqualToAnchor:_popupContainer.leadingAnchor constant:16],
            [_messageLabel.trailingAnchor constraintEqualToAnchor:_popupContainer.trailingAnchor constant:-16],

            [_graphContainer.topAnchor constraintEqualToAnchor:_messageLabel.bottomAnchor constant:16],
            [_graphContainer.leadingAnchor constraintEqualToAnchor:_popupContainer.leadingAnchor constant:16],
            [_graphContainer.trailingAnchor constraintEqualToAnchor:_popupContainer.trailingAnchor constant:-16],
            [_graphContainer.heightAnchor constraintEqualToConstant:160],

            [closeButton.topAnchor constraintEqualToAnchor:_graphContainer.bottomAnchor constant:16],
            [closeButton.centerXAnchor constraintEqualToAnchor:_popupContainer.centerXAnchor],
            [closeButton.bottomAnchor constraintEqualToAnchor:_popupContainer.bottomAnchor constant:-16]
        ]];

        // Graph
        SimpleGraphView *graph = [[SimpleGraphView alloc] initWithFrame:CGRectZero];
        graph.translatesAutoresizingMaskIntoConstraints = NO;
        graph.dates = dates;
        graph.orderQty = orderQty;
        graph.yAxisValues = yAxisValues;

        [_graphContainer addSubview:graph];

        [NSLayoutConstraint activateConstraints:@[
            [graph.topAnchor constraintEqualToAnchor:_graphContainer.topAnchor],
            [graph.bottomAnchor constraintEqualToAnchor:_graphContainer.bottomAnchor],
            [graph.leadingAnchor constraintEqualToAnchor:_graphContainer.leadingAnchor],
            [graph.trailingAnchor constraintEqualToAnchor:_graphContainer.trailingAnchor]
        ]];
    }
    return self;
}

- (void)showInView:(UIView *)view {
    self.frame = view.bounds;
    self.alpha = 0;
    [view addSubview:self];

    _popupContainer.transform = CGAffineTransformMakeScale(0.6, 0.6);

    [UIView animateWithDuration:0.25 animations:^{
        self.alpha = 1.0;
        self.backgroundView.alpha = 1.0;
        self.popupContainer.transform = CGAffineTransformIdentity;
    }];
}

- (void)dismiss {
    [UIView animateWithDuration:0.25 animations:^{
        self.alpha = 0;
        self.popupContainer.transform = CGAffineTransformMakeScale(0.7, 0.7);
    } completion:^(BOOL finished) {
        [self removeFromSuperview];
    }];
}

@end
