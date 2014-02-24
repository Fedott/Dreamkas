package project.lighthouse.autotests.elements.Buttons.localNavigation;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.common.CommonActions;

public class NavigationLinkFacade {

    private String xpath;
    private static final String xpathPattern = "//*[contains(@class, 'localNavigation__link') and normalize-space(text())='%s']";

    private CommonActions commonActions;

    public NavigationLinkFacade(WebDriver driver, String linkText) {
        xpath = String.format(xpathPattern, linkText);
        commonActions = new CommonActions(driver);
    }

    public void click() {
        commonActions.elementClick(By.xpath(xpath));
    }
}
