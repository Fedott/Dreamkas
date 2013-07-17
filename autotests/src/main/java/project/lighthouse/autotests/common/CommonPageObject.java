package project.lighthouse.autotests.common;

import net.thucydides.core.pages.PageObject;
import org.jbehave.core.model.ExamplesTable;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.CommonActions;
import project.lighthouse.autotests.Waiter;

import java.util.HashMap;
import java.util.Map;

abstract public class CommonPageObject extends PageObject {

    protected CommonPage commonPage = new CommonPage(getDriver());

    protected Waiter waiter = new Waiter(getDriver());

    public Map<String, CommonItem> items = new HashMap();

    protected CommonActions commonActions = new CommonActions(getDriver(), items);

    public CommonPageObject(WebDriver driver) {
        super(driver);
        createElements();
    }

    abstract public void createElements();

    public WebElement findElement(By by) {
        return waiter.getPresentWebElement(by);
    }

    public WebElement findVisibleElement(By by) {
        return waiter.getVisibleWebElement(by);
    }

    public void input(String elementName, String inputText) {
        commonActions.input(elementName, inputText);
    }

    public void inputTable(ExamplesTable inputTable) {
        commonActions.inputTable(inputTable);
    }

    public WebElement findOnlyVisibleWebElementFromTheWebElementsList(By findBy) {
        return waiter.getOnlyVisibleElementFromTheList(findBy);
    }

    public WebElement findModelFieldContaining(String modelName, String fieldName, String expectedValue) {
        By by = By.xpath(String.format("//span[@model_name='%s' and @model_attr='%s' and contains(text(), '%s')]", modelName, fieldName, expectedValue));
        return findVisibleElement(by);
    }
}
