Meta:
@sprint_42
@us_112.1

Narrative:
Как владелец,
Я хочу увидеть список чеков продаж за выбранный период,
Чтобы найти продажу требуемую для возврата

Scenario: Проверка попадания чека в фильтр дат истории продаж кассы

Meta:
@smoke

GivenStories: precondition/customPrecondition/symfonyEnvInitPrecondition.story,
              precondition/касса/создание_юзера.story,
              precondition/касса/создание_магазина_с_товаром.story,
              precondition/касса/создание_продаж_для_проверки_фильтра_дат_в_истории_продаж_кассы.story

Given пользователь открывает стартовую страницу авторизации
And пользователь авторизуется в системе используя адрес электронной почты 's41u1111@lighthouse.pro' и пароль 'lighthouse'

Given пользователь открывает страницу кассы магазина с названием 'store-s41u1111'

When пользователь нажимает на кнопку чтобы показать боковое меню навигации кассы
And пользователь нажимает на ссылку с названием История продаж в боковом меню кассы

When пользователь* находится на странице 'истории продаж кассы'

When пользователь* вводит значение 'todayDate-6' в поле с именем 'дата с'

Then пользователь ждет пока загрузится простой прелоадер

When пользователь* вводит значение 'todayDate-5' в поле с именем 'дата по'

Then пользователь ждет пока загрузится простой прелоадер

Then пользователь* проверяет, что список 'истории продаж' содержит точные данные
| price | date |
| 490,00 | saleTodayDate-5 |
| 590,00 | saleTodayDate-6 |

Scenario: Проверка попадания чека в фильтр дат истории продаж кассы (с=сегодня, по=сегодня)

Meta:
@smoke

GivenStories: precondition/customPrecondition/symfonyEnvInitPrecondition.story,
              precondition/касса/создание_юзера.story,
              precondition/касса/создание_магазина_с_товаром.story

Given пользователь создает чек c датой 'saleTodayDate'
And пользователь добавляет товар в чек с именем 'pos-product1', количеством '1' и по цене '350'
And пользователь вносит наличные в размере '1000' рублей
And пользователь с адресом электронной почты 's41u1111@lighthouse.pro' в магазине с именем 'store-s41u1111' совершает продажу по созданному чеку

Given пользователь открывает стартовую страницу авторизации
And пользователь авторизуется в системе используя адрес электронной почты 's41u1111@lighthouse.pro' и пароль 'lighthouse'

Given пользователь открывает страницу кассы магазина с названием 'store-s41u1111'

When пользователь нажимает на кнопку чтобы показать боковое меню навигации кассы
And пользователь нажимает на ссылку с названием История продаж в боковом меню кассы

When пользователь* находится на странице 'истории продаж кассы'

When пользователь* вводит значение 'todayDate' в поле с именем 'дата с'

Then пользователь ждет пока загрузится простой прелоадер

When пользователь* вводит значение 'todayDate' в поле с именем 'дата по'

Then пользователь ждет пока загрузится простой прелоадер

Then пользователь* проверяет, что список 'истории продаж' содержит точные данные
| price | date |
| 350,00 | saleTodayDate |

Scenario: Проверка непопадания чека в фильтр дат истории продаж кассы

Meta:
@smoke

GivenStories: precondition/customPrecondition/symfonyEnvInitPrecondition.story,
              precondition/касса/создание_юзера.story,
              precondition/касса/создание_магазина_с_товаром.story,
              precondition/касса/создание_продаж_для_проверки_фильтра_дат_в_истории_продаж_кассы.story

Given пользователь открывает стартовую страницу авторизации
And пользователь авторизуется в системе используя адрес электронной почты 's41u1111@lighthouse.pro' и пароль 'lighthouse'

Given пользователь открывает страницу кассы магазина с названием 'store-s41u1111'

When пользователь нажимает на кнопку чтобы показать боковое меню навигации кассы
And пользователь нажимает на ссылку с названием История продаж в боковом меню кассы

When пользователь* находится на странице 'истории продаж кассы'

When пользователь* вводит значение 'todayDate-3' в поле с именем 'дата с'

Then пользователь ждет пока загрузится простой прелоадер

When пользователь* вводит значение 'todayDate' в поле с именем 'дата по'

Then пользователь ждет пока загрузится простой прелоадер

Then пользователь проверяет, что на странице присутствует текст 'Продаж не найдено.'

Scenario: Сообщение если нет продаж

Meta:

GivenStories: precondition/customPrecondition/symfonyEnvInitPrecondition.story,
              precondition/касса/создание_юзера.story,
              precondition/касса/создание_магазина_с_товаром.story

Given пользователь открывает стартовую страницу авторизации
And пользователь авторизуется в системе используя адрес электронной почты 's41u1111@lighthouse.pro' и пароль 'lighthouse'

Given пользователь открывает страницу кассы магазина с названием 'store-s41u1111'

When пользователь нажимает на кнопку чтобы показать боковое меню навигации кассы
And пользователь нажимает на ссылку с названием История продаж в боковом меню кассы

Then пользователь проверяет, что на странице присутствует текст 'Продаж не найдено.'

Scenario: Фильтр дат в истории продаж кассы по умолчанию выставлен на неделю назад с текущего дня

Meta:
@smoke

GivenStories: precondition/касса/создание_юзера.story,
              precondition/касса/создание_магазина_с_товаром.story

Given пользователь открывает стартовую страницу авторизации
And пользователь авторизуется в системе используя адрес электронной почты 's41u1111@lighthouse.pro' и пароль 'lighthouse'

Given пользователь открывает страницу кассы магазина с названием 'store-s41u1111'

When пользователь нажимает на кнопку чтобы показать боковое меню навигации кассы
And пользователь нажимает на ссылку с названием История продаж в боковом меню кассы

When пользователь* находится на странице 'истории продаж кассы'

Then пользователь* проверяет поля
| elementName| value |
| дата с | todayDate-7 |
| дата по | todayDate |