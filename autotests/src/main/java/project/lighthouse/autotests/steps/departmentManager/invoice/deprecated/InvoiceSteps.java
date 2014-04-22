package project.lighthouse.autotests.steps.departmentManager.invoice.deprecated;

import junit.framework.Assert;
import net.thucydides.core.annotations.Step;
import net.thucydides.core.steps.ScenarioSteps;
import org.jbehave.core.model.ExamplesTable;
import org.json.JSONException;
import org.openqa.selenium.By;
import org.openqa.selenium.ElementNotVisibleException;
import project.lighthouse.autotests.StaticData;
import project.lighthouse.autotests.elements.items.DateTime;
import project.lighthouse.autotests.elements.preLoader.CheckBoxPreloader;
import project.lighthouse.autotests.helper.StringGenerator;
import project.lighthouse.autotests.helper.UrlHelper;
import project.lighthouse.autotests.objects.api.Store;
import project.lighthouse.autotests.pages.departmentManager.invoice.deprecated.InvoiceBrowsing;
import project.lighthouse.autotests.pages.departmentManager.invoice.deprecated.InvoiceCreatePage;
import project.lighthouse.autotests.pages.departmentManager.invoice.deprecated.InvoiceListPage;
import project.lighthouse.autotests.pages.departmentManager.invoice.deprecated.InvoiceSearchPage;

public class InvoiceSteps extends ScenarioSteps {

    InvoiceCreatePage invoiceCreatePage;
    InvoiceListPage invoiceListPage;
    InvoiceBrowsing invoiceBrowsing;

    //TODO moved invoiceSearchPage out
    InvoiceSearchPage invoiceSearchPage;

    @Step
    public void openInvoiceListPage() throws JSONException {
        Store store = StaticData.stores.get(Store.DEFAULT_NUMBER);
        openStoreInvoiceListPage(store);
    }

    @Step
    public void openStoreInvoiceListPage(Store store) throws JSONException {
        String invoiceListPageUrl = String.format(
                "%s/stores/%s/invoices",
                UrlHelper.getWebFrontUrl(),
                store.getId());
        getDriver().navigate().to(invoiceListPageUrl);
    }

    @Step
    public void invoiceListItemCreate() {
        invoiceListPage.invoiceListItemCreate();
    }

    @Step
    public void input(String elementName, String inputText) {
        invoiceCreatePage.input(elementName, inputText);
    }

    @Step
    public void fieldInput(ExamplesTable examplesTable) {
        invoiceBrowsing.fieldInput(examplesTable);
    }

    @Step
    public void listItemCheck(String skuValue) {
        invoiceListPage.listItemCheck(skuValue);
    }

    @Step
    public void checkInvoiceListItemWithSkuHasExpectedValue(String skuValue, String elementName, String expectedValue) {
        invoiceListPage.checkInvoiceListItemWithSkuHasExpectedValue(skuValue, elementName, expectedValue);
    }

    @Step
    public void checkCardValue(String elementName, String expectedValue) {
        invoiceBrowsing.checkCardValue(elementName, expectedValue);
    }

    @Step
    public void checkCardValue(String checkType, String elementName, String expectedValue) {
        invoiceBrowsing.checkCardValue(checkType, elementName, expectedValue);
    }

    @Step
    public void checkCardValue(String checkType, ExamplesTable checkValuesTable) {
        invoiceBrowsing.checkCardValue(checkType, checkValuesTable);
    }

    @Step
    public void editButtonClick() {
        invoiceBrowsing.editButtonClick();
    }

    @Step
    public void listItemClick(String skuValue) {
        invoiceListPage.listItemClick(skuValue);
    }

    @Step
    public void generateTestCharData(String elementName, int charNumber) {
        String generatedData = new StringGenerator(charNumber).generateTestData();
        input(elementName, generatedData);
    }

    @Step
    public void checkFieldLength(String elementName, int fieldLength) {
        invoiceCreatePage.checkFieldLength(elementName, fieldLength);
    }

    @Step
    public void checkTheDateisNowDate(String elementName) {
        String NowDate = DateTime.getTodayDate(DateTime.DATE_TIME_PATTERN);
        invoiceBrowsing.shouldContainsText(elementName, NowDate);
    }

    @Step
    public void goToTheaAdditionOfProductsLinkClick() {
        invoiceBrowsing.goToTheaAdditionOfProductsLinkClick();
    }

    @Step
    public void addOneMoreProductLinkClick() {
        invoiceBrowsing.addOneMoreProductLinkClick();
    }

    @Step
    public void invoiceProductListItemCheck(String value) {
        invoiceBrowsing.listItemCheck(value);
    }

    @Step
    public void invoiceProductListItemClick(String value) {
        invoiceBrowsing.listItemClick(value);
    }

    @Step
    public void checkListItemWithSkuHasExpectedValue(String value, ExamplesTable checkValuesTable) {
        invoiceBrowsing.checkListItemWithSkuHasExpectedValue(value, checkValuesTable);
    }

    @Step
    public void elementClick(String elementName) {
        invoiceBrowsing.elementClick(elementName);
    }

    @Step
    public void acceptChangesButtonClick() throws InterruptedException {
        invoiceBrowsing.acceptChangesButtonClick();
    }

    @Step
    public void discardChangesButtonClick() {
        invoiceBrowsing.discardChangesButtonClick();
    }

    @Step
    public void acceptDeleteButtonClick() throws InterruptedException {
        invoiceBrowsing.acceptDeleteButtonClick();
    }

    @Step
    public void discardDeleteButtonClick() {
        invoiceBrowsing.discardDeleteButtonClick();
    }

    @Step
    public void invoiceStopEditButtonClick() {
        invoiceBrowsing.writeOffStopEditButtonClick();
    }

    @Step
    public void invoiceStopEditlinkClick() {
        invoiceBrowsing.writeOffStopEditlinkClick();
    }

    @Step
    public void childrenElementClick(String elementName, String elementClassName) {
        invoiceBrowsing.childrenElementClick(elementName, elementClassName);
    }

    @Deprecated
    @Step
    public void childrentItemClickByFindByLocator(String parentElementName, String elementName) {
        invoiceBrowsing.childrentItemClickByFindByLocator(parentElementName, elementName);
    }

    @Step
    public void addNewInvoiceProductButtonClick() {
        invoiceBrowsing.addNewInvoiceProductButtonClick();
    }

    @Step
    public void childrenItemNavigateAndClickByFindByLocator(String elementName) {
        invoiceBrowsing.childrenItemNavigateAndClickByFindByLocator(elementName);
    }

    @Step
    public void tryChildrenItemNavigateAndClickByFindByLocator(String elementName) {
        try {
            invoiceBrowsing.childrenItemNavigateAndClickByFindByLocator(elementName);
            String message = String.format("Invoice product with sku '%s' is deleted, but should not!", elementName);
            Assert.fail(message);
        } catch (ElementNotVisibleException ignored) {
        }
    }

    @Step
    public void checkItemIsNotPresent(String elementName) {
        invoiceBrowsing.checkItemIsNotPresent(elementName);
    }

    @Step
    public void searchInput(String searchInput) {
        invoiceSearchPage.input("skuOrSupplierInvoiceSku", searchInput);
    }

    @Step
    public void searchButtonClick() {
        invoiceSearchPage.searchButtonClick();
    }

    @Step
    public void objectPropertyClick(String objectLocator, String objectPropertyName) {
        invoiceBrowsing.getInvoiceProductsCollection().clickPropertyByLocator(objectLocator, objectPropertyName);
    }

    @Step
    public void objectPropertyInput(String locator, String propertyName, String value) {
        invoiceBrowsing.getInvoiceProductsCollection().inputPropertyByLocator(locator, propertyName, value);
    }

    @Step
    public void compareWithExampleTable(ExamplesTable examplesTable) {
        invoiceBrowsing.getInvoiceProductsCollection().compareWithExampleTable(examplesTable);
    }

    @Step
    public void itemClick(String itemName) {
        invoiceBrowsing.itemClick(itemName);
    }

    @Step
    public void checkTheStateOfCheckBox(String itemName, String state) {
        String checkBoxState = invoiceBrowsing.getItemAttribute(itemName, "checked");
        switch (state) {
            case "checked":
                if (checkBoxState != null) {
                    if (!checkBoxState.equals("true")) {
                        Assert.fail("CheckBox is not checked!");
                    }
                } else {
                    Assert.fail("CheckBox is not checked!");
                }
                break;
            case "unChecked":
                if (checkBoxState != null) {
                    Assert.fail("CheckBox is not unchecked!");
                }
                break;
        }
    }

    @Step
    public void checkTheCheckBoxText(String itemName, String text) {
        String actualText = invoiceBrowsing.getItems().get(itemName).getVisibleWebElement().findElement(By.xpath(".//..")).getText();
        Assert.assertEquals(text, actualText);
    }

    @Step
    public void checkBoxPreLoaderWait() {
        new CheckBoxPreloader(getDriver()).await();
    }

    @Step
    public void checkFormResultsText(String text) {
        Assert.assertEquals(text, invoiceSearchPage.getFormResultsText());
    }
}
