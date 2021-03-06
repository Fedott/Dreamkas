Meta:
@us_132

Narrative:
Как владелец магазина,
я хочу редактировать операции с деньгами и просматривать список операций с деньгами,
чтобы иметь возможность отслеживать поток и баланс денежных средств

GivenStories: precondition/customPrecondition/symfonyEnvInitPrecondition.story,
              precondition/пользователь/создание_юзера.story,
              precondition/деньги/создание_денежных_операций_для_проверки_фильтров.story

Scenario: Операции по дате выходит из фильтра дат на странице товародвижения

Meta:

Given пользователь* открывает страницу 'списка денежных операций'
And пользователь авторизуется в системе используя адрес электронной почты 'user@lighthouse.pro' и пароль 'lighthouse'

When пользователь* вводит значение 'todayDate-4' в поле с именем 'Дата с'

Then пользователь ждет пока загрузится простой прелоадер

When пользователь* вводит значение 'todayDate-3' в поле с именем 'Дата по'

Then пользователь ждет пока загрузится простой прелоадер

Then пользователь* проверяет, что список 'денежных операций' содержит точные данные
| Дата | Сумма | Комментарий |
| todayDate-3 | + 100,00 | comment |

Scenario: Операции по дате входит из фильтра дат на странице товародвижения

Meta:

Given пользователь* открывает страницу 'списка денежных операций'
And пользователь авторизуется в системе используя адрес электронной почты 'user@lighthouse.pro' и пароль 'lighthouse'

When пользователь* вводит значение 'todayDate-5' в поле с именем 'Дата с'

Then пользователь ждет пока загрузится простой прелоадер

When пользователь* вводит значение 'todayDate-3' в поле с именем 'Дата по'

Then пользователь ждет пока загрузится простой прелоадер

Then пользователь* проверяет, что список 'денежных операций' содержит точные данные
| Дата | Сумма | Комментарий |
| todayDate-3 | + 100,00 | comment |
| todayDate-5 | + 101,00 | comment |