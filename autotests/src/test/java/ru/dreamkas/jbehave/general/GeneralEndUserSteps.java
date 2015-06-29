package ru.dreamkas.jbehave.general;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.*;
import org.jbehave.core.model.ExamplesTable;
import ru.dreamkas.steps.general.GeneralSteps;

public class GeneralEndUserSteps {

    @Steps
    GeneralSteps generalSteps;

    @Given("пользователь* находится на странице '$pageObjectName'")
    @Alias("пользователь* взаимодействует со страницей '$pageObjectName'")
    @When("пользователь* находится на странице '$pageObjectName'")
    public void givenUserSetsPageObjectWihName(String pageObjectName) {
        generalSteps.setCurrentPageObject(pageObjectName);
    }

    @Given("пользователь* открывает страницу")
    public void givenTheUserOpensPage() {
        generalSteps.openPage();
    }

    @Given("пользователь* открывает страницу '$pageObjectName'")
    public void givenTheUserOpensPageByPageObjectName(String pageObjectName) {
        generalSteps.setCurrentPageObject(pageObjectName);
        generalSteps.openPage();
    }

    @When("пользователь* вводит данные в поля $exampleTable")
    public void userInputsField(ExamplesTable exampleTable) {
        generalSteps.input(exampleTable);
    }


    @When("пользователь* вводит значение '$value' в поле с именем '$elementName'")
    @Alias("пользователь* вводит значение value в поле с именем '$elementName'")
    public void whenUserInputsValueInFieldWithName(String value, String elementName) {
        generalSteps.input(elementName, value);
    }

    @When("пользователь* нажимает на элемент списка '$collectionName' с именем '$locator'")
    public void whenUserClicksOnCollectionObjectByLocator(String locator) {
        generalSteps.clickOnCollectionObjectByLocator(locator);
    }

    @When("пользователь* нажимает на елемент с именем '$name'")
    @Aliases(values = {
            "пользователь* нажимает на кнопку с названием '$name'",
            "пользователь* нажимает на ссылку с названием '$name'"})

    public void whenTheUserClicksOnElementWithName(String name) {
        generalSteps.clickOnCommonItemWihName(name);
    }

    @When("пользователь* нажимает на кнопку создания '$button'")
    public void whenTheUserClicksOnCreateButton() {
        generalSteps.clickOnDefaultAddObjectButton();
    }

    @Then("пользователь* проверяет, что поле с именем '$elementName' имеет значение '$value'")
    @Alias("пользователь* проверяет, что '$elementName' имеет значение '$value'")
    public void thenUserChecksValue(String elementName, String value) {
        generalSteps.checkValue(elementName, value);
    }

    @Then("пользователь* проверяет поля $exampleTable")
    public void thenUserChecksFieldValues(ExamplesTable examplesTable) {
        generalSteps.checkValues(examplesTable);
    }

    @Then("пользователь* проверяет, что у поля с именем '$elementName' имеется сообщения об ошибке с сообщением '$errorMessage'")
    public void thenUserChecksFieldWithNameHasErrorMessageWithText(String elementName, String errorMessage) {
        generalSteps.checkItemErrorMessage(elementName, errorMessage);
    }

    @Then("пользователь* проверяет, что заголовок равен '$title'")
    public void thenUserAssertsTheModalWindowTitle(String title) {
        generalSteps.assertTitle(title);
    }

    @Then("пользователь* проверяет, что элемент с именем '$elementName' должен быть видимым")
    public void thenUserChecksTheElementWithNameShouldBeVisible(String elementName) {
        generalSteps.elementShouldBeVisible(elementName);
    }

    @Then("пользователь* проверяет, что элемент с именем '$elementName' должен быть невидимым")
    @Alias("пользователь* проверяет, что кнопка с названием '$elementName' должна быть невидимой")
    public void thenUserChecksTheElementWithNameShouldBeNotVisible(String elementName) {
        generalSteps.elementShouldBeNotVisible(elementName);
    }

    @Then("пользователь* проверяет, что список '$collectionName' содержит точные данные $examplesTable")
    public void thenUserExactCompareCollectionWithExamplesTable(ExamplesTable examplesTable) {
        generalSteps.exactCompareExampleTable(examplesTable);
    }

    @Then("пользователь* проверяет, что список '$collectionName' содержит данные $examplesTable")
    public void thenUserCompareCollectionWithExamplesTable(ExamplesTable examplesTable) {
        generalSteps.compareWithExampleTable(examplesTable);
    }

    @Then("пользователь проверяет, что у элемента с именем '$commonItemName' аттрибут '$attribute' имеет значение '$value'")
    public void thenTheUserChecksCommonItemAttributeValue(String commonItemName, String attribute, String value) {
        generalSteps.assertCommonItemAttributeValue(commonItemName, attribute, value);
    }

    @Then("пользователь проверяет, что у элемента с именем '$commonItemName' аттрибут '$attribute' содержит значение '$value'")
    public void thenTheUserChecksCommonItemAttributeContainsValue(String commonItemName, String attribute, String value) {
        generalSteps.assertCommonItemAttributeContainsValue(commonItemName, attribute, value);
    }

    @Then("пользователь проверяет, что у элемента с именем '$commonItemName' css '$cssValue' имеет значение '$value'")
    public void thenTheUserChecksCommonItemCssValue(String commonItemName, String cssValue, String value) {
        generalSteps.assertCommonItemCssValue(commonItemName, cssValue, value);
    }

    @Then("пользователь* проверяет, что список '$collectionName' не содержит элемент с именем '$locator'")
    public void thenUserChecksThatCollectionNotContainObjectWithLocator(String locator) {
        generalSteps.collectionNotContainObjectWithLocator(locator);
    }

    @Then("пользователь* проверяет, что список '$collectionName' содержит элемент с именем '$locator'")
    public void thenUserChecksThatCollectionContainObjectWithLocator(String locator) {
        generalSteps.collectionContainObjectWithLocator(locator);
    }

    @Then("пользователь* проверяет, что селект с именем '$elementName' не содержит опцию '$option'")
    public void thenUserChecksThatSelectNotContainExactlyOption(String elementName, String option) {
        generalSteps.selectNotContainExactlyOption(elementName, option);
    }

    @Then("пользователь* проверяет, что селект с именем '$elementName' не содержит опцию like '$option'")
    public void thenUserChecksThatSelectNotContainOption(String elementName, String option) {
        generalSteps.selectNotContainOption(elementName, option);
    }
}
