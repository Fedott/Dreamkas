package project.lighthouse.autotests.pages;

import net.thucydides.core.pages.PageObject;
import org.openqa.selenium.WebDriver;

public class CommonPage extends PageObject {

    public CommonPage(WebDriver driver) {
        super(driver);
    }

    public void isRequiredPageOpen(String pageObjectName){
        String defaultUrl = GetPageObjectDefaultUrl(pageObjectName).substring(50, 64);
        String actualUrl = getDriver().getCurrentUrl();
        if(!actualUrl.contains(defaultUrl)){
            String errorMessage = String.format("The %s is not open!\nActual url: %s\nExpected url: %s", pageObjectName, actualUrl, defaultUrl);
            throw new AssertionError(errorMessage);
        }
    }

    public String GetPageObjectDefaultUrl(String pageObjectName){
        switch (pageObjectName){
            case "ProductListPage":
                return ProductListPage.class.getAnnotations()[0].toString();
            default:
                return String.valueOf(new AssertionError("No such value!"));
        }
    }

}
