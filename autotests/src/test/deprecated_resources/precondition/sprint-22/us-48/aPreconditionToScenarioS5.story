Meta:
@sprint_22
@us_48
@id_s22u48s5
@smoke

Scenario: A scenario that prepares data

Given there is the product with 'SCPBC-name-11' name, 'SCPBC-barcode-11' barcode, 'unit' type, '12,34' purchasePrice of group named 'SCPBC-defaultGroup', category named 'SCPBC-defaultCategory', subcategory named 'SCPBC-defaultSubCategory'
And there is the writeOff in the store with number 'SCPBC' ruled by department manager with name 'departmentManager-SCPBC' with values
| elementName | elementValue |
| number | SCPBC-12 |
| date | 02.04.2013 |
And the user adds the product to the write off with number 'SCPBC-12' with name 'SCPBC-name-11', quantity '1', price '12,34', cause 'Плохо продавался' in the store ruled by 'departmentManager-SCPBC'

