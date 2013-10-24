package project.lighthouse.autotests.objects.product;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.objects.product.abstractObjects.AbstractProductObjectList;

import java.util.Map;

public class ProductWriteOffListObject extends AbstractProductObjectList {

    private String acceptanceDateFormatted;
    private String quantity;
    private String priceFormatted;
    private String totalPriceFormatted;

    public String getNumber() {
        return number;
    }

    private String number;

    public ProductWriteOffListObject(WebElement element) {
        super(element);
        setProperties();
    }

    @Override
    public String getValues() {
        return String.format("%s, %s, %s, %s", acceptanceDateFormatted, quantity, priceFormatted, totalPriceFormatted);
    }

    public void setProperties() {
        acceptanceDateFormatted = element.findElement(By.xpath(".//*[@model_attr='createdDateFormatted']")).getText();
        quantity = element.findElement(By.xpath(".//*[@model_attr='quantity']")).getText();
        priceFormatted = element.findElement(By.xpath(".//*[@model_attr='priceFormatted']")).getText();
        totalPriceFormatted = element.findElement(By.xpath(".//*[@model_attr='totalPriceFormatted']")).getText();
        number = element.getAttribute("writeoff-number");
    }

    public Boolean rowIsEqual(Map<String, String> row) {
        return acceptanceDateFormatted.equals(row.get("createdDateFormatted")) &&
                quantity.equals(row.get("quantity")) &&
                priceFormatted.equals(row.get("priceFormatted")) &&
                totalPriceFormatted.equals(row.get("totalPriceFormatted"));
    }
}
