Meta:
@sprint_40
@us_103

Narrative:
Как владелец,
Я хочу создавать, редактировать и удалять списания товаров от поставщика в магазинах,
Чтобы остатки себестоимости товаров в учетной системе соответствовали действительности

Scenario: Создание списания

Meta:
@smoke
@id_s40u103s1

GivenStories: precondition/customPrecondition/symfonyEnvInitPrecondition.story,
              precondition/writeOff/aPreconditionToUserCreation.story,
              precondition/writeOff/aPreconditionToTestDataCreation.story

Given пользователь открывает страницу товародвижения
And пользователь авторизуется в системе используя адрес электронной почты 's40u103@lighthouse.pro' и пароль 'lighthouse'

When пользователь нажимает на кнопку Списать на странице товародвижения
And пользователь вводит данные в модальном окне создания списания
| elementName | value |
| date | 08.11.2014 |
| store | s40u103-store |
| product.name | s40u103-product1 |
| price | 150 |
| quantity | 5 |
| cause | причина |
And пользователь нажимает на кнопку добавления нового товара в списание

Then пользователь проверяет, что список товаров в списании содержит товары с данными
| name | price | quantity | cause | totalPrice |
| s40u103-product1 | 150,00  | 5,0 шт. | причина | 750,00 |
And пользователь проверяет, что сумма итого равна '750,00' в модальном окне создания списания

When пользователь нажимает на кнопку Списать, чтобы списать накладную с товарами

Then пользователь ждет пока скроется модальное окно

Then пользователь проверяет операции на странице товародвижения
| date | type | store | sumTotal |
| 08.11.2014 | Списание | В s40u103-store | 750,00 |

When пользователь нажимает на списание с номером '10001' на странице товародвижения

Then пользователь проверяет поля в модальном окне редактирования списания
| elementName | value |
| date | 08.11.2014 |
| store | s40u103-store |
| supplier | s40u103-supplier |
And пользователь проверяет, что список товаров в списании содержит товары с данными
| name | price | quantity | cause | totalPrice |
| s40u103-product1 | 150,00  | 5,0 шт. | причина | 750,00 |
And пользователь проверяет, что сумма итого равна '750,00' в модальном окне редактирования списания


Scenario: Редактирование списания

Meta:
@smoke
@id_s40u103s2

GivenStories: precondition/customPrecondition/symfonyEnvInitPrecondition.story,
              precondition/writeOff/aPreconditionToUserCreation.story,
              precondition/writeOff/aPreconditionToTestDataCreation.story,
              precondition/writeOff/aPreconditionForWriteOffEditionScenario.story,
              precondition/writeOff/aPreconditionToTestWriteOffCreation.story

Given пользователь открывает страницу товародвижения
And пользователь авторизуется в системе используя адрес электронной почты 's40u103@lighthouse.pro' и пароль 'lighthouse'

When пользователь нажимает на последнее созданное списание с помощью конструктора списаний на странице товародвижения
And пользователь вводит данные в модальном окне редактирования списания
| elementName | value |
| date | 08.11.2014 |
| store | s40u103-store1 |
| supplier | s40u103-supplier1 |
| product.name | s40u103-product2 |
And пользователь нажимает на кнопку добавления нового товара в списание в модальном окне редактирования списания

Then пользователь проверяет, что список товаров в списании содержит товары с данными
| name | priceEntered | quantity | totalPrice |
| s40u103-product1 | 150,00  | 5,0 шт. | 750,00 |
| s40u103-product2 | 125,50  | 1,0 Пятюня | 125,50 |
And пользователь проверяет, что сумма итого равна '875,50' в модальном окне редактирования списания

When пользователь нажимает на кнопку сохранения списания в модальном окне редактирования списания

Then пользователь ждет пока скроется модальное окно

Then пользователь проверяет операции на странице товародвижения
| date | type | status | store | sumTotal |
| 08.11.2014 | Приёмка | / не оплачена | В s40u103-store1 | 875,50 |

When пользователь нажимает на списание с номером '10001' на странице товародвижения

Then пользователь проверяет поля в модальном окне редактирования списания
| elementName | value |
| date | 08.11.2014 |
| store | s40u103-store1 |
| supplier | s40u103-supplier1 |
Then пользователь проверяет, что список товаров в списании содержит товары с данными
| name | priceEntered | quantity | totalPrice |
| s40u103-product1 | 150,00  | 5,0 шт. | 750,00 |
| s40u103-product2 | 125,50  | 1,0 Пятюня | 125,50 |
And пользователь проверяет, что сумма итого равна '875,50' в модальном окне редактирования списания