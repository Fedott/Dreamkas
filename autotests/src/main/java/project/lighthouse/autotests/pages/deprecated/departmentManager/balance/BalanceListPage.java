package project.lighthouse.autotests.pages.deprecated.departmentManager.balance;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.objects.web.deprecated.balance.BalanceObjectCollection;

public class BalanceListPage extends CommonPageObject {

    public BalanceListPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
    }

    public BalanceObjectCollection getBalanceObjectCollection() {
        return new BalanceObjectCollection(getDriver(), By.name("inventoryItem"));
    }

    public void balanceTabClick() {
        click(By.xpath("//*[@rel='balance']"));
    }
}
