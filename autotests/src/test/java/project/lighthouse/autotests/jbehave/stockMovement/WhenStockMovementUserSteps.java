package project.lighthouse.autotests.jbehave.stockMovement;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.Alias;
import org.jbehave.core.annotations.When;
import org.jbehave.core.model.ExamplesTable;
import org.json.JSONException;
import project.lighthouse.autotests.steps.stockMovement.StockMovementSteps;

public class WhenStockMovementUserSteps {

    @Steps
    StockMovementSteps stockMovementSteps;

    @When("the user clicks on the accept products button")
    @Alias("пользователь нажимает на кнопку Принять от поставщика")
    public void whenTheUserClicksOnTheAcceptProductButton() {
        stockMovementSteps.acceptProductsButtonClick();
    }

    @When("the user inputs values on the create new invoice modal window $examplesTable")
    @Alias("пользователь вводит данные в модальном окне создания накладной $examplesTable")
    public void whenTheUserInputsOnTheCreateNewInvoiceModalWindow(ExamplesTable examplesTable) {
        stockMovementSteps.invoiceCreateModalWindowInput(examplesTable);
    }

    @When("the user inputs values on the edit invoice modal window $examplesTable")
    @Alias("пользователь вводит данные в модальном окне редактирования накладной $examplesTable")
    public void whenTheUserInputsOnTheEditInvoiceModalWindow(ExamplesTable examplesTable) {
        stockMovementSteps.invoiceEditModalWindowWindowInput(examplesTable);
    }

    @When("the user clicks on the paid check box")
    @Alias("пользователь нажимает на галочку Оплачено")
    public void whenTheUserClicksOnThePaidCheckBox() {
        stockMovementSteps.paidCheckBoxClick();
    }

    @When("the user clicks on the paid check box in the edit invoice modal window")
    @Alias("пользователь нажимает на галочку Оплачено в модальном окне редактирования накладной")
    public void whenTheUserClicksOnThePaidCheckBoxInTheEditInvoiceModalWindow() {
        stockMovementSteps.invoiceEditModalWindowPaidCheckBoxClick();
    }

    @When("the user clicks on the add new invoice product button")
    @Alias("пользователь нажимает на кнопку добавления нового товара в накладную")
    public void whenTheUserClicksOnTheAddNewInvoiceProductButton() {
        stockMovementSteps.addProductToInvoiceButtonClick();
    }

    @When("the user clicks on the add new invoice product button in the edit invoice modal window")
    @Alias("пользователь нажимает на кнопку добавления нового товара в накладную в модальном окне редактирования накладной")
    public void whenTheUserClicksOnTheAddNewInvoiceProductButtonInTheEditModalWIndow() {
        stockMovementSteps.invoiceEditModalWindowAddProductToInvoiceButtonClick();
    }

    @When("the user clicks on the invoice accept button")
    @Alias("пользователь нажимает на кнопку Принять, чтобы принять накладную с товарами")
    public void whenTheUserClicksOnTheInvoiceAcceptButton() {
        stockMovementSteps.acceptInvoiceButtonClick();
    }

    @When("the user clicks on the invoice save button in the edit invoice modal window")
    @Alias("пользователь нажимает на кнопку сохранения накладной в модальном окне редактирования накладной")
    public void whenTheUserClicksOnTheInvoiceSaveButton() {
        stockMovementSteps.saveInvoiceButtonClick();
    }

    @When("the user clicks on the last created invoice from builder steps on the stock movement page")
    @Alias("пользователь нажимает на последнюю созданнаю накладную через builder steps на странице товароджвижения")
    public void whenTheUserClicksOnTheLastCreatedInvoiceFromBuilderStepsOnTheStockMovementPage() throws JSONException {
        stockMovementSteps.openLastCreatedInvoiceInStockMovementPage();
    }

    @When("the user clicks on the invoice with number '$number' on the stock movement page")
    @Alias("пользователь нажимает на накладную с номером '$number' на странице товародвижения")
    public void whenTheUserClicksOnTheInvoiceWithName(String number) throws JSONException {
        stockMovementSteps.openInvoiceByNumberInStockMovementPage(number);
    }

    @When("the user clicks on delete invoice button in edit invoice modal window")
    @Alias("пользователь нажимает на кнопку удаления накладной в модальном окне редактирования накладной")
    public void whenTheUserClicksOnDeleteInvoiceButtonInEditInvoiceModalWindow() {
        stockMovementSteps.deleteInvoiceLinkClick();
    }

    @When("the user clicks on delete invoice confirm button in edit invoice modal window")
    @Alias("пользователь подтверждает удаление накладной в модальном окне редактирования накладной")
    public void whenTheUserClicksOnDeleteInvoiceConfirmButtonInEditInvoiceModalWindow() {
        stockMovementSteps.confirmDeleteInvoiceLinkClick();
    }

    @When("the user deletes the product with name '$name' in the edit invoice modal window")
    @Alias("пользователь удаляет товар с названием '$name' в модальном окне редактирования накладной")
    public void whenTheUserDeletesTheProductWithNameInTheEditInvoiceModalWindow(String name) {
        stockMovementSteps.invoiceProductWithNameDeleteIconClick(name);
    }
}
