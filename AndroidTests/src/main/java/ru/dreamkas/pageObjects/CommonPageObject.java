package ru.dreamkas.pageObjects;

import net.thucydides.core.annotations.findby.By;
import net.thucydides.core.pages.PageObject;
import net.thucydides.core.webdriver.WebDriverFacade;

import org.openqa.selenium.WebElement;

import java.util.ArrayList;
import java.util.List;

import io.appium.java_client.android.AndroidDriver;

public class CommonPageObject extends PageObject {

    public AndroidDriver getAppiumDriver() {
        WebDriverFacade webDriverFacade = (WebDriverFacade)getDriver();
        return  (AndroidDriver)webDriverFacade.getProxiedDriver();
    }

    public String getCurrentActivity() {
        return getAppiumDriver().currentActivity();
    }

    public void closeApp() {
        getAppiumDriver().closeApp();
    }

    public void launchApp() {
        getAppiumDriver().launchApp();
    }

    public void clickOnElementWithText(List<WebElement> webElements, String text) {
        for (WebElement webElement : webElements) {
            if (webElement.getText().equals(text)) {
                webElement.click();
                break;
            }
        }
        // TODO throw exception if not clicked
    }

    protected List<String> getListViewItemsTitles(WebElement lv, String childClass){
        List<WebElement> items = lv.findElements(By.className(childClass));
        List<String> strs = new ArrayList<String>();
        for (WebElement webElement : items) {
            strs.add(webElement.getText());
        }
        return strs;
    }
}
