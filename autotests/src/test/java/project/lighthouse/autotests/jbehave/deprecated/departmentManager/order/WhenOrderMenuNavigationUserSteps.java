package project.lighthouse.autotests.jbehave.deprecated.departmentManager.order;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.When;
import project.lighthouse.autotests.steps.deprecated.departmentManager.order.OrderMenuNavigationSteps;

public class WhenOrderMenuNavigationUserSteps {

    @Steps
    OrderMenuNavigationSteps orderMenuNavigationSteps;

    @When("the user clicks the create order link on order page menu navigation")
    public void whenTheUserClicksTheCreateOrderLinkOnOrderPageMenuNavigation() {
        orderMenuNavigationSteps.createOrderLinkClick();
    }
}
