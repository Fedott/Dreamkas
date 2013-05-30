package project.lighthouse.autotests.pages.catalog;

import net.thucydides.core.annotations.DefaultUrl;
import net.thucydides.core.pages.WebElementFacade;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.FindBy;
import project.lighthouse.autotests.common.CommonItem;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.pages.elements.Input;

@DefaultUrl("/catalog")
public class CatalogPage extends CommonPageObject {

    @FindBy(xpath = "//*[@class='button button_color_blue catalog__addClassLink editor__control']")
    WebElementFacade addNewClassButton;

    @FindBy(xpath = "//*[@class='page__controlsLink editor__on']")
    WebElementFacade startEditionButtonLink;

    @FindBy(xpath = "//*[@class='page__controlsLink editor__off']")
    WebElementFacade stopEditionButtonLink;

    public CatalogPage(WebDriver driver) {
        super(driver);
    }

    public void addNewButtonClick() {
        addNewClassButton.click();
    }

    public void startEditionButtonLinkClick() {
        startEditionButtonLink.click();
    }

    public void stopEditionButtonLinkClick() {
        stopEditionButtonLink.click();
    }

    public void addNewButtonConfirmClick() {
        findBy("//*[@class='button button_color_blue']/input").click();
    }

    @Override
    public void createElements() {
        items.put("name", new Input(this, "name"));
    }

    public CommonItem getItem() {
        return items.get("name");
    }

    public void create(String name) {
        addNewButtonClick();
        getItem().setValue(name);
        //popUpMenuAccept();
        addNewButtonConfirmClick();
    }

    public void check(String name) {
        String classTitleXpath = getItemXpath(name);
        find(By.xpath(classTitleXpath)).shouldBeVisible();
    }

    public String getItemXpath(String name) {
        String classXpath = "//*[(@class='catalog__classLink' or @class='catalogClass__className') and text()='%s']";
        return String.format(classXpath, name);
    }

    public void popUpMenuInteraction(String name) {
        String triangleItemXpath = getItemXpath(name) + "/../a[contains(@class, 'editor__editLink')]";
        findElement(By.xpath(triangleItemXpath)).click();
    }

    public void popUpMenuDelete() {
        getOnlyVisibleWebElementFromTheWebElementsList(By.xpath("//*[@class='tooltip__controlLink tooltip__removeLink']")).click();
    }

    public void popUpMenuAccept() {
        //click ok
    }

    public void popUpMenuCancel() {
        //click cancel
    }

    public void popUpMenuEdit() {
        //click edit
    }

    public void checkIsNotPresent(String name) {
        String itemXpath = getItemXpath(name);
        waiter.waitUntilIsNotVisible(By.xpath(itemXpath));
    }

    public void itemClick(String name) {
        String itemXpath = getItemXpath(name);
        findElement(By.xpath(itemXpath)).click();
    }

    public void checkItemParent(String item, String parent) {
        String xpath = String.format(
                "//*[@class='catalog__classItem' and *[@class='catalog__classTitle']/a[text()='%s'] and *[@class='catalog__classGroupList']//a[text()='%s']]",
                parent, item);
        find(By.xpath(xpath)).shouldBeVisible();
    }

    public void checkFieldLength(String elementName, int fieldLength) {
        CommonItem item = items.get(elementName);
        commonPage.checkFieldLength(elementName, fieldLength, item.getWebElement());
    }
}
