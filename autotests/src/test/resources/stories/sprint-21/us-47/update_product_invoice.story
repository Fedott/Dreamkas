Meta:
@sprint_21
@us_47

Narrative:
As a user
I want to perform an action
So that I can achieve a business goal

Scenario: Nothing found- Product Invoice

Meta:
@smoke
@id_s21u47s1

Given there is the user with name 'departmentManager-UIBS-FF', position 'departmentManager-UIBS-FF', username 'departmentManager-UIBS-FF', password 'lighthouse', role 'departmentManager'
And there is the store with number 'UIBS-FF' managed by department manager named 'departmentManager-UIBS-FF'

Given there is the subCategory with name 'ProductsUpdateInvoiceSubCategory' related to group named 'ProductsUpdateInvoiceGroup' and category named 'ProductsUpdateInvoiceCategory'
And there is the product with '7300330094025' name, '7300330094025' barcode, 'unit' type, '97,60' purchasePrice of group named 'ProductsUpdateInvoiceGroup', category named 'ProductsUpdateInvoiceCategory', subcategory named 'ProductsUpdateInvoiceSubCategory'

Given the user navigates to the product with name '7300330094025'
And the user logs in using 'departmentManager-UIBS-FF' userName and 'lighthouse' password

When the user clicks the product local navigation invoices link

Then the user checks page contains text 'Приходов товара еще не было'

Scenario: Found one invoice

Meta:
@smoke
@id_s21u47s2

Given there is the user with name 'departmentManager-UIBS-FF', position 'departmentManager-UIBS-FF', username 'departmentManager-UIBS-FF', password 'lighthouse', role 'departmentManager'
And there is the store with number 'UIBS-FF' managed by department manager named 'departmentManager-UIBS-FF'

Given there is the subCategory with name 'ProductsUpdateInvoiceSubCategory' related to group named 'ProductsUpdateInvoiceGroup' and category named 'ProductsUpdateInvoiceCategory'
And there is the product with '7300330094025' name, '7300330094025' barcode, 'unit' type, '97,60' purchasePrice of group named 'ProductsUpdateInvoiceGroup', category named 'ProductsUpdateInvoiceCategory', subcategory named 'ProductsUpdateInvoiceSubCategory'

Given the user creates invoice api object with values
| elementName | value |
| acceptanceDate | 02.04.2013 16:23 |
| accepter | accepter |
| legalEntity | legalEntity |
| supplierInvoiceNumber | supplierInvoiceNumber |
And the user adds the product with data to invoice api object
| elementName | value |
| productName | 7300330094025 |
| quantity | 1 |
| price | 100 |
And there is the invoice created with invoice builder steps by userName 'departmentManager-UIBS-FF'

Given the user navigates to the product with name '7300330094025'
And the user logs in using 'departmentManager-UIBS-FF' userName and 'lighthouse' password

When the user clicks the product local navigation invoices link

Then the user checks the product invoices list contains entry
| acceptanceDateFormatted | quantity | priceFormatted | totalPriceFormatted |
| 02.04.2013 | 1,0 | 100,00 | 100,00 |

Scenario: Found more invoices

Meta:
@depends_on_previous_test
@@id_s21u47s3

Given skipped test

!--Тест необходимо поправить
!--Есть ошибка, что при переходе на инвойс по ссылке из списка приходов - 404
!--Также не обновляется "invoice-sku" атрибут у тр элемента списка, вследствии чего на инвойс по локатору перейти нельзя

Given there is the user with name 'departmentManager-UIBS-FF', position 'departmentManager-UIBS-FF', username 'departmentManager-UIBS-FF', password 'lighthouse', role 'departmentManager'
And there is the store with number 'UIBS-FF' managed by department manager named 'departmentManager-UIBS-FF'

Given there is the subCategory with name 'ProductsUpdateInvoiceSubCategory' related to group named 'ProductsUpdateInvoiceGroup' and category named 'ProductsUpdateInvoiceCategory'
And there is the product with '7300330094025' name, '7300330094025' barcode, 'unit' type, '97,60' purchasePrice of group named 'ProductsUpdateInvoiceGroup', category named 'ProductsUpdateInvoiceCategory', subcategory named 'ProductsUpdateInvoiceSubCategory'

Given the user creates invoice api object with values
| elementName | value |
| acceptanceDate | 01.04.2013 12:22 |
| accepter | accepter |
| legalEntity | legalEntity |
| supplierInvoiceNumber | supplierInvoiceNumber |
And the user adds the product with data to invoice api object
| elementName | value |
| productName | 7300330094025 |
| quantity | 2 |
| price | 99 |
And there is the invoice created with invoice builder steps by userName 'departmentManager-UIBS-FF'

Given the user creates invoice api object with values
| elementName | value |
| acceptanceDate | 04.04.2013 16:25 |
| accepter | accepter |
| legalEntity | legalEntity |
| supplierInvoiceNumber | supplierInvoiceNumber |
And the user adds the product with data to invoice api object
| elementName | value |
| productName | 7300330094025 |
| quantity | 33 |
| price | 77 |
And there is the invoice created with invoice builder steps by userName 'departmentManager-UIBS-FF'

Given the user navigates to the product with name '7300330094025'
And the user logs in using 'departmentManager-UIBS-FF' userName and 'lighthouse' password

When the user clicks the product local navigation invoices link

Then the user checks the product invoices list contains entry
| acceptanceDateFormatted | quantity | priceFormatted | totalPriceFormatted |
| 02.04.2013 | 1,0 | 100,00 | 100,00 |
| 01.04.2013 | 2,0 | 99,00 | 198,00 |
| 04.04.2013 | 33,0 | 77,00 | 2 541,00 |

When the user clicks invoice sku 'UIBS-FF-03'

Then the user checks invoice 'head' elements  values
| elementName | value |
| sku | UIBS-FF-03 |
| acceptanceDate | 04.04.2013 16:25 |
| supplier | supplier |
| accepter | accepter |
| legalEntity | legalEntity |
| supplierInvoiceSku | supplierInvoiceSku |
| supplierInvoiceDate | 04.04.2013 |

Then the user checks the product with '7300330094025' sku has values
| elementName | value |
| productName | Корм Баффет д/кошек мясн.кус.в желе Морской коктейль 375г |
| productSku | 7300330094025 |
| productBarcode | 7300330094025 |
| productUnits | шт |
| productAmount | 33 |
| productPrice | 77 |
| productSum | 2 541,00 |

And the user checks invoice elements values
| elementName | value |
| totalProducts | 1 |
| totalSum | 2 541,00 |