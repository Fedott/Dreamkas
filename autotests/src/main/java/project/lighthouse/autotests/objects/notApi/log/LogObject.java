package project.lighthouse.autotests.objects.notApi.log;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.Waiter;
import project.lighthouse.autotests.objects.notApi.abstractObjects.AbstractObjectNode;

public class LogObject extends AbstractObjectNode {

    String id;
    String type;
    String status;
    String title;
    String statusText;
    String product;
    String message;


    public LogObject(WebElement element, WebDriver webDriver) {
        super(element, webDriver);
    }

    public String getType() {
        return type;
    }

    public String getStatus() {
        return status;
    }

    public String getTitle() {
        return title;
    }

    public String getProduct() {
        return product;
    }

    public String getMessage() {
        return message;
    }

    @Override
    public void setProperties() {
        Waiter waiter = new Waiter(getWebDriver(), 0);
        id = getElement().getAttribute("id");
        type = getElement().getAttribute("type");
        status = getElement().getAttribute("status");
        title = getElement().findElement(By.xpath(".//*[@class='log__title']")).getText();
        product = null;
        if (!waiter.invisibilityOfElementLocated(By.xpath(".//*[@class='log__productName']"))) {
            product = getElement().findElement(By.xpath(".//*[@class='log__productName']")).getText();
        }
        statusText = getElement().findElement(By.xpath(".//*[@class='log__status']")).getText();
        message = getElement().findElement(By.xpath(".//*[@class='log__finalMessage']")).getText();
    }
}
