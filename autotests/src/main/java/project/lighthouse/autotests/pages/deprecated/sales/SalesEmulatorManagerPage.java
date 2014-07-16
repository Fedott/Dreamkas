package project.lighthouse.autotests.pages.deprecated.sales;

import net.thucydides.core.annotations.DefaultUrl;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.elements.items.Autocomplete;
import project.lighthouse.autotests.elements.items.Input;

@DefaultUrl("/sale")
public class SalesEmulatorManagerPage extends CommonPageObject {

    public SalesEmulatorManagerPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
        put("sales autocomplete field", new Autocomplete(this, By.xpath("//*[@lh_product_autocomplete='name']")));
        put("sales quantity", new Input(this, "quantity[]"));
        put("sales purchasePrice", new Input(this, "sellingPrice[]"));
    }

    public void addToSales() {
        String addToSalesButtonXpath = "//*[@class='button saleBox__addProductLink']/input";
        findBy(addToSalesButtonXpath).click();
    }

    public void makePurchase() {
        String makePurchaseButtonXpath = "//*[@class='button button_color_blue']/input";
        findBy(makePurchaseButtonXpath).click();
        getCommonActions().checkAlertText("Продано!");
    }
}

