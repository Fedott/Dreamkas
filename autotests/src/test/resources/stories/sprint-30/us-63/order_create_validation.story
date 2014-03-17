Meta:
@sprint_30
@us_63
@order

Narrative:
Валидация при создании

Scenario: Supplier option is required

Meta:
@id

GivenStories: precondition/sprint-30/us-63/aPreconditionToStoryUs63.story

Given the user opens order create page
And the user logs in using 'departmentManager-s30u63' userName and 'lighthouse' password

When the user clicks the save order button

Then the user sees error messages
|error message |
| Выберите поставщика |

Scenario: Cannot create order without product

Meta:
@id

GivenStories: precondition/sprint-30/us-63/aPreconditionToStoryUs63.story

Given the user opens order create page
And the user logs in using 'departmentManager-s30u63' userName and 'lighthouse' password

When the user inputs values on order page
| elementName | value |
| supplier | supplier-s30u63s1 |

When the user clicks the save order button

Then the user sees error messages
|error message |
| Нельзя создать заказ без продукта |

Scenario: Addition product form - autocomplete is required

Meta:
@id

GivenStories: precondition/sprint-30/us-63/aPreconditionToStoryUs63.story

Given the user opens order create page
And the user logs in using 'departmentManager-s30u63' userName and 'lighthouse' password

When the user inputs values on order page
| elementName | value |
| supplier | supplier-s30u63s1 |

When the user clicks the add order product button

Then the user sees error messages
| error message |
| Нет такого товара |

Scenario: Addition product form - quantity is required

Meta:
@id

GivenStories: precondition/sprint-30/us-63/aPreconditionToStoryUs63.story

Given the user opens order create page
And the user logs in using 'departmentManager-s30u63' userName and 'lighthouse' password

When the user inputs values on order page
| elementName | value |
| supplier | supplier-s30u63s1 |
When the user inputs values in addition new product form on the order page
| elementName | value |
| name | name-3063 |

When the user clicks the add order product button

Then the user sees error messages
| error message |
| Заполните это поле |

Scenario: Addition product form - autocomplete validation

Meta:
@id

GivenStories: precondition/sprint-30/us-63/aPreconditionToStoryUs63.story

Given the user opens order create page
And the user logs in using 'departmentManager-s30u63' userName and 'lighthouse' password

When the user inputs values on order page
| elementName | value |
| supplier | supplier-s30u63s1 |
When the user inputs values in addition new product form on the order page
| elementName | value |
| name | !dfdfdfdfdfdfdf |

When the user clicks the add order product button

Then the user sees error messages
| error message |
| Нет такого товара |


Scenario: Addition product form - quantity negative validation

Meta:
@id

Given there is the supplier with name 'supplier-s30u63s1'

And there is the subCategory with name 'defaultSubCategory-s30u63' related to group named 'defaultGroup-s30u63' and category named 'defaultCategory-s30u63'
And the user sets subCategory 'defaultSubCategory-s30u63' mark up with max '10' and min '0' values

Given there is the product with 'name-3063' name, '3063' sku, '3063' barcode, 'unit' units, '100' purchasePrice of group named 'defaultGroup-s30u63', category named 'defaultCategory-s30u63', subcategory named 'defaultSubCategory-s30u63'

Given there is the user with name 'departmentManager-s30u63', position 'departmentManager-s30u63', username 'departmentManager-s30u63', password 'lighthouse', role 'departmentManager'
And there is the store with number 'store-s30u63' managed by department manager named 'departmentManager-s30u63'

Given the user opens order create page
And the user logs in using 'departmentManager-s30u63' userName and 'lighthouse' password

When the user inputs values on order page
| elementName | value |
| supplier | supplier-s30u63s1 |
And the user inputs values in addition new product form on the order page
| elementName | value |
| name | name-3063 |

When the user inputs value in elementName 'quantity' in addition new product form on the order page

When the user clicks the add order product button

Then the user user sees errorMessage

Examples:
| value | errorMessage |
| -10 | Значение должно быть больше 0 |
| -1 | Значение должно быть больше 0 |
| -1,12 | Значение должно быть больше 0 |
| -1.12 | Значение должно быть больше 0 |
| -1,123 | Значение должно быть больше 0 |
| 0 | Значение должно быть больше 0 |
| asdd | Значение должно быть числом |
| ADHF | Значение должно быть числом |
| домик | Значение должно быть числом |
| ДОМИЩЕ | Значение должно быть числом |
| ^%#$)& | Значение должно быть числом |

Scenario: Cannot create order if departmantManager dont have store

Meta:
@id

Given there is the user with name 'departmentManager-s30u63-no-store', position 'departmentManager-s30u63-no-store', username 'departmentManager-s30u63-no-store', password 'lighthouse', role 'departmentManager'
And the user opens the authorization page
And the user logs in using 'departmentManager-s30u63-no-store' userName and 'lighthouse' password

Then the user checks the orders navigation menu item is not visible

Scenario: Cannot create order throug link if departmantManager dont have store

Meta:
@id

Given there is the user with name 'departmentManager-s30u63-no-store', position 'departmentManager-s30u63-no-store', username 'departmentManager-s30u63-no-store', password 'lighthouse', role 'departmentManager'
And the user opens order create page
And the user logs in using 'departmentManager-s30u63-no-store' userName and 'lighthouse' password

Then the user sees the 403 error

Scenario: Cannot view order list throug link if departmantManager dont have store

Meta:
@id

Given there is the user with name 'departmentManager-s30u63-no-store', position 'departmentManager-s30u63-no-store', username 'departmentManager-s30u63-no-store', password 'lighthouse', role 'departmentManager'
And the user opens orders list page
And the user logs in using 'departmentManager-s30u63-no-store' userName and 'lighthouse' password

Then the user sees the 403 error

