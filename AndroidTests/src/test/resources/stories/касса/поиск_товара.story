Meta:
@us_113.3
@sprint_43

Narrative:
Как владелец,
Я хочу найти на кассе Dreamkas android товар,
Чтобы иметь возможность начать формировать чек продажи

Scenario: Поиск товара по части артикулу

Meta:

GivenStories: precondition/ресет_и_перезапуск_приложения.story,
              precondition/создание_пользователя.story,
              precondition/создание_магазинов.story
              precondition/создание_товаров.story

Given пользователь авторизируется в системе используя адрес электронной почты 'owner@lighthouse.pro' и пароль 'lighthouse'

When пользователь выбирает магазин с именем 'Магазин №2' из списка
And пользователь нажимает на кнопку 'Перейти к кассе'
And пользователь набирает в поле поиска товаров '100'

Then пользователь видит результат поиска, в котором присутствует товары в количестве '3'
And пользователь видит результат поиска, в котором присутствует товар с названием 'Товар1 / 10001'
And пользователь видит результат поиска, в котором присутствует товар с названием 'Вар2 / 10002'
And пользователь видит результат поиска, в котором присутствует товар с названием 'товар3 / 10003'

Scenario: Поиск товара по названию

Meta:

GivenStories: precondition/ресет_и_перезапуск_приложения.story,
              precondition/создание_пользователя.story,
              precondition/создание_магазинов.story
              precondition/создание_товаров.story

Given пользователь авторизируется в системе используя адрес электронной почты 'owner@lighthouse.pro' и пароль 'lighthouse'

When пользователь выбирает магазин с именем 'Магазин №2' из списка
And пользователь нажимает на кнопку 'Перейти к кассе'
And пользователь набирает в поле поиска товаров 'Товар'

Then пользователь видит результат поиска, в котором присутствует товары в количестве '2'
And пользователь видит результат поиска, в котором присутствует товар с названием 'Товар1 / 10001'
And пользователь видит результат поиска, в котором присутствует товар с названием 'товар3 / 10003'

Scenario: Поиск товара по штрих-коду

Meta:

GivenStories: precondition/ресет_и_перезапуск_приложения.story,
              precondition/создание_пользователя.story,
              precondition/создание_магазинов.story
              precondition/создание_товаров.story

Given пользователь авторизируется в системе используя адрес электронной почты 'owner@lighthouse.pro' и пароль 'lighthouse'

When пользователь выбирает магазин с именем 'Магазин №2' из списка
And пользователь нажимает на кнопку 'Перейти к кассе'
And пользователь набирает в поле поиска товаров '2222'

Then пользователь видит результат поиска, в котором присутствует товары в количестве '2'
And пользователь видит результат поиска, в котором присутствует товар с названием 'Вар2 / 10002'

Scenario: Проверка сообщения автокомплита о том, что надо ввести 3 или более символа

Meta:

Given пользователь ресетит все данные и перезапускает приложение
Given пользователь запускает консольную команду для создания пользователя с параметрами: адрес электронной почты 'androidpos@lighthouse.pro' и пароль 'lighthouse'
Given пользователь с адресом электронной почты 'androidpos@lighthouse.pro' создает магазин с именем 'Магазин №2' и адресом 'адресс'

Given пользователь авторизируется в системе используя адрес электронной почты 'owner@lighthouse.pro' и пароль 'lighthouse'

When пользователь выбирает магазин с именем 'Магазин №2' из списка
And пользователь нажимает на кнопку 'Перейти к кассе'
And пользователь набирает в поле поиска товаров productSearchQuery

Then пользователь проверяет, что у автокоплитного поля есть сообщение 'Для поиска товара введите 3 или более символа.'

Examples:
| productSearchQuery |
|  |
| 1 |
| 11 |

Scenario: Проверка сообщения автокомплита когда не найдено ни одного товара

Meta:

GivenStories: precondition/ресет_и_перезапуск_приложения.story,
              precondition/создание_пользователя.story,
              precondition/создание_магазинов.story
              precondition/создание_товаров.story

Given пользователь авторизируется в системе используя адрес электронной почты 'owner@lighthouse.pro' и пароль 'lighthouse'

When пользователь выбирает магазин с именем 'Магазин №2' из списка
And пользователь нажимает на кнопку 'Перейти к кассе'
And пользователь набирает в поле поиска товаров '2222'

Then пользователь проверяет, что у автокоплитного поля есть сообщение 'Продуктов не найдено.'