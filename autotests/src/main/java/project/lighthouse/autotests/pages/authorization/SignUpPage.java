package project.lighthouse.autotests.pages.authorization;

import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.elements.Buttons.ButtonFacade;
import project.lighthouse.autotests.elements.items.Input;

public class SignUpPage extends CommonPageObject {

    public SignUpPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
        put("email", new Input(this, "email"));
    }

    public void signUpButtonClick() {
        new ButtonFacade(this, "Зарегестрироваться").click();
    }
}
