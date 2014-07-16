package project.lighthouse.autotests.steps.deprecated.storeManager.reports;

import net.thucydides.core.annotations.Step;
import net.thucydides.core.steps.ScenarioSteps;
import project.lighthouse.autotests.pages.deprecated.storeManager.reports.ReportsMenuLocalNavigationPage;

public class ReportsMenuLocalNavigationPageSteps extends ScenarioSteps {

    ReportsMenuLocalNavigationPage reportsMenuLocalNavigationPage;

    @Step
    public void grossSalePerHourLinkClick() {
        reportsMenuLocalNavigationPage.grossSalePerHourLinkClick();
    }

    @Step
    public void grossSaleByProductsLinkClick() {
        reportsMenuLocalNavigationPage.grossSaleByProductsLinkClick();
    }

    @Step
    public void storeGrossSaleMarginLinkClick() {
        reportsMenuLocalNavigationPage.storeGrossSaleMarginLinkClick();
    }
}
