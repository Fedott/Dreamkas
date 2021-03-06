package ru.dreamkas.storage;

import ru.dreamkas.common.pageObjects.GeneralPageObject;

public class CurrentPageObjectStorage {

    private GeneralPageObject pageObject;

    public void setCurrentPageObject(GeneralPageObject pageObject) {
        this.pageObject = pageObject;
    }

    public GeneralPageObject getCurrentPageObject() {
        return pageObject;
    }

    public Boolean hasCurrentPageObject() {
        return null != pageObject;
    }
}
