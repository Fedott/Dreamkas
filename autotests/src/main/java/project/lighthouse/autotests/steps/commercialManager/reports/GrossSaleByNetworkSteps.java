package project.lighthouse.autotests.steps.commercialManager.reports;

import junit.framework.Assert;
import net.thucydides.core.annotations.Step;
import net.thucydides.core.steps.ScenarioSteps;
import project.lighthouse.autotests.helper.DateTimeHelper;
import project.lighthouse.autotests.pages.commercialManager.reports.GrossSalesByNetworkPage;

public class GrossSaleByNetworkSteps extends ScenarioSteps {

    GrossSalesByNetworkPage grossSalesByNetworkPage;

    private static final String ZERO_PRICE = "0,00 р.";

    @Step
    public void assertYesterdayValue() {
        String expectedMessage = String.format("Вчера %s", "115 050,00 р.");
        Assert.assertEquals(expectedMessage, grossSalesByNetworkPage.getYesterdayValue());
    }

    @Step
    public void assertTwoDaysAgoValue() {
        String expectedMessage = String.format("Позавчера %s", "77 700,00 р.");
        Assert.assertEquals(expectedMessage, grossSalesByNetworkPage.getTwoDaysAgoValue());
    }

    @Step
    public void assertEightDaysAgoValue() {
        String expectedMessage = String.format("В %s %s", DateTimeHelper.getDate(), "118 050,00 р.");
        Assert.assertEquals(expectedMessage, grossSalesByNetworkPage.getEightDaysAgoValue());
    }

    @Step
    public void assertYesterdayValueIsZero() {
        String expectedMessage = String.format("Вчера %s", ZERO_PRICE);
        Assert.assertEquals(expectedMessage, grossSalesByNetworkPage.getYesterdayValue());
    }

    @Step
    public void assertTwoDaysAgoValueIsZero() {
        String expectedMessage = String.format("Позавчера %s", ZERO_PRICE);
        Assert.assertEquals(expectedMessage, grossSalesByNetworkPage.getTwoDaysAgoValue());
    }

    @Step
    public void assertEightDaysAgoValueIsZero() {
        String expectedMessage = String.format("В %s %s", DateTimeHelper.getDate(), ZERO_PRICE);
        Assert.assertEquals(expectedMessage, grossSalesByNetworkPage.getEightDaysAgoValue());
    }
}
