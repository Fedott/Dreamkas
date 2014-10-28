//
//  ProductModel.h
//  dreamkas
//
//  Created by sig on 28.10.14.
//  Copyright (c) 2014 Dreamkas. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>
#import "AbstractModel.h"


@interface ProductModel : AbstractModel

@property (nonatomic, retain) NSString * name;
@property (nonatomic, retain) NSString * barcode;
@property (nonatomic, retain) NSNumber * purchasePrice;
@property (nonatomic, retain) NSNumber * sellingPrice;
@property (nonatomic, retain) NSNumber * sku;

@end
