//
//  BlockAndProtocolConstants.h
//  EO-GOALS
//
//  Created by Coral  on 19/05/16.
//  Copyright © 2016 Coral . All rights reserved.
//

typedef void(^blockStatusMessage)(NSString *strStatus, NSString *strMessgae);
typedef void(^blockArrayWithMessage)(NSString *strStatus, NSString *strMessgae, NSArray *arrValue);
typedef void(^blockDictWithMessage)(NSString *strStatus, NSString *strMessgae, NSDictionary *dictValue);