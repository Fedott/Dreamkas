package project.lighthouse.autotests.jbehave;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.Given;
import org.jbehave.core.annotations.Then;
import org.jbehave.core.annotations.When;
import project.lighthouse.autotests.robotClient.InterruptedException_Exception;
import project.lighthouse.autotests.steps.RobotSteps;

import java.io.IOException;

public class RobotUserSteps {

    @Steps
    RobotSteps robotSteps;

    String uuid;

    @When("the robot starts the test named '$testName' on cashregistry with '$cashIp'")
    public void whenTheRobotsStartTheTestOnCashRegistry(String testName, String cashIp) throws InterruptedException_Exception {
        uuid = robotSteps.runTest(cashIp, testName);
    }

    @Then("the robot waits for the test success status")
    public void thenTheRobotWaitsForSuccessStatus() {
        robotSteps.waitForStatus(uuid);
    }

    @Given("the robot prepares products data")
    public void givenTheRobotPreparesData() throws IOException, InterruptedException {
        robotSteps.prepareData();
    }

    @Given("the robot waits for complete export")
    public void givenTheRobotWaitsForCompleteExport() throws InterruptedException {
        robotSteps.checkExportIsDone();
    }
}
