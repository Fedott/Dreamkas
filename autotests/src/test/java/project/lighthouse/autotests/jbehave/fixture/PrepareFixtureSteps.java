package project.lighthouse.autotests.jbehave.fixture;

import fixtures.Us_53_1_Fixture;
import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.Given;
import project.lighthouse.autotests.steps.ConsoleCommandSteps;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.TransformerException;
import javax.xml.xpath.XPathExpressionException;
import java.io.File;
import java.io.IOException;

public class PrepareFixtureSteps {

    @Steps
    ConsoleCommandSteps consoleCommandSteps;

    @Given("the user prepares data for us 53.1 scenario 1")
    public void GivenTheUserPreparesDateForUs531Scenario1() throws InterruptedException, ParserConfigurationException, IOException, XPathExpressionException, TransformerException {
        File file = new Us_53_1_Fixture().prepareTodayData();
        consoleCommandSteps.runCapAutoTestsSymfonyImportSalesLocalCommand(file.getPath());
    }
}
