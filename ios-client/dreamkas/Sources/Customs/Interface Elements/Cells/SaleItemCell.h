//
//  SaleItemCell.h
//  dreamkas
//
//  Created by sig on 18.11.14.
//  Copyright (c) 2014 Dreamkas. All rights reserved.
//

#import "CustomTableViewCell.h"

@interface SaleItemCell : CustomTableViewCell

/** Название продуктовой единицы */
@property (nonatomic, weak) IBOutlet WordWrappingLabel *titleLabel;

/** Стоимость продуктовой единицы */
@property (nonatomic, weak) IBOutlet UILabel *priceLabel;

@end
