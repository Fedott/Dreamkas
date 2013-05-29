package project.lighthouse.autotests.steps;

import net.thucydides.core.annotations.Step;
import net.thucydides.core.pages.Pages;
import net.thucydides.core.steps.ScenarioSteps;
import org.jbehave.core.model.ExamplesTable;
import org.json.JSONException;
import project.lighthouse.autotests.common.CommonPage;

import java.io.IOException;

public class CommonSteps extends ScenarioSteps {

    CommonPage commonPage;

    public CommonSteps(Pages pages) {
        super(pages);
    }

    @Step
    public void checkTheRequiredPageIsOpen(String pageObjectName) {
        commonPage.isRequiredPageOpen(pageObjectName);
    }

    @Step
    public void checkErrorMessages(ExamplesTable errorMessageTable) {
        commonPage.checkErrorMessages(errorMessageTable);
    }

    @Step
    public void checkNoErrorMessages() {
        commonPage.checkNoErrorMessages();
    }

    @Step
    public void checkNoErrorMessages(ExamplesTable errorMessageTable) {
        commonPage.checkNoErrorMessages(errorMessageTable);
    }

    @Step
    public void checkAutoCompleteNoResults() {
        commonPage.checkAutoCompleteNoResults();
    }

    @Step
    public void checkAutoCompleteResults(ExamplesTable checkValuesTable) {
        commonPage.checkAutoCompleteResults(checkValuesTable);
    }

    @Step
    public void createProductPostRequestSend(String name, String sku, String barcode, String units, String purchasePrice) throws JSONException, IOException {
        commonPage.сreateProductThroughPost(name, sku, barcode, units, purchasePrice);
    }

    @Step
    public void createInvoiceThroughPost(String invoiceName) throws JSONException, IOException {
        commonPage.createInvoiceThroughPost(invoiceName);
    }

    @Step
    public void createInvoiceThroughPostWithData(String invoiceName, String productName) throws JSONException, IOException {
        commonPage.createInvoiceThroughPost(invoiceName, productName);
    }

    public void checkAlertText(String expectedText) {
        commonPage.checkAlertText(expectedText);
    }

    public void NoAlertIsPresent() {
        commonPage.NoAlertIsPresent();
    }

    @Step
    public void averagePriceCalculation() {
        commonPage.averagePriceCalculation();
    }

    @Step
    public void createWriteOffThroughPost(String writeOffNumber) throws IOException, JSONException {
        commonPage.createWriteOffThroughPost(writeOffNumber);
    }

    @Step
    public void createWriteOffThroughPost(String writeOffNumber, String productName, String productSku, String productBarCode, String productUnits, String purchasePrice,
                                          String quantity, String price, String cause)
            throws IOException, JSONException {
        commonPage.createWriteOffThroughPost(writeOffNumber, productName, productSku, productBarCode, productUnits, purchasePrice, quantity, price, cause);
    }

    @Step
    public void createWriteOffAndNavigateToIt(String writeOffNumber, String productName, String productSku, String productBarCode, String productUnits, String purchasePrice,
                                              String quantity, String price, String cause)
            throws JSONException, IOException {
        commonPage.createWriteOffAndNavigateToIt(writeOffNumber, productName, productSku, productBarCode, productUnits, purchasePrice, quantity, price, cause);
    }

    @Step
    public void createWriteOffAndNavigateToIt(String writeOffNumber)
            throws JSONException, IOException {
        commonPage.createWriteOffAndNavigateToIt(writeOffNumber);
    }

    @Step
    public void navigatoToWriteOffPage(String writeOffNumber) throws JSONException {
        commonPage.navigatoToWriteOffPage(writeOffNumber);
    }

    @Step
    public void createKlassThroughPost(String klassName) throws IOException, JSONException {
        commonPage.createKlassThroughPost(klassName);
    }

    @Step
    public void createGroupThroughPost(String groupName, String klassName) throws IOException, JSONException {
        commonPage.createGroupThroughPost(groupName, klassName);
    }

    @Step
    public void navigateToKlassPage(String klassName) throws JSONException {
        commonPage.navigateToKlassPage(klassName);
    }
}
