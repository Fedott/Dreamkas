package project.lighthouse.autotests.objects.web.product.writeOffList;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.objects.web.abstractObjects.AbstractObject;
import project.lighthouse.autotests.objects.web.abstractObjects.objectInterfaces.ObjectClickable;
import project.lighthouse.autotests.objects.web.abstractObjects.objectInterfaces.ObjectLocatable;
import project.lighthouse.autotests.objects.web.abstractObjects.objectInterfaces.ResultComparable;
import project.lighthouse.autotests.objects.web.compare.CompareResults;

import java.util.Map;

public class WriteOffListObject extends AbstractObject implements ObjectLocatable, ResultComparable, ObjectClickable {

    private String acceptanceDateFormatted;
    private String quantity;
    private String priceFormatted;
    private String totalPriceFormatted;

    private String number;

    public WriteOffListObject(WebElement element) {
        super(element);
    }

    @Override
    public void setProperties() {
        acceptanceDateFormatted = getElement().findElement(By.xpath(".//*[@model-attribute='createdDateFormatted']")).getText();
        quantity = getElement().findElement(By.xpath(".//*[@model-attribute='quantityElement']")).getText();
        priceFormatted = getElement().findElement(By.xpath(".//*[@model-attribute='priceFormatted']")).getText();
        totalPriceFormatted = getElement().findElement(By.xpath(".//*[@model-attribute='totalPriceFormatted']")).getText();
        number = getElement().getAttribute("writeoff-number");
    }

    @Override
    public CompareResults getCompareResults(Map<String, String> row) {
        return new CompareResults()
                .compare("createdDateFormatted", acceptanceDateFormatted, row.get("createdDateFormatted"))
                .compare("quantity", quantity, row.get("quantity"))
                .compare("priceFormatted", priceFormatted, row.get("priceFormatted"))
                .compare("totalPriceFormatted", totalPriceFormatted, row.get("totalPriceFormatted"));
    }

    @Override
    public String getObjectLocator() {
        return number;
    }

    @Override
    public void click() {
        getElement().click();
    }
}
