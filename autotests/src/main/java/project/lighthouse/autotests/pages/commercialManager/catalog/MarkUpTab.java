package project.lighthouse.autotests.pages.commercialManager.catalog;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.elements.Buttons.ButtonFacade;
import project.lighthouse.autotests.elements.InputOnlyVisible;
import project.lighthouse.autotests.elements.SelectByVisibleText;
import project.lighthouse.autotests.elements.preLoader.PreLoader;

import static junit.framework.Assert.assertEquals;

public class MarkUpTab extends CommonPageObject {

    public MarkUpTab(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
        items.put("retailMarkupMin", new InputOnlyVisible(this, "retailMarkupMin"));
        items.put("retailMarkupMax", new InputOnlyVisible(this, "retailMarkupMax"));
        items.put("rounding", new SelectByVisibleText(this, "rounding.name"));
    }

    public void saveMarkUpButtonClick() {
        new ButtonFacade(getDriver(), "Сохранить").catalogClick();
        new PreLoader(getDriver()).await();
    }

    public WebElement getSuccessMessage() {
        return findVisibleElement(
                By.xpath("//*[@class='form__successMessage']")
        );
    }

    public void checkSuccessMessage(String expectedMessage) {
        assertEquals(
                String.format("Success message is not expected. Actual: '%s', Expected: '%s'", getSuccessMessage().getText(), expectedMessage),
                getSuccessMessage().getText(), expectedMessage
        );
    }

    public void checkDropDownDefaultValue(String expectedValue) {
        WebElement element = items.get("rounding").getVisibleWebElement();
        commonPage.checkDropDownDefaultValue(element, expectedValue);
    }
}
