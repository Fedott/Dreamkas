package project.lighthouse.autotests.pages.catalog;

import net.thucydides.core.annotations.DefaultUrl;
import net.thucydides.core.annotations.NotImplementedException;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.elements.bootstrap.buttons.DefaultBtnFacade;
import project.lighthouse.autotests.objects.web.catalog.GroupObjectCollection;

/**
 * Catalog page object
 */
@DefaultUrl("/catalog")
public class CatalogPage extends CommonPageObject {

    public CatalogPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
    }

    public void addGroupButtonClick() {
        new DefaultBtnFacade(this, "Добавить группу").click();
    }

    public GroupObjectCollection getGroupObjectCollection() {
        return new GroupObjectCollection(getDriver(), By.name("group"));
    }

    public String getGroupTitle() {
        return findVisibleElement(By.name("groupHeading")).getText();
    }

    public String getTitle() {
        return findVisibleElement(By.className("page-title")).getText();
    }

    public void editGroupIconClick() {
        throw new NotImplementedException("Not implemented yet");
    }
}
