package project.lighthouse.autotests.pages.common;

import net.thucydides.core.pages.PageObject;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.Waiter;

import java.util.HashMap;
import java.util.Map;

abstract public class CommonPageObject extends PageObject {

    protected CommonPage commonPage = new CommonPage(getDriver());

    protected Waiter waiter = new Waiter(getDriver());

    public Map<String, CommonItem> items = new HashMap();

    public CommonPageObject(WebDriver driver) {
        super(driver);
        createElements();
    }

    abstract public void createElements();

    public void input(String elementName, String inputText) {
        items.get(elementName).setValue(inputText);
    }

    public WebElement findElement(By by) {
        return waiter.getFluenWaitWebElement(by);
    }
}
