Meta:
@sprint_30
@us_63
@order

Narrative:
Тесты на видимость

Scenario: Cannot view create order page navigation through link by commercialManager

Meta:
@id_s30u63s15

Given the user opens order create page
And the user logs in as 'commercialManager'

Then the user sees the 403 error


Scenario: Cannot view create order page navigation through link by storeManager

Meta:
@id_s30u63s16

Given the user opens order create page
And the user logs in as 'storeManager'

Then the user sees the 403 error

Scenario: Cannot view create order page navigation through link by administrator

Meta:
@id_s30u63s17

Given the user opens order create page
And the user logs in as 'watchman'

Then the user sees the 403 error

Scenario: No orders menu navigation link for commercialManager

Meta:
@id_s30u63s18

Given the user opens the authorization page
And the user logs in as 'commercialManager'

Then the user checks the orders navigation menu item is not visible

Scenario: No orders menu navigation link for storeManager

Meta:
@id_s30u63s19

Given the user opens the authorization page
And the user logs in as 'storeManager'

Then the user checks the orders navigation menu item is not visible

Scenario: No orders menu navigation link for administrator

Meta:
@id_s30u63s20

Given the user opens the authorization page
And the user logs in as 'watchman'

Then the user checks the orders navigation menu item is not visible
