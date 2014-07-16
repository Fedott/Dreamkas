package project.lighthouse.autotests.pages.deprecated.commercialManager.oraganization;

import net.thucydides.core.annotations.DefaultUrl;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.common.CommonPageObject;

@DefaultUrl("/company")
public class OrganizationListPage extends CommonPageObject {
    public OrganizationListPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
        put("name");
    }

    public void clickCreateNewOrganizationLink() {
        click(By.linkText("Добавить организацию"));
    }

    public void clickOrganizationListItemByName(String name) {
        click(By.linkText(name));
    }
}
