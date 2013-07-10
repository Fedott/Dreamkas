package project.lighthouse.autotests.pages.commercialManager.catalog;


import net.thucydides.core.pages.WebElementFacade;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.FindBy;
import project.lighthouse.autotests.elements.InputOnlyVisible;

public class CategoryPage extends GroupPage {

    @FindBy(xpath = "//*[@class='button button_color_blue catalog__addCategoryLink editor__control']")
    WebElementFacade addNewCategoryButton;

    public CategoryPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void addNewButtonClick() {
        addNewCategoryButton.click();
    }

    @Override
    public void addNewButtonConfirmClick() {
        findBy("//*[@class='form catalog__addGroupForm']//*[@class='button button_color_blue']/input").click();
        preloaderWait();
    }

    @Override
    public void createElements() {
        items.put("name", new InputOnlyVisible(this, "name"));
    }

    @Override
    public String getItemXpath(String name) {
        String groupXpath = "//*[@model_name='catalogCategory' and text()='%s']";
        return String.format(groupXpath, name);
    }
}
