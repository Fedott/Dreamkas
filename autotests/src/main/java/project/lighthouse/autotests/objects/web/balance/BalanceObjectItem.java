package project.lighthouse.autotests.objects.web.balance;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.objects.web.abstractObjects.AbstractObjectNode;

import java.util.Map;

public class BalanceObjectItem extends AbstractObjectNode {

    private String sku;
    private String name;
    private String barcode;
    private String inventory;
    private String averageDailySales;
    private String inventoryDays;
    private String averagePurchasePrice;
    private String lastPurchasePrice;

    public BalanceObjectItem(WebElement element) {
        super(element);
    }

    @Override
    public void setProperties() {
        sku = getElement().findElement(By.xpath(".//*[@model-attribute='product.sku']")).getText();
        name = getElement().findElement(By.xpath(".//*[@model-attribute='product.name']")).getText();
        barcode = getElement().findElement(By.xpath(".//*[@model-attribute='product.barcode']")).getText();
        inventory = getElement().findElement(By.xpath(".//*[@model-attribute='inventoryElement']")).getText();
        averageDailySales = getElement().findElement(By.xpath(".//*[@model-attribute='averageDailySalesElement']")).getText();
        inventoryDays = getElement().findElement(By.xpath(".//*[@model-attribute='inventoryDaysElement']")).getText();
        averagePurchasePrice = getElement().findElement(By.xpath(".//*[@model-attribute='averagePurchasePriceElement']")).getText();
        lastPurchasePrice = getElement().findElement(By.xpath(".//*[@model-attribute='lastPurchasePriceElement']")).getText();
    }

    @Override
    public Boolean rowIsEqual(Map<String, String> row) {
        return sku.equals(row.get("sku")) &&
                name.equals(row.get("name")) &&
                barcode.equals(row.get("barcode")) &&
                inventory.equals(row.get("inventory")) &&
                averageDailySales.equals(row.get("averageDailySales")) &&
                inventoryDays.equals(row.get("inventoryDays")) &&
                averagePurchasePrice.equals(row.get("averagePurchasePrice")) &&
                lastPurchasePrice.equals(row.get("lastPurchasePrice"));
    }

    @Override
    public String getObjectLocator() {
        return sku;
    }
}
