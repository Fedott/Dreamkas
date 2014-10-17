package ru.dreamkas.steps;

import ru.dreamkas.pageObjects.PosPage;
import net.thucydides.core.annotations.Step;
import net.thucydides.core.steps.ScenarioSteps;

import org.jbehave.core.model.ExamplesTable;

import java.util.List;
import java.util.Map;

import static org.hamcrest.Matchers.hasItem;
import static org.hamcrest.Matchers.is;
import static org.junit.Assert.assertThat;

public class PosSteps extends ScenarioSteps {

    PosPage posPage;

    @Step
    public void assertActionBarTitle(String expectedTitle) {
        assertThat(posPage.getActionBarTitle(), is(expectedTitle));
    }

    @Step
    public void chooseSpinnerItemWithValue(String value) {
        posPage.chooseSpinnerItemWithValue(value);
    }

    @Step
    public void clickOnSaveStoreSettings() {
        posPage.clickOnSaveStoreSettings();
    }

    @Step
    public void assertStore(String store) {
        assertThat(posPage.getStore(), is(store));
    }

    @Step
    public void openDrawerAndClickOnDrawerOption(String menuOption) {
        posPage.openDrawerAndClickOnDrawerOption(menuOption);
    }

    @Step
    public void inputProductSearchQuery(String productSearchQuery) {
        posPage.inputProductSearchQuery(productSearchQuery);
    }

    @Step
    public void assertSearchProductsResult(Integer count) {
        assertThat(posPage.getSearchProductResultCount(), is(count));

    }

    @Step
    public void assertSearchProductsResult(String containsProductTitle) {
        assertThat(posPage.getSearchProductResult(), hasItem(containsProductTitle));
    }

    @Step
    public void assertSearchResultEmptyLabelText(String expected) {
        assertThat(posPage.getSearchResultEmptyLabel(), is(expected));
    }

    @Step
    public void assertReceiptEmptyLabelText(String expected) {
        assertThat(posPage.getReceiptEmptyLabel(), is(expected));
    }

    public void tapOnProductInSearchResultWithTitle(String title) {
        posPage.tapOnSearchListItemWithTitle(title);
    }

    public void assertReceiptItemsCount(Integer count) {
        assertThat(posPage.getReceiptItemsCount(), is(count));
    }

    public void assertReceiptContainsProducts(ExamplesTable examplesTable) {
        List<List<String>> rows = posPage.getReceiptItems();
        assertThat(rows.size(), is(examplesTable.getRowCount()));

        for (int i = 0; i < examplesTable.getRowCount(); i++) {
            assertThat(rows.get(i).size(), is(3));

            Map<String, String> ethalonRow = examplesTable.getRow(i);
            assertThat(rows.get(i).get(0), is(ethalonRow.get("Товар")));
            assertThat(rows.get(i).get(1), is(ethalonRow.get("Количество")));
            assertThat(rows.get(i).get(2), is(ethalonRow.get("Стоимость")));
        }
    }

    @Step
    public void assertReceiptTotalButtonText(String expected) {
        assertThat(posPage.getReceiptTotalButtonLabel(), is(expected));
    }
}
