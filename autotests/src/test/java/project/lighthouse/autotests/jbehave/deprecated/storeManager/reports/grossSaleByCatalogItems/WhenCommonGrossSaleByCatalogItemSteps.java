package project.lighthouse.autotests.jbehave.deprecated.storeManager.reports.grossSaleByCatalogItems;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.When;
import project.lighthouse.autotests.steps.deprecated.storeManager.reports.grossSaleByCatalogItems.CommonGrossSaleByCatalogItemSteps;

public class WhenCommonGrossSaleByCatalogItemSteps {

    @Steps
    CommonGrossSaleByCatalogItemSteps commonGrossSaleByCatalogItemSteps;

    @When("the user clicks the catalog item with name '$name'")
    public void whenTheUserClicksTheCatalogItemWithName(String name) {
        commonGrossSaleByCatalogItemSteps.clickByLocator(name);
    }
}
