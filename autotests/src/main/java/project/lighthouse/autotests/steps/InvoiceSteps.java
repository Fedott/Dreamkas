package project.lighthouse.autotests.steps;

import net.thucydides.core.annotations.Step;
import net.thucydides.core.pages.Pages;
import net.thucydides.core.steps.ScenarioSteps;
import org.jbehave.core.model.ExamplesTable;
import org.json.JSONException;
import project.lighthouse.autotests.common.CommonPage;
import project.lighthouse.autotests.pages.elements.DateTime;
import project.lighthouse.autotests.pages.invoice.InvoiceApi;
import project.lighthouse.autotests.pages.invoice.InvoiceBrowsing;
import project.lighthouse.autotests.pages.invoice.InvoiceCreatePage;
import project.lighthouse.autotests.pages.invoice.InvoiceListPage;
import project.lighthouse.autotests.pages.product.ProductApi;

import java.io.IOException;

public class InvoiceSteps extends ScenarioSteps {

    InvoiceCreatePage invoiceCreatePage;
    InvoiceListPage invoiceListPage;
    InvoiceBrowsing invoiceBrowsing;
    InvoiceApi invoiceApi;
    ProductApi productApi;
    CommonPage commonPage;

    public InvoiceSteps(Pages pages) {
        super(pages);
    }

    @Step
    public void createInvoiceThroughPost(String invoiceName) throws JSONException, IOException {
        invoiceApi.createInvoiceThroughPost(invoiceName);
    }

    @Step
    public void createInvoiceThroughPostWithData(String invoiceName, String productName, String productSku, String productBarCode, String productUnits) throws JSONException, IOException {
        productApi.сreateProductThroughPost(productName, productSku, productBarCode, productUnits, "123");
        invoiceApi.createInvoiceThroughPost(invoiceName, productName);
    }

    @Step
    public void openInvoiceCreatePage() {
        invoiceCreatePage.open();
    }

    @Step
    public void openInvoiceListPage() {
        invoiceListPage.open();
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
        String generatedData = commonPage.generateTestData(charNumber);
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
    public void acceptChangesButtonClick() {
        invoiceBrowsing.acceptChangesButtonClick();
    }

    @Step
    public void discardChangesButtonClick() {
        invoiceBrowsing.discardChangesButtonClick();
    }

    @Step
    public void acceptDeleteButtonClick() {
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

    public void tryTochildrenItemNavigateAndClickByFindByLocator(String elementName) {
        invoiceBrowsing.tryTochildrenItemNavigateAndClickByFindByLocator(elementName);
    }

    @Step
    public void checkItemIsNotPresent(String elementName) {
        invoiceBrowsing.checkItemIsNotPresent(elementName);
    }
}
