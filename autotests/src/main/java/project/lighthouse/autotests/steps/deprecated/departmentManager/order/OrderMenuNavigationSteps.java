package project.lighthouse.autotests.steps.deprecated.departmentManager.order;

import net.thucydides.core.annotations.Step;
import net.thucydides.core.steps.ScenarioSteps;
import project.lighthouse.autotests.pages.deprecated.departmentManager.order.menu.OrderPageMenuNavigation;

public class OrderMenuNavigationSteps extends ScenarioSteps {

    OrderPageMenuNavigation orderPageMenuNavigation;

    @Step
    public void createOrderLinkClick() {
        orderPageMenuNavigation.createOrderLinkClick();
    }
}