Meta:
@us_70
@sprint_34

Narrative:
Как комерческий директор
Я хочу при добавлении алкоголя ввести все необходимые данные,
Чтобы товар можно было продавать в магазинах по всем правилам

Крепость - 0 <= x <100, один знак после запятой, число (Decimal), не обязательное
Объем тары - x >0, три знака после запятой, число (Decimal), не обязательное

Scenario: Product edit alcoholByVolume field positive validation

Meta:
@id_s34u70s8

Given there is the product with <productName> name, 'alcohol' type
And the user navigates to the product with name <productName>
And the user logs in as 'commercialManager'

When the user clicks the edit button on product card view page
And the user inputs <exampleValue> in <exampleElement> element field
And the user clicks the create button

Then the user sees no error messages

Given the user is on the product list page

Then the user checks the product with <productName> name is present

When the user clicks on product with <productName>

Then the user checks the <exampleElement> has <exampleExpectedValue>

Examples:
| exampleElement | exampleValue | productName | exampleExpectedValue |
| alcoholByVolume | 0 | s34u70s07e06.0 | 0 |
| alcoholByVolume | 0,1 | s34u70s07e06.1 | 0,1 |
| alcoholByVolume | 0.1 | s34u70s07e06.2 | 0,1 |
| alcoholByVolume | 1 | s34u70s07e06.3 | 1 |
| alcoholByVolume | 99.9 | s34u70s07e06.4 | 99,9 |
| alcoholByVolume | 99,9 | s34u70s07e06.5 | 99,9 |
| alcoholByVolume | 40 | s34u70s07e06.6 | 40 |
| alcoholByVolume | 40,0 | s34u70s07e06.7 | 40,0 |
| alcoholByVolume | 40.0 | s34u70s07e06.8 | 40,0 |


Scenario: Product edit alcoholByVolume field negative validation

Meta:
@id_s34u70s9

Given there is the product with <productName> name, 'alcohol' type
And the user navigates to the product with name <productName>
And the user logs in as 'commercialManager'

When the user clicks the edit button on product card view page
And the user inputs <exampleValue> in <exampleElement> element field
And the user clicks the create button

Then the user checks the element field 'alcoholByVolume' has errorMessage

Examples:
| exampleElement | exampleValue | errorMessage | productName |
| alcoholByVolume | -0,1 | Значение должно быть больше или равно 0 | s34u70s07e06.3 |
| alcoholByVolume | -0.1 | Значение должно быть больше или равно 0 | s34u70s07e06.4 |
| alcoholByVolume | -1 | Значение должно быть больше или равно 0 | s34u70s07e06.5 |
| alcoholByVolume | 1,12 | Значение не должно содержать больше 1 цифр после запятой | s34u70s07e06.6 |
| alcoholByVolume | 1.12 | Значение не должно содержать больше 1 цифр после запятой | s34u70s07e06.7 |
| alcoholByVolume | 100 | Значение должно быть меньше 100 | s34u70s07e06.8 |
| alcoholByVolume | 101 | Значение должно быть меньше 100 | s34u70s07e06.9 |
| alcoholByVolume | alco | Значение должно быть числом | s34u70s07e06.10 |
| alcoholByVolume | ALCO | Значение должно быть числом | s34u70s07e06.11 |
| alcoholByVolume | алко | Значение должно быть числом | s34u70s07e06.12 |
| alcoholByVolume | АЛКО | Значение должно быть числом | s34u70s07e06.13 |
| alcoholByVolume | !"№;%:?*() | Значение должно быть числом | s34u70s07e06.14 |
| alcoholByVolume | 1 1 | Значение должно быть числом | s34u70s07e06.15 |
| alcoholByVolume | вы33434№4 | Значение должно быть числом | s34u70s07e06.16 |

Scenario: Product edit volume field positive validation

Meta:
@id_s34u70s10

Given there is the product with <productName> name, 'alcohol' type
And the user navigates to the product with name <productName>
And the user logs in as 'commercialManager'

When the user clicks the edit button on product card view page
And the user inputs <exampleValue> in <exampleElement> element field
And the user clicks the create button

Then the user sees no error messages

Given the user is on the product list page

Then the user checks the product with <productName> name is present

When the user clicks on product with <productName>

Then the user checks the <exampleElement> has <exampleExpectedValue>

Examples:
| exampleElement | exampleValue | productName | exampleExpectedValue |
| volume | 1 | s34u70s07d06.0 | 1 |
| volume | 100,000 | s34u70s07d06.1 | 100,0 |
| volume | 1,123 | s34u70s07d06.2 | 1,123 |
| volume | 1.123 | s34u70s07d06.3 | 1,123 |
| volume | 1.1 | s34u70s07d06.4 | 1,1 |
| volume | 1,1 | s34u70s07d06.5 | 1,1 |
| volume | 1,12 | s34u70s07d06.6 | 1,12 |
| volume | 1.12 | s34u70s07d06.7 | 1,12 |
| volume | 1.13 | s34u70s07d06.8 | 1,13 |
| volume | 1,13 | s34u70s07d06.9 | 1,13 |


Scenario: Product edit volume field negative validation

Meta:
@id_s34u70s11

Given there is the product with <productName> name, 'alcohol' type
And the user navigates to the product with name <productName>
And the user logs in as 'commercialManager'

When the user clicks the edit button on product card view page
And the user inputs <exampleValue> in <exampleElement> element field
And the user clicks the create button

Then the user checks the element field 'volume' has errorMessage

Examples:
| exampleElement | exampleValue | errorMessage | productName |
| volume | 0 | Значение должно быть больше 0 | s34u70s07d07.1 |
| volume | -1 | Значение должно быть больше 0 | s34u70s07d07.5 |
| volume | 1,1234 | Значение не должно содержать больше 3 цифр после запятой | s34u70s07d07.6 |
| volume | 1.1234 | Значение не должно содержать больше 3 цифр после запятой | s34u70s07d07.7 |
| volume | alco | Значение должно быть числом | s34u70s07d07.8 |
| volume | ALCO | Значение должно быть числом | s34u70s07d07.9 |
| volume | алко | Значение должно быть числом | s34u70s07d07.10 |
| volume | АЛКО | Значение должно быть числом | s34u70s07d07.11 |
| volume | !"№;%:?*() | Значение должно быть числом | s34u70s07d07.12 |
| volume | 1 1 | Значение должно быть числом | s34u70s07d07.13 |
| volume | вы33434№4 | Значение должно быть числом | s34u70s07d07.14 |