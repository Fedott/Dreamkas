package ru.dreamkas.pages.stockMovement.modal;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import ru.dreamkas.apihelper.DateTimeHelper;
import ru.dreamkas.collection.abstractObjects.AbstractObjectCollection;
import ru.dreamkas.elements.bootstrap.buttons.PrimaryBtnFacade;
import ru.dreamkas.elements.items.DateInput;
import ru.dreamkas.elements.items.Input;
import ru.dreamkas.elements.items.NonType;
import ru.dreamkas.elements.items.SelectByVisibleText;
import ru.dreamkas.elements.items.autocomplete.ProductAutoComplete;
import ru.dreamkas.handler.field.FieldChecker;
import ru.dreamkas.pages.modal.ModalWindowPage;

public abstract class StockMovementModalPage extends ModalWindowPage {

    public StockMovementModalPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
        put("date", getCustomDateInput());
        put("store", new SelectByVisibleText(this, "//*[@name='store']"));
        put("product.name", new ProductAutoComplete(this, "//*[@name='product.name' and contains(@class, 'input')]"));
        put("price", new Input(this, "//*[@name='price']"));
        put("quantity", new Input(this, "//*[@name='quantity']"));

        put("заголовок успешного удаления", new NonType(this, "//*[@name='successRemoveTitle']"));
    }

    public abstract AbstractObjectCollection getProductCollection();

    public String getTotalSum() {
        String xpath = String.format("%s//*[contains(@class, 'totalPrice') and @name='totalPrice']", modalWindowXpath());
        return findVisibleElement(By.xpath(xpath)).getText();
    }

    public void addProductButtonClick() {
        clickInTheModalWindowByXpath("//*[contains(@class, 'btn btn-default')]");
    }

    public abstract Integer getProductRowsCount();

    @Override
    public void deleteButtonClick() {
        String xpath = String.format("%s//*[@class='removeLink']", modalWindowXpath());
        findVisibleElement(By.xpath(xpath)).click();
    }

    protected void confirmationOkClick(String buttonLabel) {
        new PrimaryBtnFacade(this, buttonLabel).click();
    }

    protected void deleteButtonClick(String cssClass) {
        String xpath = String.format("%s//*[@class='removeLink %s']", modalWindowXpath(), cssClass);
        findVisibleElement(By.xpath(xpath)).click();
    }

    public void confirmDeleteButtonClick() {
        String xpath = String.format("%s//*[@class='confirmLink__confirmation']//*[contains(@class, '__removeLink')]", modalWindowXpath());
        findVisibleElement(By.xpath(xpath)).click();
    }

    protected Integer getProductRowsCount(String tableClass) {
        String cssSelector = String.format("table.%s tbody>tr", tableClass);
        return getDriver().findElements(By.cssSelector(cssSelector)).size();
    }

    @Override
    public String getTitle() {
        return findVisibleElement(By.xpath(modalWindowXpath() + "//*[@class='modal__title']")).getText();
    }

    private DateInput getCustomDateInput() {
        return new DateInput(this, "//*[@name='date']") {

            @Override
            public void setValue(String value) {
                String convertedValue = DateTimeHelper.getDate(value);
                super.setValue(convertedValue);
            }

            @Override
            public FieldChecker getFieldChecker() {
                return new FieldChecker(this) {

                    @Override
                    public void assertValueEqual(String expectedValue) {
                        String convertedValue = DateTimeHelper.getDate(expectedValue);
                        super.assertValueEqual(convertedValue);
                    }
                };
            }
        };
    }
}
