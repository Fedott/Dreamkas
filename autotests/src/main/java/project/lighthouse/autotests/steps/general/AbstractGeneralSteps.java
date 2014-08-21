package project.lighthouse.autotests.steps.general;

import net.thucydides.core.steps.ScenarioSteps;
import project.lighthouse.autotests.common.GeneralPageObject;

import java.util.Map;

public abstract class AbstractGeneralSteps<T extends GeneralPageObject> extends ScenarioSteps {

    private T currentPageObject;

    abstract Map<String, Class> getPageObjectClasses();

    @SuppressWarnings("unchecked")
    public void setCurrentPageObject(String pageObjectName) {
        Class pageObjectClass = getPageObjectClasses().get(pageObjectName);
        currentPageObject = (T) getPages().get(pageObjectClass);
    }

    protected T getCurrentPageObject() {
        return currentPageObject;
    }
}
