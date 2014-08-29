package project.lighthouse.autotests.objects.web.abstractObjects;

import junit.framework.Assert;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

import java.util.ArrayList;
import java.util.List;

abstract public class AbstractSearchObjectCollection extends AbstractObjectCollection<AbstractObject> {

    public AbstractSearchObjectCollection(WebDriver webDriver, By findBy) {
        super(webDriver, findBy);
    }

    public void containsHighLightText(String expectedHighLightText) {
        Boolean found = false;
        List<String> highLightCollection = new ArrayList<>();
        for (AbstractObject abstractObject : this) {
            AbstractSearchObjectNode abstractSearchObjectNode = (AbstractSearchObjectNode) abstractObject;
            highLightCollection.addAll(abstractSearchObjectNode.getHighLightTexts());
        }
        for (String highLightText : highLightCollection) {
            if (highLightText.equals(expectedHighLightText)) {
                found = true;
                break;
            }
        }
        if (!found) {
            String errorMessage = String.format("No highlightedText '%s'. Actual: '%s'", expectedHighLightText, highLightCollection);
            Assert.fail(errorMessage);
        }
    }
}
