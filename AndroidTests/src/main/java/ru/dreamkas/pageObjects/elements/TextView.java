package ru.dreamkas.pageObjects.elements;

import ru.dreamkas.pageObjects.CommonPageObject;
import ru.dreamkas.pageObjects.elements.interfaces.Gettable;

public class TextView extends ViewElement implements Gettable {

    public TextView(CommonPageObject commonPageObject, String id) {
        super(commonPageObject, id);
    }

    @Override
    public String getText() {
        return getCommonPageObject().getAppiumDriver().findElement(getFindBy()).getText();
    }
}
