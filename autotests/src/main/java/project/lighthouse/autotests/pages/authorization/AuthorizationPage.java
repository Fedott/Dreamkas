package project.lighthouse.autotests.pages.authorization;

import net.thucydides.core.annotations.DefaultUrl;
import org.openqa.selenium.By;
import org.openqa.selenium.Cookie;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.pages.administrator.users.UserCreatePage;

import java.util.HashMap;
import java.util.Map;

import static junit.framework.Assert.*;

@DefaultUrl("/")
public class AuthorizationPage extends UserCreatePage {

    public Map<String, String> users = new HashMap();
    Boolean isAuthorized = false;

    public AuthorizationPage(WebDriver driver) {
        super(driver);
        users();
    }

    public void users() {
        users.put("watchman", "lighthouse");
        users.put("commercialManager", "lighthouse");
        users.put("storeManager", "lighthouse");
        users.put("departmentManager", "lighthouse");
    }

    @Override
    public void createElements() {
    }

    public void authorization(String userName) {
        String password = users.get(userName);
        authorization(userName, password);
    }

    public void authorization(String userName, String password) {
        authorization(userName, password, false);
    }

    public void authorization(String userName, String password, Boolean isFalse) {
        type(By.name("username"), userName);
        type(By.name("password"), password);
        String loginButtonXpath = "//*[@class='button button_color_blue']";
        spanElementClick(loginButtonXpath);
        if (!isFalse) {
            checkUser(userName);
        }
        isAuthorized = true;
    }

    public void logOut() {
        logOutButtonClick();
        isAuthorized = false;
    }

    public void logOutButtonClick() {
        String logOutButtonXpath = "//*[@class='topBar__logoutLink']";
        findVisibleElement(By.xpath(logOutButtonXpath)).click();
    }

    public void beforeScenario() {
        if (isAuthorized) {
            Cookie token = getDriver().manage().getCookieNamed("token");
            if (token != null) {
                getDriver().manage().deleteCookie(token);
            }
        }
    }

    public void checkUser(String userName) {
        String userXpath = "//*[@class='topBar__userName']";
        String actualUserName = find(By.xpath(userXpath)).getText();
        assertEquals(
                String.format("The user name is '%s'. Should be '%s'.", actualUserName, userName),
                userName, actualUserName
        );
    }

    public boolean loginFormIsVisible() {
        return commonPage.isElementVisible(By.id("form_login"));
    }

    public void loginFormIsPresent() {
        assertTrue("The log out is not successful!", loginFormIsVisible());
    }

    public void authorizationFalse(String userName, String password) {
        authorization(userName, password, true);
        isAuthorized = false;
    }

    public void error403IsPresent() {
        try {
            String error404Xpath = getError403Xpath();
            findElement(By.xpath(error404Xpath));
        } catch (Exception e) {
            fail("The error 403 is not present on the page!");
        }
    }

    public String getError403Xpath() {
        return "//body[@class='page page_common_403']";
    }

    public void error403IsNotPresent() {
        try {
            String error404Xpath = getError403Xpath();
            waiter.waitUntilIsNotVisible(By.xpath(error404Xpath));
        } catch (Exception e) {
            fail("The error 403 is present on the page!");
        }
    }
}
