Meta:
@precondition

Scenario: Сценарий для создания товаров

Given пользователь с адресом электронной почты 'androidpos@lighthouse.pro' создает группу с именем 'Товары'
And пользователь с адресом электронной почты 'androidpos@lighthouse.pro' создает продукт с именем 'Товар1', еденицами измерения 'кг.', штрихкодом '111111111', НДС '10', ценой закупки '150' и ценой продажи '250' в группе с именем 'Товары'
And пользователь с адресом электронной почты 'androidpos@lighthouse.pro' создает продукт с именем 'Вар2', еденицами измерения 'литр', штрихкодом '22222222', НДС '0', ценой закупки '110' и ценой продажи '130' в группе с именем 'Товары'
And пользователь с адресом электронной почты 'androidpos@lighthouse.pro' создает продукт с именем 'Товар3', еденицами измерения 'пятюни', штрихкодом '33333333', НДС '18', ценой закупки '80' и ценой продажи '100' в группе с именем 'Товары'