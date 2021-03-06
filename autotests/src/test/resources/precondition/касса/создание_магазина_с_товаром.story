Meta:
@smoke
@precondition
@sprint_41
@us_111.1
@us_111.2
@us_111.3

Scenario: Сценарий для создания магазина c товаром через консольную команду

Given пользователь с адресом электронной почты 's41u1111@lighthouse.pro' создает магазин с именем 'store-s41u1111' и адресом 'адрес'
Given пользователь с адресом электронной почты 's41u1111@lighthouse.pro' создает группу с именем 'pos-group1'
And пользователь с адресом электронной почты 's41u1111@lighthouse.pro' создает продукт с именем 'pos-product1', еденицами измерения 'шт.', штрихкодом 'post-barcode-1', НДС '0', ценой закупки '100' и ценой продажи '110' в группе с именем 'pos-group1'