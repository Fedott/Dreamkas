Meta:
@sprint_37
@us_80.3

Narrative:
Как владелец торговой точки,
Я хочу добавить в систему данные нескольких счетов своей организации,
Что бы в дальнейшем использовать эти данные в документах

Scenario: Create organization bank account

Meta:
@smoke

Given the user opens the authorization page
And the user logs in as 'owner'
And user have organization with name 'organization-s37u803_1'
And user is on organization page name 'organization-s37u803_1'
When user clicks bank accounts list link
And user clicks create new bank account link
And user fill bank account inputs
 | elementName | value |
 | bic | 123456789 |
 | bankName | ООО "Банк s37u803_1" |
 | bankAddress | Россия, СПб, Корачаево, дом 10 |
 | correspondentAccount | 123456789012345678901234567890 |
 | account | s37u803_1 |
And user clicks create new bank account form button
Then user checks bank account list exists account 's37u803_1' and bank 'ООО "Банк s37u803_1"'
When user clicks to bank accounts in list with bank 'ООО "Банк s37u803_1"'
Then user checks bank account fields data

Scenario: Create organization bank account validation

Given the user opens the authorization page
And the user logs in as 'owner'
And user have organization with name 'organization-s37u803_2'
And user is on organization page name 'organization-s37u803_2'
When user clicks bank accounts list link
And user clicks create new bank account link
And user fill bank account inputs
 | elementName | value |
 | bic | 1234567890 |
 | bankName | length301char01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789T |
 | bankAddress | length301char01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789T |
 | correspondentAccount | length31charr123456789012345678901 |
 | account | length101char0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890 |
And user clicks create new bank account form button
Then user checks bank account form the element field 'bic' has error message 'БИК должен состоять из 9 цифр'
And user checks bank account form the element field 'bankName' has error message 'Не более 300 символов'
And user checks bank account form the element field 'bankAddress' has error message 'Не более 300 символов'
And user checks bank account form the element field 'correspondentAccount' has error message 'Не более 30 символов'
And user checks bank account form the element field 'account' has error message 'Не более 100 символов'
When user fill bank account inputs
 | elementName | value |
 | bic | 12345678 |
 | bankName | valid s37u803_2 |
 | bankAddress | valid s37u803_2 |
 | correspondentAccount | s37u803_2 |
 | account | s37u803_2 |
And user clicks create new bank account form button
Then user checks bank account form the element field 'bic' has error message 'БИК должен состоять из 9 цифр'
When user fill bank account inputs
 | elementName | value |
 | bic | notNumeric |
And user clicks create new bank account form button
Then user checks bank account form the element field 'bic' has error message 'БИК должен состоять из 9 цифр'
When user fill bank account inputs
 | elementName | value |
 | bic | 123456789 |
And user clicks create new bank account form button
Then user checks bank account list exists account 's37u803_2' and bank 'valid s37u803_2'

Scenario: Edit organization bank account

Meta:
@smoke

Given the user opens the authorization page
And the user logs in as 'owner'
And user have organization with name 'organization-s37u803_3'
And user is on organization page name 'organization-s37u803_3'
When user clicks bank accounts list link
And user clicks create new bank account link
And user fill bank account inputs
 | elementName | value |
 | bic | 123456789 |
 | bankName | ООО "Банк s37u803_3" |
 | bankAddress | Россия, СПб, Корачаево, дом 10 |
 | correspondentAccount | 123456789012345678901234567890 |
 | account | s37u803_3 |
And user clicks create new bank account form button
Then user checks bank account list exists account 's37u803_3' and bank 'ООО "Банк s37u803_3"'
When user clicks to bank accounts in list with bank 'ООО "Банк s37u803_3"'
Then user checks bank account fields data
When user fill bank account inputs
 | elementName | value |
 | bic | 987654321 |
 | bankName | s37u803_3 edited |
 | bankAddress | s37u803_3 edited |
 | correspondentAccount | s37u803_3 edited |
 | account | s37u803_3 edited |
And user clicks save bank account form button
Then user checks bank account list exists account 's37u803_3 edited' and bank 's37u803_3 edited'

Scenario: Edit organization bank account validation

Given the user opens the authorization page
And the user logs in as 'owner'
And user have organization with name 'organization-s37u803_4'
And user is on organization page name 'organization-s37u803_4'
When user clicks bank accounts list link
And user clicks create new bank account link
And user fill bank account inputs
 | elementName | value |
 | bic | 123456789 |
 | bankName | ООО "Банк s37u803_4" |
 | bankAddress | Россия, СПб, Корачаево, дом 10 |
 | correspondentAccount | 123456789012345678901234567890 |
 | account | s37u803_4 |
And user clicks create new bank account form button
Then user checks bank account list exists account 's37u803_4' and bank 'ООО "Банк s37u803_4"'
When user clicks to bank accounts in list with bank 'ООО "Банк s37u803_4"'
Then user checks bank account fields data
When user fill bank account inputs
 | elementName | value |
 | bic | 1234567890 |
 | bankName | length301char01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789T |
 | bankAddress | length301char01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789T |
 | correspondentAccount | length31charr123456789012345678901 |
 | account | length101char0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890 |
And user clicks save bank account form button
Then user checks bank account form the element field 'bic' has error message 'БИК должен состоять из 9 цифр'
And user checks bank account form the element field 'bankName' has error message 'Не более 300 символов'
And user checks bank account form the element field 'bankAddress' has error message 'Не более 300 символов'
And user checks bank account form the element field 'correspondentAccount' has error message 'Не более 30 символов'
And user checks bank account form the element field 'account' has error message 'Не более 100 символов'
When user fill bank account inputs
 | elementName | value |
 | bic | 12345678 |
 | bankName | valid s37u803_4 |
 | bankAddress | valid s37u803_4 |
 | correspondentAccount | s37u803_4 |
 | account | s37u803_4 |
And user clicks save bank account form button
Then user checks bank account form the element field 'bic' has error message 'БИК должен состоять из 9 цифр'
When user fill bank account inputs
 | elementName | value |
 | bic | notNumeric |
And user clicks save bank account form button
Then user checks bank account form the element field 'bic' has error message 'БИК должен состоять из 9 цифр'
When user fill bank account inputs
 | elementName | value |
 | bic | 123456789 |
And user clicks save bank account form button
Then user checks bank account list exists account 's37u803_4' and bank 'valid s37u803_4'

Scenario: Check list bank accounts

Given the user opens the authorization page
And the user logs in as 'owner'
And user have organization with name 'organization-s37u803_5'
And user is on organization page name 'organization-s37u803_5'
When user clicks bank accounts list link
And user clicks create new bank account link
And user fill bank account inputs
 | elementName | value |
 | bic | 123456789 |
 | bankName | ООО "Банк s37u803_5_1" |
 | bankAddress | Россия, СПб, Корачаево, дом 10 |
 | correspondentAccount | 123456789012345678901234567890 |
 | account | s37u803_5_1 |
And user clicks create new bank account form button
And user clicks create new bank account link
And user fill bank account inputs
 | elementName | value |
 | bic | 123456789 |
 | bankName | ООО "Банк s37u803_5_2" |
 | bankAddress | Россия, СПб, Корачаево, дом 10 |
 | correspondentAccount | 123456789012345678901234567890 |
 | account | s37u803_5_2 |
And user clicks create new bank account form button
And user clicks create new bank account link
And user fill bank account inputs
 | elementName | value |
 | bic | 123456789 |
 | bankName | ООО "Банк s37u803_5_3" |
 | bankAddress | Россия, СПб, Корачаево, дом 10 |
 | correspondentAccount | 123456789012345678901234567890 |
 | account | s37u803_5_3 |
And user clicks create new bank account form button
Then user checks bank account list exists account 's37u803_5_1' and bank 'ООО "Банк s37u803_5_1"'
And user checks bank account list exists account 's37u803_5_2' and bank 'ООО "Банк s37u803_5_2"'
And user checks bank account list exists account 's37u803_5_3' and bank 'ООО "Банк s37u803_5_3"'