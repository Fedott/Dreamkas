package project.lighthouse.autotests.pages.departmentManager.invoice;

import net.thucydides.core.annotations.DefaultUrl;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.CommonViewInterface;
import project.lighthouse.autotests.common.CommonView;
import project.lighthouse.autotests.elements.Buttons.ButtonFacade;

@DefaultUrl("/invoices")
public class InvoiceListPage extends InvoiceCreatePage {

    public static final String ITEM_NAME = "invoice";
    private static final String ITEM_SKU_NAME = "sku";

    CommonViewInterface commonViewInterface = new CommonView(getDriver(), ITEM_NAME, ITEM_SKU_NAME);

    public InvoiceListPage(WebDriver driver) {
        super(driver);
    }

    public void invoiceListItemCreate() {
        new ButtonFacade(getDriver(), "Новая накладная").click();
    }

    public void listItemClick(String skuValue) {
        commonViewInterface.itemClick(skuValue);
    }

    public void listItemCheck(String skuValue) {
        commonViewInterface.itemCheck(skuValue);
    }

    public void checkInvoiceListItemWithSkuHasExpectedValue(String skuValue, String elementName, String expectedValue) {
        commonViewInterface.checkListItemWithSkuHasExpectedValue(skuValue, elementName, expectedValue);
    }
}
