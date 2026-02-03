#import "SimpleGraphView.h"

@implementation SimpleGraphView

- (instancetype)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        self.backgroundColor = UIColor.whiteColor;
    }
    return self;
}

- (void)drawRect:(CGRect)rect {

    if (self.dates.count == 0 || self.orderQty.count == 0) {
        NSLog(@"Graph: No data to draw");
        return;
    }

    NSLog(@"Graph drawRect: dates=%lu qty=%lu yAxis=%lu",
          (unsigned long)self.dates.count,
          (unsigned long)self.orderQty.count,
          (unsigned long)self.yAxisValues.count);

    CGContextRef context = UIGraphicsGetCurrentContext();

    CGFloat width  = rect.size.width;
    CGFloat height = rect.size.height;

    CGFloat paddingLeft   = 40;
    CGFloat paddingBottom = 35;
    CGFloat paddingTop    = 20;

    // ===============================
    // WHITE BACKGROUND
    // ===============================
    [[UIColor whiteColor] setFill];
    CGContextFillRect(context, rect);

    // ===============================
    // SCOREBOARD BORDER
    // ===============================
    CGContextSetStrokeColorWithColor(context, UIColor.blackColor.CGColor);
    CGContextSetLineWidth(context, 2);
    CGContextStrokeRect(context, CGRectMake(0, 0, width, height));

    // ===============================
    // MAX VALUE (FROM yAxisValues IF EXISTS)
    // ===============================
    CGFloat maxValue = 0;

    if (self.yAxisValues.count > 0) {
        NSNumber *lastValue = [self.yAxisValues lastObject];
        maxValue = [lastValue floatValue];
    } else {
        for (NSNumber *num in self.orderQty) {
            CGFloat value = [num floatValue];
            if (value > maxValue) maxValue = value;
        }
    }

    if (maxValue <= 0) maxValue = 10;

    NSInteger horizontalLines = 5;

    // ===============================
    // HORIZONTAL GRID + Y LABELS
    // ===============================
    for (int i = 0; i <= horizontalLines; i++) {

        CGFloat y = paddingTop + (height - paddingBottom - paddingTop) * i / horizontalLines;

        CGContextSetStrokeColorWithColor(context, UIColor.blackColor.CGColor);
        CGContextSetLineWidth(context, 0.5);

        CGContextMoveToPoint(context, paddingLeft, y);
        CGContextAddLineToPoint(context, width - 10, y);
        CGContextStrokePath(context);

        CGFloat val = maxValue - (maxValue / horizontalLines) * i;
        NSString *qtyText = [NSString stringWithFormat:@"%.0f", val];

        NSDictionary *attrs = @{
            NSFontAttributeName : [UIFont systemFontOfSize:10],
            NSForegroundColorAttributeName : UIColor.blackColor
        };

        CGSize size = [qtyText sizeWithAttributes:attrs];
        [qtyText drawAtPoint:CGPointMake(5, y - size.height/2)
              withAttributes:attrs];
    }

    // ===============================
    // SAFE X STEP CALCULATION
    // ===============================
    CGFloat availableWidth = width - paddingLeft - 10;
    CGFloat stepX;

    if (self.dates.count > 1) {
        stepX = availableWidth / (self.dates.count - 1);
    } else {
        stepX = availableWidth / 2;
    }

    // ===============================
    // VERTICAL GRID
    // ===============================
    for (int i = 0; i < self.dates.count; i++) {

        CGFloat x = paddingLeft + (stepX * i);

        CGContextSetStrokeColorWithColor(context, UIColor.blackColor.CGColor);
        CGContextSetLineWidth(context, 0.5);

        CGContextMoveToPoint(context, x, paddingTop);
        CGContextAddLineToPoint(context, x, height - paddingBottom);
        CGContextStrokePath(context);
    }

    // ===============================
    // DRAW LINE GRAPH
    // ===============================
    CGContextSetStrokeColorWithColor(context, UIColor.blackColor.CGColor);
    CGContextSetLineWidth(context, 2.0);

    for (int i = 0; i < self.orderQty.count; i++) {

        CGFloat value = [self.orderQty[i] floatValue];

        CGFloat x = paddingLeft + (stepX * i);
        CGFloat y = (height - paddingBottom) -
                    ((value / maxValue) * (height - paddingBottom - paddingTop));

        if (i == 0) {
            CGContextMoveToPoint(context, x, y);
        } else {
            CGContextAddLineToPoint(context, x, y);
        }
    }

    CGContextStrokePath(context);

    // ===============================
    // DRAW POINTS + VALUES
    // ===============================
    for (int i = 0; i < self.orderQty.count; i++) {

        CGFloat value = [self.orderQty[i] floatValue];

        CGFloat x = paddingLeft + (stepX * i);
        CGFloat y = (height - paddingBottom) -
                    ((value / maxValue) * (height - paddingBottom - paddingTop));

        CGContextSetFillColorWithColor(context, UIColor.blackColor.CGColor);
        CGContextFillEllipseInRect(context, CGRectMake(x - 3, y - 3, 6, 6));

        NSString *valText = [NSString stringWithFormat:@"%.0f", value];

        NSDictionary *attr = @{
            NSFontAttributeName : [UIFont boldSystemFontOfSize:10],
            NSForegroundColorAttributeName : UIColor.blackColor
        };

        CGSize txtSize = [valText sizeWithAttributes:attr];
        [valText drawAtPoint:CGPointMake(x - txtSize.width/2, y - 15)
              withAttributes:attr];
    }

    // ===============================
    // DATE LABELS (X AXIS)
    // ===============================
    for (int i = 0; i < self.dates.count; i++) {

        NSString *date = self.dates[i];

        NSDictionary *attr = @{
            NSFontAttributeName : [UIFont systemFontOfSize:10],
            NSForegroundColorAttributeName : UIColor.blackColor
        };

        CGSize size = [date sizeWithAttributes:attr];

        CGFloat x = paddingLeft + (stepX * i) - size.width / 2;
        CGFloat y = height - paddingBottom + 6;

        [date drawAtPoint:CGPointMake(x, y) withAttributes:attr];
    }

    // ===============================
    // AXIS TITLES
    // ===============================
    [@"Date" drawAtPoint:CGPointMake(width/2 - 20, height - 15)
          withAttributes:@{
              NSFontAttributeName:[UIFont boldSystemFontOfSize:12],
              NSForegroundColorAttributeName:UIColor.blackColor
          }];

    [@"Qty" drawAtPoint:CGPointMake(5, 5)
         withAttributes:@{
             NSFontAttributeName:[UIFont boldSystemFontOfSize:12],
             NSForegroundColorAttributeName:UIColor.blackColor
         }];
}

@end
