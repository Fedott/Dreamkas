package project.lighthouse.autotests.jbehave.commercialManager.supplier;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.When;
import project.lighthouse.autotests.steps.commercialManager.supplier.SupplierMenuNavigationSteps;

public class WhenSupplierMenuNavigationSteps {

    @Steps
    SupplierMenuNavigationSteps supplierMenuNavigationSteps;

    @When("the user clicks the create supplier link on supplier page menu navigation")
    public void whenTheUSerClicksTheCreateSupplierLinkOnSupplierPageMenuNavigation() {
        supplierMenuNavigationSteps.createSupplierLinkClick();
    }
}
