package project.lighthouse.autotests.jbehave.api;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.Given;
import org.json.JSONException;
import project.lighthouse.autotests.objects.api.Category;
import project.lighthouse.autotests.objects.api.Group;
import project.lighthouse.autotests.objects.api.SubCategory;
import project.lighthouse.autotests.steps.api.administrator.UserApiSteps;
import project.lighthouse.autotests.steps.api.commercialManager.CatalogApiSteps;
import project.lighthouse.autotests.steps.api.commercialManager.StoreApiSteps;
import project.lighthouse.autotests.steps.api.departmentManager.InvoiceApiSteps;

import java.io.IOException;

public class EndCatalogUserSteps {

    @Steps
    CatalogApiSteps catalogApiSteps;

    @Steps
    UserApiSteps userApiSteps;

    @Steps
    StoreApiSteps storeApiSteps;

    @Given("there is the group with name '$groupName'")
    public void givenThereIsTheGroupWithName(String groupName) throws IOException, JSONException {
        catalogApiSteps.createGroupThroughPost(groupName);
    }

    @Given("there is the category with name '$categoryName' related to group named '$groupName'")
    public void givenThereIsTheCategoryWithNameRelatedToGroup(String categoryName, String groupName) throws IOException, JSONException {
        catalogApiSteps.createCategoryThroughPost(categoryName, groupName);
    }

    @Given("the user navigates to the group with name '$groupName'")
    public void givenTheUserNavigatesToTheGroup(String groupName) throws JSONException {
        catalogApiSteps.navigateToGroupPage(groupName);
    }

    @Given("the user navigates to the category with name '$categoryName' related to group named '$groupName'")
    public void givenTheUserNavigatesToTheCategoryPage(String categoryName, String groupName) throws JSONException {
        catalogApiSteps.navigateToCategoryPage(categoryName, groupName);
    }

    @Given("there is the subCategory with name '$subCategoryName' related to group named '$groupName' and category named '$categoryName'")
    public void givenThereIsTheSubCategory(String groupName, String categoryName, String subCategoryName) throws IOException, JSONException {
        catalogApiSteps.createSubCategoryThroughPost(groupName, categoryName, subCategoryName);
    }

    @Given("there is the subCategory with rounding set to '$rounding' with name '$subCategoryName' related to group named '$groupName' and category named '$categoryName'")
    public void createSubCategoryThroughPost(String rounding, String groupName, String categoryName, String subCategoryName) throws IOException, JSONException {
        catalogApiSteps.createSubCategoryThroughPost(groupName, categoryName, subCategoryName, rounding);
    }

    @Given("the user navigates to the subCategory '$subCategoryName', category '$categoryName', group '$groupName' product list page")
    public void navigateToSubCategoryProductListPageUrl(String subCategoryName, String categoryName, String groupName) throws JSONException {
        catalogApiSteps.navigateToSubCategoryProductListPageUrl(subCategoryName, categoryName, groupName);
    }

    @Given("the user sets subCategory '$subCategoryName' mark up with max '$maxValue' and min '$minValue' values")
    public void givenTheUSerSetsSubCategoryMarkUp(String subCategoryName, String maxValue, String minValue) throws IOException, JSONException {
        catalogApiSteps.setSubCategoryMarkUp(maxValue, minValue, subCategoryName);
    }

    @Given("the user navigates to the default subCategory product list page")
    public void givenTheUserNavigatesToTheDefaultSubCategory() throws JSONException, IOException {
        before();
        navigateToSubCategoryProductListPageUrl(SubCategory.DEFAULT_NAME, Category.DEFAULT_NAME, Group.DEFAULT_NAME);
    }

    public void before() throws IOException, JSONException {
        userApiSteps.getUser(InvoiceApiSteps.DEFAULT_USER_NAME);
        catalogApiSteps.promoteDepartmentManager(storeApiSteps.createStoreThroughPost(), InvoiceApiSteps.DEFAULT_USER_NAME);
    }
}
