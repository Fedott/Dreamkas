package ru.dreamkas.jbehave;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.*;
import org.jbehave.core.model.ExamplesTable;
import ru.dreamkas.steps.CommonSteps;

public class CommonUserSteps {

    @Steps
    CommonSteps commonSteps;

    String storedUrl;

    @Then("the user sees error messages $errorMessageTable")
    public void ThenTheUserSeesErrorMessages(ExamplesTable errorMessageTable) {
        commonSteps.checkErrorMessages(errorMessageTable);
    }

    @Then("the user sees exact error messages $errorMessageTable")
    public void ThenTheUserSeesExactErrorMessages(ExamplesTable errorMessageTable) {
        commonSteps.exactCheckErrorMessages(errorMessageTable);
    }

    @Then("the user sees no error messages")
    public void ThenTheUserSeesNoErrorMessages() {
        commonSteps.checkNoErrorMessages();
    }

    @Then("the user sees no error messages $errorMessageTable")
    public void ThenTheUserSeesNoErrorMessages(ExamplesTable errorMessageTable) {
        commonSteps.checkNoErrorMessages(errorMessageTable);
    }

    @Then("the user checks alert text is equal to '$expectedText'")
    public void thenTheUserChecksAlertTextIsEqualTo(String expectedText) {
        commonSteps.checkAlertText(expectedText);
    }

    @Then("the user checks there is no alert on the page")
    public void thenTheUserChecksNoAlertOnThePage() {
        commonSteps.NoAlertIsPresent();
    }

    @Then("the user user sees <errorMessage>")
    @Aliases(values = {
            "the user user sees errorMessage",
            "the user sees '$errorMessage'",
            "пользователь видит сообщение об ошибке '$errorMessage'",
            "пользователь видит сообщение об ошибке c текстом errorMessage"
    })
    public void thenTheUserSeesErrorMessage(String errorMessage) {
        commonSteps.checkErrorMessage(errorMessage);
    }

    @Given("skipped test")
    @Pending
    public void pending() {
        //Pending
    }

    @When("the user refreshes the current page")
    @Given("the user refreshes the current page")
    @Alias("пользователь перезагружает страницу")
    public void whenTheUserRefreshesTheCurrentPage() {
        commonSteps.pageRefresh();
    }

    @Then("the user checks page contains text '$text'")
    @Alias("пользователь проверяет, что на странице присутствует текст '$text'")
    public void pageContainsText(String text) {
        commonSteps.pageContainsText(text);
    }

    @Given("skipped. Info: '$description', Details: '$details'")
    @Pending
    public void about(String description, String details) {
        //pending
    }

    @Given("the user stores the current url")
    public void givenTheUserStoresTheCurrentUrl() {
        storedUrl = commonSteps.getDriver().getCurrentUrl();
    }

    @Given("the user navigates to the stored url")
    public void givenTheUserNavigatesToTheStoredUrl() {
        commonSteps.getDriver().navigate().to(storedUrl);
    }

    @Then("the user waits for modal window closing")
    @Alias("пользователь ждет пока скроется модальное окно")
    public void thenTheUserWaitsForModalWindowClosing() {
        commonSteps.waitForModalPageClose();
    }

    @Then("пользователь ждет пока загрузится простой прелоадер")
    public void thenTheUserWaitsForSimplePreloaderLoading() {
        commonSteps.waitForSimplePreloaderLoading();
    }

    @Then("the user waits for page finishing loading")
    @Alias("пользователь ждет пока загрузится страница")
    public void thenTheUserWaitsForPageFinishingLoading() {
        commonSteps.waitForPageFinishingLoading();
    }

    @Then("the user asserts pop over content is '$content'")
    public void thenTheUserAssertsPopOverContent(String content) {
        commonSteps.assertPopOverContent(content);
    }

    @When("пользователь переключается на окно браузера с выбором кассы")
    @Aliases(values = {
            "пользователь переключается на окно браузера с кассой",
            "пользователь переключается на новое окно браузера"})
    public void whenUserSwitchesToThePOSWindow() {
        commonSteps.switchToLastWindowHandle();
    }

    @BeforeScenario
    @Given("Пользователь переключается на главное окно браузера если это необходимо")
    @Alias("пользователь закрывает окно с кассой и переключается на основное окно приложения")
    public void givenUserSwitchesToTheMainWindowHandle() {
        commonSteps.beforeScenarioSwitchToMainWindowHandleIfNeeded();
    }
}
