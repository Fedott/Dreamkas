package project.lighthouse.autotests.jbehave;

import net.thucydides.jbehave.ThucydidesJUnitStories;

public class AcceptanceTestSuite extends ThucydidesJUnitStories {
	
	public AcceptanceTestSuite() {
		
		findStoriesIn("**/Actual/");		
	}
}
