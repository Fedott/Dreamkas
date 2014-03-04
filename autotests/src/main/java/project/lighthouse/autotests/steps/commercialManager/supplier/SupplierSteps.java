package project.lighthouse.autotests.steps.commercialManager.supplier;

import net.thucydides.core.annotations.Step;
import net.thucydides.core.steps.ScenarioSteps;
import org.apache.commons.io.FileUtils;
import org.jbehave.core.model.ExamplesTable;
import org.junit.Assert;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.helper.FileCreator;
import project.lighthouse.autotests.helper.FilesCompare;
import project.lighthouse.autotests.helper.StringGenerator;
import project.lighthouse.autotests.objects.web.supplier.SupplierObject;
import project.lighthouse.autotests.pages.commercialManager.supplier.SupplierListPage;
import project.lighthouse.autotests.pages.commercialManager.supplier.SupplierPage;

import java.io.File;
import java.net.URL;

public class SupplierSteps extends ScenarioSteps {

    SupplierPage supplierPage;
    SupplierListPage supplierListPage;

    private File file;
    private String fileName;

    private String supplierName;

    @Step
    public void openSupplierCreatePage() {
        supplierPage.open();
    }

    @Step
    public void openSupplierListPage() {
        supplierListPage.open();
    }

    @Step
    public void input(ExamplesTable examplesTable) {
        supplierPage.inputTable(examplesTable);
    }

    @Step
    public void input(String elementName, String value) {
        supplierPage.input(elementName, value);
    }

    @Step
    public void createButtonClick() {
        supplierPage.getCreateButtonFacade().click();
    }

    @Step
    public void cancelButtonClick() {
        supplierPage.getCancelButtonLinkFacade().click();
    }

    @Step
    public void labelsCheck(String elementName) {
        supplierPage.checkFieldLabel(elementName);
    }

    @Step
    public void checkFieldLength(String elementName, int number) {
        supplierPage.checkFieldLength(elementName, number);
    }

    @Step
    public void generateString(String elementName, int number) {
        String generatedData = new StringGenerator(number).generateTestData();
        supplierPage.input(elementName, generatedData);
        supplierName = generatedData;
    }

    @Step
    public void contains(String locator) {
        supplierListPage.getSupplierObjectCollection().contains(locator);
    }

    @Step
    public void supplierObjectCollectionNotContains(String locator) {
        supplierListPage.getSupplierObjectCollection().notContains(locator);
    }

    @Step
    public void supplierCollectionObjectClickByLocator(String locator) {
        supplierListPage.getSupplierObjectCollection().clickByLocator(locator);
    }

    @Step
    public void supplierObjectCollectionContainsStoredValue() {
        supplierListPage.getSupplierObjectCollection().contains(supplierName);
    }

    @Step
    public void assertUploadFileButtonIsClickable() {
        supplierPage.getWaiter().elementToBeClickable(
                supplierPage.getUploadForm().getUploadButton().getFindBy());
    }

    @Step
    public void assertReplaceFileButtonIsClickable() {
        supplierPage.getWaiter().elementToBeClickable(
                supplierPage.getUploadForm().getReplaceFileButton().getFindBy());
    }

    @Step
    public void uploadFile(String fileName, int size) {
        File file = new FileCreator(fileName, size).create();
        supplierPage.getUploadForm().uploadFile(file);
        this.file = file;
        this.fileName = fileName;
    }

    @Step
    public void assertCreateButtonIsDisabled() {
        if (!supplierPage.getCreateButtonFacade().isDisable()) {
            Assert.fail("The supplier create button is not disabled");
        }
    }

    @Step
    public void assertCancelButtonIsDisabled() {
        if (!supplierPage.getCancelButtonLinkFacade().isDisable()) {
            Assert.fail("The supplier cancel button is not disabled");
        }
    }

    @Step
    public void waitForUploadComplete() {
        supplierPage.getUploadForm().waitForUploadComplete();
    }

    @Step
    public void assertUploadedFileName() {
        Assert.assertEquals(fileName, supplierPage.getUploadForm().getUploadedFileName());
    }

    @Step
    public void assertDownloadedFileEqualsToUploadedFile() throws Exception {
        String downloadLocation = supplierPage.getUploadForm().getUploadedFileNameLinkWebElement().getAttribute("href");
        if (downloadLocation.trim().equals("")) {
            throw new Exception("The element you have specified does not link to anything!");
        }
        String downloadPath = System.getProperty("java.io.tmpdir");
        URL downloadURL = new URL(downloadLocation);
        File downloadedFile = new File(downloadPath + "/" + fileName);
        FileUtils.copyURLToFile(downloadURL, new File(downloadPath + "/" + fileName));
        Boolean compareResult = new FilesCompare(file, downloadedFile).compare();
        if (!compareResult) {
            Assert.fail("md5 sum is not equals!");
        }
    }

    @Step
    public void assertDownloadAgreementButtonIsVisibleFromSupplierObjectByLocator(String locator) {
        supplierListPage.getWaiter().getVisibleWebElement(
                getDownloadAgreementButtonFromSupplierObjectByLocator(locator));
    }

    @Step
    public void assertDownloadAgreementButtonIsNotVisibleFromSupplierObjectByLocator(String locator) {
        supplierListPage.getWaiter().invisibilityOfElementLocated(
                getDownloadAgreementButtonFromSupplierObjectByLocator(locator));
    }

    private WebElement getDownloadAgreementButtonFromSupplierObjectByLocator(String locator) {
        return ((SupplierObject) supplierListPage
                .getSupplierObjectCollection()
                .getAbstractObjectByLocator(locator))
                .getDownloadAgreementButtonWebElement();
    }
}
