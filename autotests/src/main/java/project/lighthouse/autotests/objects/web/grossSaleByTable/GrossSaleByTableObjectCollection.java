package project.lighthouse.autotests.objects.web.grossSaleByTable;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.objects.web.abstractObjects.AbstractObjectCollection;

public class GrossSaleByTableObjectCollection extends AbstractObjectCollection {

    public GrossSaleByTableObjectCollection(WebDriver webDriver, By findBy) {
        super(webDriver, findBy);
    }

    @Override
    public GrossSaleByTableObject createNode(WebElement element) {
        return new GrossSaleByTableObject(element);
    }
}
