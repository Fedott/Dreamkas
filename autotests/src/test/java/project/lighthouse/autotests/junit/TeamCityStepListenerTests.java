package project.lighthouse.autotests.junit;

import net.thucydides.core.model.*;
import org.junit.Before;
import org.junit.Test;
import org.mockito.ArgumentCaptor;
import org.slf4j.Logger;
import project.lighthouse.autotests.thucydides.TeamCityStepListener;

import java.util.HashMap;

import static org.hamcrest.Matchers.is;
import static org.junit.Assert.assertThat;
import static org.mockito.Mockito.*;

/**
 * Test class to test implemented team city thucydides step listener
 */
public class TeamCityStepListenerTests {

    private Logger logger;
    private TeamCityStepListener teamCityStepListener;

    private static final String STORY_PATH = "stories/sprint-1/us-1/story.story";
    private static final Story STORY = Story.withIdAndPath("storyId", "Test story", STORY_PATH);
    private static final Throwable THROWABLE = new Throwable("the test is failed!");
    private static final DataTable DATA_TABLE = mock(DataTable.class);

    @Before
    public void before() {
        logger = mock(Logger.class);
        TeamCityStepListener originalTeamCityStepListener = new TeamCityStepListener(logger);
        teamCityStepListener = spy(originalTeamCityStepListener);
        doReturn("StackTrace").when(teamCityStepListener).getStackTrace(any(Throwable.class));
    }

    @Test
    public void testScenarioResultIsSuccess() {

        TestOutcome testOutcome = new TestOutcome("passedScenario");
        testOutcome.setUserStory(STORY);
        testOutcome.recordStep(TestStepFactory.getSuccessfulTestStep("Passed"));

        teamCityStepListener.testFinished(testOutcome);

        String testStartedExpectedMessage = "##teamcity[testStarted  name='sprint-1.us-1.story.passedScenario']";
        String testFinishedExpectedMessage = "##teamcity[testFinished  duration='100' name='sprint-1.us-1.story.passedScenario']";

        ArgumentCaptor<String> stringArgumentCaptor = ArgumentCaptor.forClass(String.class);
        verify(logger, times(2)).info(stringArgumentCaptor.capture());
        assertThat(stringArgumentCaptor.getAllValues().get(0), is(testStartedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(1), is(testFinishedExpectedMessage));
    }

    @Test
    public void testScenarioResultIsFailure() {

        TestOutcome testOutcome = new TestOutcome("failedScenario");
        testOutcome.setUserStory(STORY);
        testOutcome.recordStep(TestStepFactory.getFailureTestStep("Failed scenario step", THROWABLE));
        testOutcome.setTestFailureCause(THROWABLE);

        teamCityStepListener.testFinished(testOutcome);

        String testStartedExpectedMessage = "##teamcity[testStarted  name='sprint-1.us-1.story.failedScenario']";
        String testFailedExpectedMessage = "##teamcity[testFailed  message='the test is failed!' details='Steps:|r|nFailed scenario step (0.1) -> ERROR|r|n|nStackTrace|r|n|r|n' name='sprint-1.us-1.story.failedScenario']";
        String testFinishedExpectedMessage = "##teamcity[testFinished  duration='100' name='sprint-1.us-1.story.failedScenario']";

        ArgumentCaptor<String> stringArgumentCaptor = ArgumentCaptor.forClass(String.class);
        verify(logger, times(3)).info(stringArgumentCaptor.capture());

        assertThat(stringArgumentCaptor.getAllValues().get(0), is(testStartedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(1), is(testFailedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(2), is(testFinishedExpectedMessage));
    }

    /**
     * This test is failed because listener can't get the children step cause exception
     * Solution: have to iterate through children steps if(isAgroup = true) and print all childSteps info with failed child step
     */
    @Test
    public void testScenarioChildStepResultIsFailure() {

        TestOutcome testOutcome = new TestOutcome("failedScenario");
        testOutcome.setUserStory(STORY);
        TestStep testStep = TestStepFactory.getFailureTestStep("Failed scenario step");
        testStep.addChildStep(TestStepFactory.getFailureTestStep("Failed scenario child step", THROWABLE));
        testOutcome.recordStep(testStep);
        testOutcome.setTestFailureCause(THROWABLE);

        teamCityStepListener.testFinished(testOutcome);

        String testStartedExpectedMessage = "##teamcity[testStarted  name='sprint-1.us-1.story.failedScenario']";
        String testFailedExpectedMessage = "##teamcity[testFailed  message='the test is failed!' details='Steps:|r|nFailed scenario step (0.1) -> ERROR|r|n|nStackTrace|r|n|r|n' name='sprint-1.us-1.story.failedScenario']";
        String testFinishedExpectedMessage = "##teamcity[testFinished  duration='100' name='sprint-1.us-1.story.failedScenario']";

        ArgumentCaptor<String> stringArgumentCaptor = ArgumentCaptor.forClass(String.class);
        verify(logger, times(3)).info(stringArgumentCaptor.capture());

        assertThat(stringArgumentCaptor.getAllValues().get(0), is(testStartedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(1), is(testFailedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(2), is(testFinishedExpectedMessage));
    }

    @Test
    public void testScenarioResultIsError() {

        TestOutcome testOutcome = new TestOutcome("failedScenario");
        testOutcome.setUserStory(STORY);
        testOutcome.recordStep(TestStepFactory.getErrorTestStep("Failed scenario step", THROWABLE));
        testOutcome.setTestFailureCause(THROWABLE);

        teamCityStepListener.testFinished(testOutcome);

        String testStartedExpectedMessage = "##teamcity[testStarted  name='sprint-1.us-1.story.failedScenario']";
        String testFailedExpectedMessage = "##teamcity[testFailed  message='the test is failed!' details='Steps:|r|nFailed scenario step (0.1) -> ERROR|r|n|nStackTrace|r|n|r|n' name='sprint-1.us-1.story.failedScenario']";
        String testFinishedExpectedMessage = "##teamcity[testFinished  duration='100' name='sprint-1.us-1.story.failedScenario']";

        ArgumentCaptor<String> stringArgumentCaptor = ArgumentCaptor.forClass(String.class);
        verify(logger, times(3)).info(stringArgumentCaptor.capture());

        assertThat(stringArgumentCaptor.getAllValues().get(0), is(testStartedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(1), is(testFailedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(2), is(testFinishedExpectedMessage));
    }

    @Test
    public void testScenarioResultIsSkipped() {

        TestOutcome testOutcome = new TestOutcome("skippedScenario");
        testOutcome.setUserStory(STORY);
        testOutcome.recordStep(TestStepFactory.getSkippedTestStep("Skipped scenario step"));

        teamCityStepListener.testFinished(testOutcome);

        String testStartedExpectedMessage = "##teamcity[testStarted  name='sprint-1.us-1.story.skippedScenario']";
        String testIgnoredExpectedMessage = "##teamcity[testIgnored  name='sprint-1.us-1.story.skippedScenario']";
        String testFinishedExpectedMessage = "##teamcity[testFinished  duration='100' name='sprint-1.us-1.story.skippedScenario']";

        ArgumentCaptor<String> stringArgumentCaptor = ArgumentCaptor.forClass(String.class);
        verify(logger, times(3)).info(stringArgumentCaptor.capture());

        assertThat(stringArgumentCaptor.getAllValues().get(0), is(testStartedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(1), is(testIgnoredExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(2), is(testFinishedExpectedMessage));
    }

    @Test
    public void testScenarioResultIsPending() {

        TestOutcome testOutcome = new TestOutcome("pendingScenario");
        testOutcome.setUserStory(STORY);
        testOutcome.recordStep(TestStepFactory.getPendingTestStep("Pending scenario step"));

        teamCityStepListener.testFinished(testOutcome);

        String testStartedExpectedMessage = "##teamcity[testStarted  name='sprint-1.us-1.story.pendingScenario']";
        String testIgnoredExpectedMessage = "##teamcity[testIgnored  name='sprint-1.us-1.story.pendingScenario']";
        String testFinishedExpectedMessage = "##teamcity[testFinished  duration='100' name='sprint-1.us-1.story.pendingScenario']";

        ArgumentCaptor<String> stringArgumentCaptor = ArgumentCaptor.forClass(String.class);
        verify(logger, times(3)).info(stringArgumentCaptor.capture());

        assertThat(stringArgumentCaptor.getAllValues().get(0), is(testStartedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(1), is(testIgnoredExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(2), is(testFinishedExpectedMessage));
    }

    @Test
    public void testScenarioResultIsIgnored() {

        TestOutcome testOutcome = new TestOutcome("ignoringScenario");
        testOutcome.setUserStory(STORY);
        testOutcome.recordStep(TestStepFactory.getIgnoredTestStep("Ignored scenario step"));

        teamCityStepListener.testFinished(testOutcome);

        String testStartedExpectedMessage = "##teamcity[testStarted  name='sprint-1.us-1.story.ignoringScenario']";
        String testIgnoredExpectedMessage = "##teamcity[testIgnored  name='sprint-1.us-1.story.ignoringScenario']";
        String testFinishedExpectedMessage = "##teamcity[testFinished  duration='100' name='sprint-1.us-1.story.ignoringScenario']";

        ArgumentCaptor<String> stringArgumentCaptor = ArgumentCaptor.forClass(String.class);
        verify(logger, times(3)).info(stringArgumentCaptor.capture());

        assertThat(stringArgumentCaptor.getAllValues().get(0), is(testStartedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(1), is(testIgnoredExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(2), is(testFinishedExpectedMessage));
    }

    @Test
    public void testParametrisedSuccessfulScenarioWithGivenStoriesInStory() {

        teamCityStepListener.exampleStarted(new HashMap<String, String>() {{
            put("value", "exampleTableValue");
        }});

        TestOutcome testOutcome = new TestOutcome("parametrisedScenario");
        testOutcome.useExamplesFrom(DATA_TABLE);
        testOutcome.setUserStory(STORY);
        TestStep testStep = TestStepFactory.getSuccessfulTestStep("Successful scenario step");
        testStep.addChildStep(TestStepFactory.getSuccessfulTestStep("Successful scenario child step"));
        testOutcome.recordStep(testStep);
        TestStep testStep2 = TestStepFactory.getSuccessfulTestStep("[1] {value=exampleTableValue");
        testStep2.addChildStep(TestStepFactory.getSuccessfulTestStep("Successful scenario child step"));
        testOutcome.recordStep(testStep2);

        teamCityStepListener.testFinished(testOutcome);

        String testStartedExpectedMessage = "##teamcity[testStarted  name='sprint-1.us-1.story.parametrisedScenario.{value=exampleTableValue}']";
        String testFinishedExpectedMessage = "##teamcity[testFinished  duration='100' name='sprint-1.us-1.story.parametrisedScenario.{value=exampleTableValue}']";

        ArgumentCaptor<String> stringArgumentCaptor = ArgumentCaptor.forClass(String.class);
        verify(logger, times(2)).info(stringArgumentCaptor.capture());

        assertThat(stringArgumentCaptor.getAllValues().get(0), is(testStartedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(1), is(testFinishedExpectedMessage));
    }

    @Test
    public void testEscapingSymbols() {

        TestOutcome testOutcome = new TestOutcome("\\|'\n\r\\[\\][]");
        testOutcome.setUserStory(STORY);
        Throwable throwable = new Throwable("\\|'\n\r\\[\\][]");
        testOutcome.recordStep(TestStepFactory.getFailureTestStep("\\|'\n\r\\[\\][]", throwable));
        testOutcome.setTestFailureCause(throwable);

        teamCityStepListener.testFinished(testOutcome);

        String testStartedExpectedMessage = "##teamcity[testStarted  name='sprint-1.us-1.story.|||'|n|r||[||]|[|]']";
        String testFailedExpectedMessage = "##teamcity[testFailed  message='|||'|n|r||[||]|[|]' details='Steps:|r|n|||'|n|r||[||]|[|] (0.1) -> ERROR|r|n|nStackTrace|r|n|r|n' name='sprint-1.us-1.story.|||'|n|r||[||]|[|]']";
        String testFinishedExpectedMessage = "##teamcity[testFinished  duration='100' name='sprint-1.us-1.story.|||'|n|r||[||]|[|]']";

        ArgumentCaptor<String> stringArgumentCaptor = ArgumentCaptor.forClass(String.class);
        verify(logger, times(3)).info(stringArgumentCaptor.capture());

        assertThat(stringArgumentCaptor.getAllValues().get(0), is(testStartedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(1), is(testFailedExpectedMessage));
        assertThat(stringArgumentCaptor.getAllValues().get(2), is(testFinishedExpectedMessage));
    }

    //test scenario with given stories in story
    //test scenario with given stories in test scenario
    //test scenario with given stories in test scenario + in story

    //test childstep success, pending, error, failure, skipped, ignored

    //example result successful, error, fail, skipped, ignored, pending
    //example result with given story in story
    //example result with given story in test
    //example result with given story in test + in story

    //test example childstep success, pending, error, failure, skipped, ignored

    //test if given stories fails (given story in story)
    //test if given stories fails (given story in scenario)
}
