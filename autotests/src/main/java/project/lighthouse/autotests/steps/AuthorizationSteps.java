package project.lighthouse.autotests.steps;

import net.thucydides.core.annotations.Step;
import net.thucydides.core.pages.Pages;
import net.thucydides.core.steps.ScenarioSteps;
import project.lighthouse.autotests.pages.administrator.users.UserCardPage;
import project.lighthouse.autotests.pages.authorization.AuthorizationPage;
import project.lighthouse.autotests.pages.commercialManager.product.ProductCardView;
import project.lighthouse.autotests.pages.commercialManager.product.ProductListPage;

import static junit.framework.Assert.fail;

public class AuthorizationSteps extends ScenarioSteps {

    AuthorizationPage authorizationPage;
    ProductCardView productCardView;
    ProductListPage productListPage;
    UserCardPage userCardPage;

    public AuthorizationSteps(Pages pages) {
        super(pages);
    }

    @Step
    public void authorization(String userName) {
        authorizationPage.authorization(userName);
    }

    @Step
    public void authorization(String userName, String password) {
        authorizationPage.authorization(userName, password);
    }

    @Step
    public void logOut() {
        authorizationPage.logOut();
    }

    @Step
    public void beforeScenario() {
        authorizationPage.beforeScenario();
    }

    @Step
    public void checkUser(String userName) {
        authorizationPage.checkUser(userName);
    }

    @Step
    public void openPage() {
        authorizationPage.open();
    }

    @Step
    public void loginFormIsPresent() {
        authorizationPage.loginFormIsPresent();
    }

    @Step
    public void authorizationFalse(String userName, String password) {
        authorizationPage.authorizationFalse(userName, password);
    }

    @Step
    public void error403IsPresent() {
        authorizationPage.error403IsPresent();
    }

    @Step
    public void error404isPresent() {
        authorizationPage.error404isPresent();
    }

    @Step
    public void error403IsNotPresent() {
        authorizationPage.error403IsNotPresent();
    }

    @Step
    public void editProductButtonIsNotPresent() {
        try {
            productCardView.editButtonClick();
            fail("Edit product link is present!");
        } catch (Exception ignored) {
        }
    }

    @Step
    public void newProductCreateButtonIsNotPresent() {
        try {
            productListPage.createNewProductButtonClick();
            fail("Create new product button is present on product list page!");
        } catch (Exception ignored) {
        }
    }

    @Step
    public void userCardEditButtonIsPresent() {
        userCardPage.editButtonClick();
    }

    @Step
    public void userCardEditButtonIsNotPresent() {
        try {
            userCardPage.editButtonClick();
            fail("User card edit link is present!");
        } catch (Exception ignored) {
        }
    }

    @Step
    public void userCardListLinkIsPresent() {
        userCardPage.pageBackLink();
    }

    @Step
    public void userCardListLinkIsNotPresent() {
        try {
            userCardPage.pageBackLink();
            fail("User card list link is present!");
        } catch (Exception ignored) {
        }
    }
}
