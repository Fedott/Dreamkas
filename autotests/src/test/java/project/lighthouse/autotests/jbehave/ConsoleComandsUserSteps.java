package project.lighthouse.autotests.jbehave;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.Given;
import org.xml.sax.SAXException;
import project.lighthouse.autotests.steps.ConsoleCommandSteps;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.TransformerException;
import java.io.IOException;

public class ConsoleComandsUserSteps {

    @Steps
    ConsoleCommandSteps consoleCommandSteps;

    @Given("the user runs the prepare fixture data cap command for inventory testing")
    public void givenTheRobotRunsThePrepareFixtureDataCommand() throws IOException, InterruptedException, TransformerException, ParserConfigurationException, SAXException {
        consoleCommandSteps.runFixtureCommand();
    }

    @Given("the user runs the recalculate_metrics cap command")
    public void givenTheRobotRunsTheRecalculateMetricsCapCommand() throws IOException, InterruptedException {
        consoleCommandSteps.runCapAutoTestsSymfonyProductsRecalculateMetrics();
    }

    @Given("the user runs the prepare fixture data cap command for negative inventory testing - '$days' days")
    public void givenTheRobotRunsThePrepareNegativeFixtureDataCommand(String days) throws IOException, InterruptedException, TransformerException, ParserConfigurationException, SAXException {
        consoleCommandSteps.runNegativeFixtureCommand(days);
    }
}
