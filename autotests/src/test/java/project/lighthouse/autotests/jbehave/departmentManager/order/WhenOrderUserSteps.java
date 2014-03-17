package project.lighthouse.autotests.jbehave.departmentManager.order;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.When;
import org.jbehave.core.model.ExamplesTable;
import project.lighthouse.autotests.steps.departmentManager.order.OrderSteps;

public class WhenOrderUserSteps {

    @Steps
    OrderSteps orderSteps;

    @When("the user inputs values on order page $examplesTable")
    public void whenTheUserInputsValuesOnOrderPage(ExamplesTable examplesTable) {
        orderSteps.input(examplesTable);
    }

    @When("the user inputs values in addition new product form on the order page $examplesTable")
    public void whenTheUserInputsValuesInAdditionNewProductFormOnTheOrderPage(ExamplesTable examplesTable) {
        orderSteps.additionFormInput(examplesTable);
    }

    @When("the user inputs value in elementName '$elementName' in addition new product form on the order page")
    public void whenTheUserInputsValuesInAdditionNewProductFormOnTheOrderPage(String value, String elementName) {
        orderSteps.additionFormInput(elementName, value);
    }

    @When("the user clicks the save order button")
    public void whenTheUserClicksTheCreateOrderButton() {
        orderSteps.saveButtonClick();
    }

    @When("the user clicks the add order product button")
    public void whenTheUserClicksTheAddOrderProductButton() {
        orderSteps.addProductToOrderButtonClick();
    }
}
