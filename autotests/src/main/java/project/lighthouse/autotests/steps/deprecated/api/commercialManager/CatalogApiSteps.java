package project.lighthouse.autotests.steps.deprecated.api.commercialManager;

import net.thucydides.core.annotations.Step;
import org.json.JSONException;
import project.lighthouse.autotests.StaticData;
import project.lighthouse.autotests.api.ApiConnect;
import project.lighthouse.autotests.objects.api.Category;
import project.lighthouse.autotests.objects.api.Group;
import project.lighthouse.autotests.objects.api.Store;
import project.lighthouse.autotests.objects.api.SubCategory;
import project.lighthouse.autotests.storage.Storage;
import project.lighthouse.autotests.storage.containers.user.UserContainer;

import java.io.IOException;

public class CatalogApiSteps extends OwnerApi {

    private static final String STORE_MANAGERS_REL_VALUE = "storeManagers";
    private static final String DEPARTMENT_MANAGERS_REL_VALUE = "departmentManagers";

    @Step
    public Group createGroupThroughPost(String groupName) throws IOException, JSONException {
        Group group = new Group(groupName);
        return apiConnect.createGroupThroughPost(group);
    }

    @Step
    public Group createGroupThroughPostByUserWithEmail(String groupName, String email) throws IOException, JSONException {
        Group group = new Group(groupName);
        UserContainer userContainer = Storage.getUserVariableStorage().getUserContainers().getContainerWithEmail(email);
        return new ApiConnect(userContainer.getEmail(), userContainer.getPassword()).createGroupThroughPost(group);
    }

    @Step
    public Category createCategoryThroughPost(String categoryName, String groupName) throws IOException, JSONException {
        Group group = createGroupThroughPost(groupName);
        Category category = new Category(categoryName, group.getId());
        return apiConnect.createCategoryThroughPost(category, group);
    }

    @Step
    public Category createCategoryThroughPostByUserWithEmail(String categoryName, String groupName, String email) throws IOException, JSONException {
        Group group = createGroupThroughPostByUserWithEmail(groupName, email);
        Category category = new Category(categoryName, group.getId());
        UserContainer userContainer = Storage.getUserVariableStorage().getUserContainers().getContainerWithEmail(email);
        return new ApiConnect(userContainer.getEmail(), userContainer.getPassword()).createCategoryThroughPost(category, group);
    }

    @Step
    public void navigateToCategoryPage(String categoryName, String groupName) throws JSONException {
        String categoryPageUrl = apiConnect.getCategoryPageUrl(categoryName, groupName);
        getDriver().navigate().to(categoryPageUrl);
    }

    @Step
    public SubCategory createSubCategoryThroughPost(String groupName, String categoryName, String subCategoryName) throws IOException, JSONException {
        Group group = createGroupThroughPost(groupName);
        Category category = createCategoryThroughPost(categoryName, groupName);
        SubCategory subCategory = new SubCategory(subCategoryName, category.getId());
        return apiConnect.createSubCategoryThroughPost(subCategory, category, group);
    }

    @Step
    public SubCategory createSubCategoryThroughPostByUserWithEmail(String groupName, String categoryName, String subCategoryName, String email) throws IOException, JSONException {
        Group group = createGroupThroughPostByUserWithEmail(groupName, email);
        Category category = createCategoryThroughPostByUserWithEmail(categoryName, groupName, email);
        SubCategory subCategory = new SubCategory(subCategoryName, category.getId());
        UserContainer userContainer = Storage.getUserVariableStorage().getUserContainers().getContainerWithEmail(email);
        return new ApiConnect(userContainer.getEmail(), userContainer.getPassword()).createSubCategoryThroughPost(subCategory, category, group);
    }

    @Step
    public SubCategory createSubCategoryThroughPost(String groupName, String categoryName, String subCategoryName, String rounding) throws IOException, JSONException {
        Group group = createGroupThroughPost(groupName);
        Category category = createCategoryThroughPost(categoryName, groupName);
        SubCategory subCategory = new SubCategory(subCategoryName, category.getId(), rounding);
        return apiConnect.createSubCategoryThroughPost(subCategory, category, group);
    }

    @Step
    public void navigateToSubCategoryProductListPageUrl(String subCategoryName, String categoryName, String groupName) throws JSONException {
        String subCategoryProductListPageUrl = apiConnect.getSubCategoryProductListPageUrl(subCategoryName, categoryName, groupName);
        getDriver().navigate().to(subCategoryProductListPageUrl);
    }

    @Step
    public void navigateToSubCategoryProductListPageUrlWihEditModeOn(String subCategoryName, String categoryName, String groupName) throws JSONException {
        String subCategoryProductListPageUrl = apiConnect.getSubCategoryProductListPageUrl(subCategoryName, categoryName, groupName) + "?editMode=true";
        getDriver().navigate().to(subCategoryProductListPageUrl);
    }

    @Step
    public void navigateToSubCategoryProductCreatePageUrl(String subCategoryName) throws JSONException {
        String subCategoryProductCreatePageUrl = apiConnect.getSubCategoryProductCreatePageUrl(subCategoryName);
        getDriver().navigate().to(subCategoryProductCreatePageUrl);
    }

    @Step
    public void setSubCategoryMarkUp(String retailMarkupMax, String retailMarkupMin, String subCategoryName) throws IOException, JSONException {
        apiConnect.setSubCategoryMarkUp(retailMarkupMax, retailMarkupMin, StaticData.subCategories.get(subCategoryName));
    }

    @Step
    public void setSubCategoryMarkUpByUserWithEmail(String retailMarkupMax,
                                                    String retailMarkupMin,
                                                    String subCategoryName,
                                                    String email) throws IOException, JSONException {
        UserContainer userContainer = Storage.getUserVariableStorage().getUserContainers().getContainerWithEmail(email);
        new ApiConnect(userContainer.getEmail(), userContainer.getPassword())
                .setSubCategoryMarkUp(retailMarkupMax, retailMarkupMin, StaticData.subCategories.get(subCategoryName));
    }

    @Deprecated
    @Step
    public void promoteStoreManager(Store store, String userName) throws IOException, JSONException {
        apiConnect.promoteUserToManager(store, StaticData.users.get(userName), STORE_MANAGERS_REL_VALUE);
    }

    @Deprecated
    @Step
    public void promoteDepartmentManager(Store store, String userName) throws IOException, JSONException {
        apiConnect.promoteUserToManager(store, StaticData.users.get(userName), DEPARTMENT_MANAGERS_REL_VALUE);
    }

    @Step
    public SubCategory createDefaultSubCategoryThroughPost() throws IOException, JSONException {
        return createSubCategoryThroughPost(Group.DEFAULT_NAME, Category.DEFAULT_NAME, SubCategory.DEFAULT_NAME);
    }
}
