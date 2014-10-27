package ru.dreamkas.pages.catalog.modal;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import ru.dreamkas.elements.bootstrap.buttons.PrimaryBtnFacade;
import ru.dreamkas.elements.items.Input;
import ru.dreamkas.elements.items.NonType;
import ru.dreamkas.elements.items.SelectByLabel;

/**
 * Edit group modal page object
 */
public class EditGroupModalPage extends CreateGroupModalPage {

    public EditGroupModalPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
        super.createElements();

        put("заголовок успешного удаления", new NonType(this, "//*[@name='successRemoveTitle']"));
        put("название удаленной группы", new NonType(this, "//*[@name='removedGroupName']"));
        put("кнопка продолжения работы", new NonType(this, "//*[@name='removeContinue']"));
    }

    public void deleteButtonClick() {
        findVisibleElement(By.xpath(modalWindowXpath() + "//*[contains(@class, 'removeLink')]")).click();
    }

    public void deleteButtonConfirmClick() {
        findVisibleElement(By.xpath(modalWindowXpath() + "//*[@class='confirmLink__confirmation']/*[contains(@class, 'removeLink')]")).click();
    }

    @Override
    public void confirmationOkClick() {
        new PrimaryBtnFacade(this, "Сохранить").click();
    }
}
