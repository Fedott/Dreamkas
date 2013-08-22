Meta:
@sprint 15
@us 25

Narrative:
As заведующим отделом,
I want to чтобы данные в списании не изменялись при редактировании коммерческой службой товаров
In order to иметь возможность работать с оригинальной версией документа

Scenario: WriteOff data independence
Given there is the write off with 'WriteOff-DI-Test' number with product 'WriteOff-DI-Test' with quantity '1', price '1' and cause 'cause'
And the user navigates to the product with sku 'WriteOff-DI-Test'
And the user logs in as 'commercialManager'
When the user clicks the edit button on product card view page
And the user inputs values in element fields
| elementName | value |
| name | WriteOff-DI-Test name edited |
| barcode | WriteOff-DI-Test barcode edited |
| sku | WriteOff-DI-Test sku edited |
And the user clicks the create button
Then the user checks the stored input values
When the user logs out
Given the user navigates to the write off with number 'WriteOff-DI-Test'
And the user logs in as 'departmentManager'
Then the user checks the write off product with 'WriteOff-DI-Test sku edited' sku is not present
And the user checks the product with 'WriteOff-DI-Test' sku has elements on the write off page
| elementName | value |
| writeOff product name review | WriteOff-DI-Test |
| writeOff product barCode review | WriteOff-DI-Test |
| writeOff product sku review | WriteOff-DI-Test |

Scenario: Edited product can be added again to write off
Given there is the write off with 'WriteOff-DI-Test1' number with product 'WriteOff-DI-Test1' with quantity '1', price '1' and cause 'cause'
And the user navigates to the product with sku 'WriteOff-DI-Test1'
And the user logs in as 'commercialManager'
When the user clicks the edit button on product card view page
And the user inputs values in element fields
| elementName | value |
| name | WriteOff-DI-Test1 name edited |
| barcode | WriteOff-DI-Test1 barcode edited |
| sku | WriteOff-DI-Test sku1 edited |
And the user clicks the create button
Then the user checks the stored input values
When the user logs out
Given the user navigates to the write off with number 'WriteOff-DI-Test1'
And the user logs in as 'departmentManager'
When the user clicks edit button and starts write off edition
And the user inputs 'WriteOff-DI-Test1 name edited' in the 'writeOff product name autocomplete' field on the write off page
And the user inputs '1' in the 'writeOff product quantity' field on the write off page
And the user inputs 'Причина сдачи: Истек срок хранения' in the 'writeOff cause' field on the write off page
And the user presses the add product button and add the product to write off
And the user clicks finish edit button and ends the write off edition
Then the user checks the product with 'WriteOff-DI-Test sku1 edited' sku has elements on the write off page
| elementName | value |
| writeOff product name review | WriteOff-DI-Test1 name edited |
| writeOff product barCode review | WriteOff-DI-Test1 barcode edited |
| writeOff product sku review | WriteOff-DI-Test sku1 edited |
