package project.lighthouse.autotests.objects.web.log;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.Waiter;
import project.lighthouse.autotests.objects.web.abstractObjects.AbstractObjectCollection;
import project.lighthouse.autotests.objects.web.product.InvoiceListObject;

import java.util.List;

public class SimpleLogObjectCollection extends AbstractObjectCollection {
    public SimpleLogObjectCollection(WebDriver webDriver, By findBy) {
        super(webDriver, findBy);
    }

    @Override
    public void init(WebDriver webDriver, By findBy) {
        List<WebElement> webElementList = new Waiter(webDriver).getVisibleWebElements(findBy);
        for (WebElement element : webElementList) {
            SimpleLogObject simpleLogObject = new SimpleLogObject(element, webDriver);
            add(simpleLogObject);
        }
    }

    @Override
    public InvoiceListObject createNode(WebElement element) {
        return null;
    }
}
