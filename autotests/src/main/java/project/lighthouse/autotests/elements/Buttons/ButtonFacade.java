package project.lighthouse.autotests.elements.Buttons;

import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.CommonActions;

public class ButtonFacade {

    WebDriver webDriver;
    String xpath;
    private static final String BUTTON_XPATH = "//*[@class='button button_color_blue']";

    CommonActions commonActions;

    public ButtonFacade(WebDriver webDriver) {
        this.webDriver = webDriver;
        this.xpath = BUTTON_XPATH;
        commonActions = new CommonActions(webDriver);
    }

    public void click() {
        commonActions.spanElementClick(xpath);
    }
}
