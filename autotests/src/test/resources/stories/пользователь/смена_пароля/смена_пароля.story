Meta:
@release_0.45
@us_121

Narrative:
Как владелец торговой точки,
Я хочу изменить свой пароль учётной записи Дримкас,
Чтобы сохранить контроль над аккаунтом Дримкас

Scenario: Смена пароля

Meta:
@smoke

GivenStories: precondition/пользователь/создание_юзера.story

Given пользователь открывает стартовую страницу авторизации
And пользователь авторизуется в системе используя адрес электронной почты 'user@lighthouse.pro' и пароль 'lighthouse'

When пользователь* находится на странице 'c боковой навигацией'
And пользователь* нажимает на елемент с именем 'настройки пользователя'

When пользователь* находится на странице 'настроек пользователя'
And пользователь* вводит данные в поля
| elementName | value |
| Старый пароль | lighthouse |
| Новый пароль | newlighthouse |
| Подтверждение пароля | newlighthouse |

And пользователь* нажимает на елемент с именем 'кнопка сохранения нового пароля'

Then пользователь* проверяет, что 'сообщение о подтвержении смены пароля' имеет значение 'Пароль изменен.'

When пользователь разлогинивается

When пользователь авторизуется в системе используя адрес электронной почты 'user@lighthouse.pro' и пароль 'newlighthouse'

Then пользователь видит что он авторизирован как 'user@lighthouse.pro'

Scenario: Попытка авторизироваться под старым паролем

Meta:

GivenStories: precondition/пользователь/создание_юзера.story

Given пользователь открывает стартовую страницу авторизации
And пользователь авторизуется в системе используя адрес электронной почты 'user@lighthouse.pro' и пароль 'lighthouse'

When пользователь* находится на странице 'c боковой навигацией'
And пользователь* нажимает на елемент с именем 'настройки пользователя'

When пользователь* находится на странице 'настроек пользователя'
And пользователь* вводит данные в поля
| elementName | value |
| Старый пароль | lighthouse |
| Новый пароль | newlighthouse |
| Подтверждение пароля | newlighthouse |

And пользователь* нажимает на елемент с именем 'кнопка сохранения нового пароля'

Then пользователь* проверяет, что 'сообщение о подтвержении смены пароля' имеет значение 'Пароль изменен.'

When пользователь разлогинивается

When пользователь авторизуется в системе используя адрес электронной почты 'user@lighthouse.pro' и пароль 'lighthouse'

Then пользователь видит сообщение об ошибке 'Пароль неверен!'

Scenario: Проверка заголовка страницы настроек

Meta:

GivenStories: precondition/пользователь/создание_юзера.story

Given пользователь открывает стартовую страницу авторизации
And пользователь авторизуется в системе используя адрес электронной почты 'user@lighthouse.pro' и пароль 'lighthouse'

Given пользователь* взаимодействует со страницей 'настроек пользователя'
And пользователь* открывает страницу

Then пользователь* проверяет, что заголовок равен 'Настройки'