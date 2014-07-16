package project.lighthouse.autotests.pages.deprecated.storeManager.reports;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.objects.web.grossMargin.GrossMarginTableObjectCollection;

public class StoreGrossSaleMarginReportPage extends CommonPageObject {

    public StoreGrossSaleMarginReportPage(WebDriver driver) {
        super(driver);
    }

    public GrossMarginTableObjectCollection getGrossMarginTableObjectCollection() {
        return new GrossMarginTableObjectCollection(getDriver(), By.name("grossMarginRow"));
    }

    public String getReportName() {
        return findVisibleElement(By.xpath("//*[@class='page__data']/h2")).getText();
    }

    @Override
    public void createElements() {
    }
}
