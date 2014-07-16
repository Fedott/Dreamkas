package project.lighthouse.autotests.pages.deprecated.commercialManager.supplier;

import net.thucydides.core.annotations.DefaultUrl;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.elements.Buttons.ButtonFacade;
import project.lighthouse.autotests.elements.Buttons.LinkFacade;
import project.lighthouse.autotests.elements.items.Input;
import project.lighthouse.autotests.elements.items.Textarea;
import project.lighthouse.autotests.pages.deprecated.commercialManager.supplier.pageElements.UploadForm;

/**
 * Page object representing supplier create/edit page
 */
@DefaultUrl("/suppliers/create")
public class SupplierPage extends CommonPageObject {


    private UploadForm uploadForm;

    public SupplierPage(WebDriver driver) {
        super(driver);
    }

    public UploadForm getUploadForm() {
        return uploadForm;
    }

    @Override
    public void createElements() {
        put("supplierName", new Input(this, "name", "Наименование"));
        put("phone", new Input(this, "phone", "Телефон"));
        put("fax", new Input(this, "fax", "Факс"));
        put("email", new Input(this, "email", "Эл. почта"));
        put("contactPerson", new Textarea(this, "contactPerson"));
    }

    public ButtonFacade getCreateButtonFacade() {
        return new ButtonFacade(this, "Сохранить");
    }

    public LinkFacade getCancelButtonLinkFacade() {
        return new LinkFacade(this, "Отменить");
    }
}
