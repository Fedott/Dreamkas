package project.lighthouse.autotests.steps.logSteps;

import net.thucydides.core.pages.Pages;
import net.thucydides.core.steps.ScenarioSteps;
import project.lighthouse.autotests.pages.logPage.LogPage;

import static junit.framework.Assert.assertEquals;

public class LogSteps extends ScenarioSteps {

    LogPage logPage;

    public LogSteps(Pages pages) {
        super(pages);
    }

    public void open() {
        logPage.open();
    }

    public String getLastLogStatus() {
        return logPage.getLastRecalcProductLogMessage().getStatus();
    }

    public String getLastLogStatusText() {
        return logPage.getLastRecalcProductLogMessage().getStatusText();
    }

    public String getLastLogProduct() {
        return logPage.getLastRecalcProductLogMessage().getProduct();
    }

    public String getLastLogTitle() {
        return logPage.getLastRecalcProductLogMessage().getTitle();
    }

    public void waitStatusForSuccess() {
        String status = getLastLogStatus();
        int retriesCount = 0;
        while (!status.equals(LogPage.SUCCESS_STATUS) && retriesCount < 10) {
            status = getLastLogStatus();
            getDriver().navigate().refresh();
            retriesCount++;
        }
        assertEquals(LogPage.SUCCESS_STATUS, status);
    }

    public void assertLastLogProduct(String expectedMessage) {
        assertEquals(expectedMessage, getLastLogProduct());
    }

    public void assertLastLogTitle(String expectedTitle) {
        assertEquals(expectedTitle, getLastLogTitle());
    }

    public void assertLastLogStatusText(String expectedStatusText) {
        assertEquals(expectedStatusText, getLastLogStatusText());
    }
}
