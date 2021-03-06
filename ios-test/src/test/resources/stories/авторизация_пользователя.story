Meta:
@us_124.1

Narrative:
Как владелец ,
Я хочу авторизоваться в Dreamkas iOS,
Чтобы начать работать с приложением

Scenario: Авторизация пользователя

Meta:
@smoke

GivenStories: precondition/полный_сброс_данных_симулятора.story,
              precondition/создание_пользователя.story

Given пользователь* находится на 'стартовом экране'

When пользователь* нажимает на кнопку 'Войти'

Given пользователь* находится в 'диалоговом окне авторизации'

When пользователь* вводит значение 'ios@lighthouse.pro' в поле с именем 'Логин'
And пользователь* вводит значение 'lighthouse' в поле с именем 'Пароль'
And пользователь* нажимает на кнопку 'Войти'

Given пользователь* находится в 'диалоговом окне выбора магазина'

Then пользователь* проверяет, что 'Заголовок' содержит текст 'Выбор магазина'

Scenario: Авторизация пользователя с неправильными кредами

Meta:

GivenStories: precondition/полный_сброс_данных_симулятора.story,
              precondition/создание_пользователя.story

Given пользователь* находится на 'стартовом экране'

When пользователь* нажимает на кнопку 'Войти'

Given пользователь* находится в 'диалоговом окне авторизации'

When пользователь* вводит значение 'error@lighthouse.pro' в поле с именем 'Логин'
And пользователь* вводит значение 'errorerror' в поле с именем 'Пароль'
And пользователь* нажимает на кнопку 'Войти'

Then пользователь* проверяет, что 'Заголовок ошибки' содержит текст 'Ошибка'
And пользователь* проверяет, что 'Текст ошибки' содержит текст 'Во время запроса произошла ошибка. Попробуйте снова'