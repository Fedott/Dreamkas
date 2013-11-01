package project.lighthouse.autotests.pages.departmentManager.catalog.product;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.elements.NonType;
import project.lighthouse.autotests.objects.notApi.product.InvoiceListCollection;

public class ProductInvoicesList extends CommonPageObject {

    public ProductInvoicesList(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
        items.put("acceptanceDateFormatted", new NonType(this, "acceptanceDateFormatted"));
        items.put("quantity", new NonType(this, "quantity"));
        items.put("priceFormatted", new NonType(this, "priceFormatted"));
        items.put("totalPriceFormatted", new NonType(this, "totalPriceFormatted"));
    }

    public InvoiceListCollection getProductInvoiceListObjects() {
        return new InvoiceListCollection(getDriver(), By.name("invoice"));
    }

    public void invoiceSkuClick(String sku) {
        getProductInvoiceListObjects().clickByLocator(sku);
    }
}
