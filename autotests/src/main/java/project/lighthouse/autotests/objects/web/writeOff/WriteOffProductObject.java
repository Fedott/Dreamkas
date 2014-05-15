package project.lighthouse.autotests.objects.web.writeOff;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import project.lighthouse.autotests.objects.web.abstractObjects.AbstractObject;
import project.lighthouse.autotests.objects.web.abstractObjects.ObjectProperty;
import project.lighthouse.autotests.objects.web.abstractObjects.objectInterfaces.ObjectLocatable;
import project.lighthouse.autotests.objects.web.abstractObjects.objectInterfaces.ResultComparable;
import project.lighthouse.autotests.objects.web.compare.CompareResults;

import java.util.Map;

public class WriteOffProductObject extends AbstractObject implements ObjectLocatable, ResultComparable {

    private ObjectProperty name;
    private ObjectProperty sku;
    private ObjectProperty barcode;
    private ObjectProperty quantity;
    private ObjectProperty units;
    private ObjectProperty price;
    private ObjectProperty productCause;

    public WriteOffProductObject(WebElement element) {
        super(element);
    }

    public WriteOffProductObject(WebElement element, WebDriver webDriver) {
        super(element, webDriver);
    }

    @Override
    public void setProperties() {
        name = setObjectProperty("productName", By.name("productName"));
        sku = setObjectProperty("productSku", By.name("productSku"));
        barcode = setObjectProperty("productBarcode", By.name("productBarcode"));
        quantity = setObjectProperty("productAmount", By.name("productAmount"));
        units = setObjectProperty("productUnits", By.name("productUnits"));
        price = setObjectProperty("productPrice", By.name("productPrice"));
        productCause = setObjectProperty("productCause", By.name("productCause"));
    }

    @Override
    public CompareResults getCompareResults(Map<String, String> row) {
        return new CompareResults()
                .compare("productSku", sku.getText(), row.get("productSku"))
                .compare("productName", name.getText(), row.get("productName"))
                .compare("productBarcode", barcode.getText(), row.get("productBarcode"))
                .compare("productAmount", quantity.getText(), row.get("productAmount"))
                .compare("productUnits", units.getText(), row.get("productUnits"))
                .compare("productPrice", price.getText(), row.get("productPrice"))
                .compare("productCause", productCause.getText(), row.get("productCause"));
    }

    @Override
    public String getObjectLocator() {
        return name.getText();
    }

    public void clickDeleteButton() {
        new Actions(getWebDriver())
                .moveToElement(getElement())
                .click(getElement().findElement(By.xpath(".//*[@class='writeOff__removeLink']")))
                .build()
                .perform();
    }
}
