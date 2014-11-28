package ru.dreamkas.pages.catalog.modal;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import ru.dreamkas.elements.bootstrap.buttons.PrimaryBtnFacade;
import ru.dreamkas.elements.items.Input;
import ru.dreamkas.pages.modal.ModalWindowPage;

/**
 * Create group modal page object
 */
public class CreateGroupModalPage extends ModalWindowPage {

    public CreateGroupModalPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
        put("name", new Input(this, "//*[@name='name']"));
        putDefaultConfirmationOkButton(
                new PrimaryBtnFacade(this, "Добавить"));
    }

    @Override
    public String modalWindowXpath() {
        return "//*[contains(@id, 'modal_group') and contains(@class, 'modal_visible')]";
    }

    @Override
    public String getTitle() {
        return findVisibleElement(By.xpath(modalWindowXpath() + "//*[@class='modal__title']")).getText();
    }
}
