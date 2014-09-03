package project.lighthouse.autotests.jbehave.deprecated.commercialManager;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.Given;
import org.jbehave.core.annotations.Then;
import org.jbehave.core.annotations.When;
import org.jbehave.core.model.ExamplesTable;
import project.lighthouse.autotests.steps.AuthorizationSteps;
import project.lighthouse.autotests.steps.deprecated.commercialManager.StoreSteps;

public class StoreUserSteps {

    ExamplesTable storeData;

    @Steps
    StoreSteps formSteps;

    @Steps
    AuthorizationSteps authorizationSteps;

    @Given("the user is on create store page")
    public void userIsOnCreateStorePage() {
        formSteps.navigateToCreateStorePage();
    }

    @Given("user is on stores list page")
    public void userIsOnStoresListPage() {
        formSteps.navigateToStoresListPage();
    }

    @When("When user clicks create new store button")
    public void userClicksCreateNewStoreButton() {
        formSteps.clickCreateNewStoreButton();
    }

    @When("user clicks store form create button")
    public void userClicksFormCreateButton() {
        formSteps.clickCreateStoreSubmitButton();
    }

    @When("user clicks store form save button")
    public void userClicksFormSaveButton() {
        formSteps.clickSaveStoreSubmitButton();
    }

    @When("user fills store form with following data $formData")
    public void userFillsFormData(ExamplesTable formData) {
        formSteps.fillStoreFormData(formData);
        storeData = formData;
    }

    @Then("user checks store data in list")
    public void userChecksStoreDataInList() {
    }

    @When("user clicks on store row in list")
    public void userClickOnStoreRowInList() {
        String storeNumber = storeData.getRow(0).get("value");
        formSteps.clickOnStoreRowInList(storeNumber);
    }

    @Then("user checks store card data")
    public void userChecksStoreCardData() {
    }

    @When("user clicks edit button on store card page")
    public void userClicksEditButtonOnStoreCardPage() {
        formSteps.userClicksEditButtonOnStoreCardPage();
    }

    @When("user promotes store manager named '$storeManager'")
    public void whenUserPromotesStoreManager(String storeManager) {
        formSteps.promoteStoreManager(storeManager);
    }

    @When("user unpromotes store manager named '$storeManager'")
    public void whenUserUnPromotesStoreManager(String storeManager) {
        formSteps.unPromoteStoreManager(storeManager);
    }

    @When("user try to promote not store manager named '$notStoreManager'")
    public void whenUserTryToPromoteNotStoreManager(String notStoreManager) {
        formSteps.promoteNotStoreManager(notStoreManager);
    }

    @Then("user checks the promoted store manager is '$storeManager'")
    public void thenTheUserChecksThePromotedStoreManager(String storeManager) {
        formSteps.checkPromotedStoreManager(storeManager);
    }

    @Then("user checks the promoted store manager is not '$storeManager'")
    public void thenTheUserChecksThePromotedStoreManagerIsNot(String storeManager) {
        formSteps.checkPromotedStoreManagerIsNotPresent(storeManager);
    }

    @Then("user checks the store number is eqaul to '$number'")
    public void thenUserChecksTheStoreNumber(String number) {
        formSteps.checkStoreNumber(number);
    }

    @When("user promotes department manager named '$departmentManager'")
    public void whenUserPromotesDepartmentManager(String departmentManager) {
        formSteps.promoteDepartmentManager(departmentManager);
    }

    @When("user unpromotes department manager named '$departmentManager'")
    public void whenUserUnPromotesDepartmentManager(String departmentManager) {
        formSteps.unPromoteDepartmentManager(departmentManager);
    }

    @When("user try to promote not department manager named '$notDepartmentManager'")
    public void whenUserTryToPromoteNotDepartmentManager(String notDepartmentManager) {
        formSteps.promoteNotDepartmentManager(notDepartmentManager);
    }

    @Then("user checks the promoted department manager is '$departmentManager'")
    public void thenTheUserChecksThePromotedDepartmentManager(String departmentManager) {
        formSteps.checkPromotedDepartmentManager(departmentManager);
    }

    @Then("user checks the promoted department manager is not '$departmentManager'")
    public void thenTheUserChecksThePromotedDepartmentManagerIsNot(String departmentManager) {
        formSteps.checkPromotedDepartmentManagerIsNotPresent(departmentManager);
    }
}
