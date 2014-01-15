package project.lighthouse.autotests.objects.web.grossMargin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.objects.web.abstractObjects.AbstractObject;
import project.lighthouse.autotests.objects.web.abstractObjects.objectInterfaces.ResultComparable;
import project.lighthouse.autotests.objects.web.compare.CompareResults;

import java.util.Map;

public class GrossMarginTableObject extends AbstractObject implements ResultComparable {

    private String date;
    private String grossMarginValue;

    public GrossMarginTableObject(WebElement element) {
        super(element);
    }

    @Override
    public void setProperties() {
        date = getElement().findElement(By.name("")).getText();
        grossMarginValue = getElement().findElement(By.name("")).getText();
    }

    @Override
    public CompareResults getCompareResults(Map<String, String> row) {
        return new CompareResults()
                .compare("date", date, row.get("name"))
                .compare("grossMarginValue", grossMarginValue, row.get("grossMarginValue"));
    }
}
