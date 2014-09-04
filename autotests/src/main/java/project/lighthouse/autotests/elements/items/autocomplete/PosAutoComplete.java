package project.lighthouse.autotests.elements.items.autocomplete;

import org.openqa.selenium.By;
import project.lighthouse.autotests.common.CommonItem;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.objects.web.posAutoComplete.PosAutoCompleteCollection;

public class PosAutoComplete extends CommonItem {

    public PosAutoComplete(CommonPageObject pageObject, By findBy) {
        super(pageObject, findBy);
    }

    @Override
    public void setValue(String value) {
        if (value.startsWith("!")) {
            value = value.substring(1);
            getVisibleWebElementFacade().type(value);
        } else if (value.equals("#clear")) {
            getPageObject().findVisibleElement(By.xpath("//*[contains(@class, 'fa fa-times')]")).click();
        } else {
            getVisibleWebElementFacade().type(value);
            new PosAutoCompleteCollection(getPageObject().getDriver()).clickByLocator(value);
        }
    }
}
