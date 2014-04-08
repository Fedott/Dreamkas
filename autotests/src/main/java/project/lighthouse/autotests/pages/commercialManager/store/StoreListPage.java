package project.lighthouse.autotests.pages.commercialManager.store;

import net.thucydides.core.annotations.DefaultUrl;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.elements.Buttons.ButtonFacade;
import project.lighthouse.autotests.elements.items.NonType;

@DefaultUrl("/stores")
public class StoreListPage extends CommonPageObject {

    public StoreListPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
        put("number", new NonType(this, "number"));
        put("address", new NonType(this, "address"));
        put("contacts", new NonType(this, "contacts"));
    }

    public void createNewStoreButtonClick() {
        new ButtonFacade(this, "Новый магазин").click();
    }

    public void checkStoreListItem(String columnName, String expectedValue) {
        findVisibleElement(
                By.xpath(
                        String.format("//span[@model-attribute='%s' and contains(text(), '%s')]", columnName, expectedValue)
                )
        );
    }

    public WebElement findStoreRowInList(String storeNumber) {
        return findVisibleElement(
                By.xpath(
                        String.format("//td/span[@model-attribute='number' and contains(text(), '%s')]/..", storeNumber)
                )
        );
    }
}
