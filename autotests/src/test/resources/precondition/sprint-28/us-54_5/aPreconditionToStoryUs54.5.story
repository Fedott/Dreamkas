Meta:
@sprint_28
@us_54.5
@smoke
@id_s28u54.5s1
@id_s28u54.5s2

Scenario: A scenario that prepares data

Given the user runs the symfony:env:init command

Given there is the user with name 'storeManager-s28u544', position 'storeManager-s28u544', username 'storeManager-s28u544', password 'lighthouse', role 'storeManager'
Given there is the user with name 'departmentManager-s28u544', position 'departmentManager-s28u544', username 'departmentManager-s28u544', password 'lighthouse', role 'departmentManager'

Given there is the store with number '28544' managed by 'storeManager-s28u544'
Given there is the store with number '28544' managed by department manager named 'departmentManager-s28u544'

And there is the subCategory with name 'defaultSubCategory-s28u544' related to group named 'defaultGroup-s28u544' and category named 'defaultCategory-s28u544'
And the user sets subCategory 'defaultSubCategory-s28u544' mark up with max '10' and min '0' values

Given there is the product with 'name-28544' name, '28544' barcode, 'unit' units, '100' purchasePrice of group named 'defaultGroup-s28u544', category named 'defaultCategory-s28u544', subcategory named 'defaultSubCategory-s28u544'


