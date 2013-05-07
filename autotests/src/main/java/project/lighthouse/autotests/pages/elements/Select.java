package project.lighthouse.autotests.pages.elements;

import project.lighthouse.autotests.pages.common.CommonItem;
import project.lighthouse.autotests.pages.common.CommonPageObject;

public class Select extends CommonItem {

    public Select(CommonPageObject pageObject, String name) {
        super(pageObject, name);
    }

    @Override
    public void setValue(String value) {
        $().selectByValue(value);
    }
}
