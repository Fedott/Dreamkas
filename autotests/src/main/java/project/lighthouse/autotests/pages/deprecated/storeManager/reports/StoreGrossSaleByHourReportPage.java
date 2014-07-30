package project.lighthouse.autotests.pages.deprecated.storeManager.reports;

import net.thucydides.core.annotations.DefaultUrl;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.objects.web.deprecated.reports.storeGrossSaleBuHour.StoreGrossSaleByHourElementCollection;

@DefaultUrl("/stores/storeId/reports/reports/grossSalesByHours")
public class StoreGrossSaleByHourReportPage extends CommonPageObject {

    public StoreGrossSaleByHourReportPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
    }

    public StoreGrossSaleByHourElementCollection getStoreGrossSaleByHourElementCollection() {
        return new StoreGrossSaleByHourElementCollection(getDriver(), By.name("storeGrossSalesByHourRow"));
    }
}
