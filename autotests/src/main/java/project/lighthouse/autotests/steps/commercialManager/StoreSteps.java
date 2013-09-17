package project.lighthouse.autotests.steps.commercialManager;

import net.thucydides.core.annotations.Step;
import net.thucydides.core.pages.Pages;
import net.thucydides.core.steps.ScenarioSteps;
import org.jbehave.core.model.ExamplesTable;
import org.json.JSONException;
import project.lighthouse.autotests.StaticData;
import project.lighthouse.autotests.UrlHelper;
import project.lighthouse.autotests.objects.Store;
import project.lighthouse.autotests.pages.commercialManager.store.StoreApi;
import project.lighthouse.autotests.pages.commercialManager.store.StoreCardPage;
import project.lighthouse.autotests.pages.commercialManager.store.StoreCreatePage;
import project.lighthouse.autotests.pages.commercialManager.store.StoreListPage;

import java.io.IOException;
import java.util.Map;

public class StoreSteps extends ScenarioSteps {

    StoreCreatePage storeCreatePage;
    StoreListPage storeListPage;
    StoreCardPage storeCardPage;
    StoreApi storeApi;

    public StoreSteps(Pages pages) {
        super(pages);
    }

    @Step
    public void navigateToCreateStorePage() {
        storeCreatePage.open();
    }

    @Step
    public void clickCreateNewStoreButton() {
        storeListPage.createNewStoreButton().click();
    }

    @Step
    public void navigateToStoresListPage() {
        storeListPage.open();
    }

    @Step
    public void clickCreateStoreSubmitButton() {
        storeCreatePage.createButton().click();
    }

    @Step
    public void clickSaveStoreSubmitButton() {
        storeCreatePage.saveButton().click();
    }

    @Step
    public void fillStoreFormData(ExamplesTable formData) {
        storeCreatePage.inputTable(formData);
    }

    @Step
    public void checkStoreDataInList(ExamplesTable storeData) {
        for (Map<String, String> column : storeData.getRows()) {
            storeListPage.findModelFieldContaining(Store.NAME, column.get("elementName"), column.get("value"));
        }
    }

    @Step
    public void clickOnStoreRowInList(String storeNumber) {
        storeListPage.findStoreRowInList(storeNumber).click();
    }

    @Step
    public void checkStoreCardData(ExamplesTable storeData) {
        for (Map<String, String> row : storeData.getRows()) {
            String columnName = row.get("elementName");
            String columnValue = row.get("value");
            if (columnName.equals("number")) {
                storeCardPage.checkStoreCardHeader(columnValue);
            }
            storeCardPage.checkStoreCardValue(columnName, columnValue);
        }

    }

    @Step
    public Store createStore(String number, String address, String contacts) throws IOException, JSONException {
        return storeApi.createStoreThroughPost(number, address, contacts);
    }

    @Step
    public Store createStore() throws IOException, JSONException {
        return storeApi.createStoreThroughPost();
    }

    @Step
    public void navigateToStorePage(String id) {
        storeCardPage.navigateToStoreCardPage(id);
    }

    @Step
    public void navigateToStorePageByNumber(String storeNumber) throws JSONException {
        String storeId = storeApi.getStoreId(storeNumber);
        navigateToStorePage(storeId);
    }

    @Step
    public void userClicksEditButtonOnStoreCardPage() {
        storeCardPage.editButton().click();
    }

    @Step
    public void checkPromotedStoreManager(String storeManager) {
        storeCardPage.checkPromotedStoreManager(storeManager);
    }

    @Step
    public void checkPromotedStoreManagerIsNotPresent(String storeManager) {
        storeCardPage.checkPromotedStoreManagerIsNotPresent(storeManager);
    }

    @Step
    public void unPromoteStoreManager(String storeManager) {
        storeCardPage.unPromoteStoreManager(storeManager);
    }

    @Step
    public void promoteStoreManager(String storeManager) {
        storeCardPage.promoteStoreManager(storeManager);
    }

    @Step
    public void promoteNotStoreManager(String notStoreManager) {
        storeCardPage.promoteNotStoreManager(notStoreManager);
    }

    @Step
    public void checkStoreNumber(String number) {
        storeCardPage.checkStoreCardHeader(number);
    }

    @Step
    public void navigatesToTheStoreCatalogPage(String storeName) throws JSONException {
        String url = String.format("%s/catalog?editMode=false&storeId=%s", UrlHelper.getWebFrontUrl(), StaticData.stores.get(storeName).getId());
        getDriver().navigate().to(url);
    }


    @Step
    public void checkPromotedDepartmentManager(String departmentManager) {
        storeCardPage.checkPromotedDepartmentManager(departmentManager);
    }

    @Step
    public void checkPromotedDepartmentManagerIsNotPresent(String departmentManager) {
        storeCardPage.checkPromotedDepartmentManagerIsNotPresent(departmentManager);
    }

    @Step
    public void unPromoteDepartmentManager(String departmentManager) {
        storeCardPage.unPromoteDepartmentManager(departmentManager);
    }

    @Step
    public void promoteDepartmentManager(String departmentManager) {
        storeCardPage.promoteDepartmentManager(departmentManager);
    }

    @Step
    public void promoteNotDepartmentManager(String notDepartmentManager) {
        storeCardPage.promoteNotDepartmentManager(notDepartmentManager);
    }
}
