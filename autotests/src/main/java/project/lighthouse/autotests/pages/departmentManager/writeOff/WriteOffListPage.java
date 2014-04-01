package project.lighthouse.autotests.pages.departmentManager.writeOff;

import net.thucydides.core.annotations.DefaultUrl;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import project.lighthouse.autotests.CommonViewInterface;
import project.lighthouse.autotests.common.CommonPageObject;
import project.lighthouse.autotests.common.CommonView;
import project.lighthouse.autotests.elements.items.NonType;

@DefaultUrl("/writeOffs")
public class WriteOffListPage extends CommonPageObject {

    public static final String ITEM_NAME = "writeOff";
    private static final String ITEM_SKU_NAME = "number";

    CommonViewInterface commonViewInterface = new CommonView(getDriver(), ITEM_NAME, ITEM_SKU_NAME);

    public WriteOffListPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
        put("writeOff list page date", new NonType(this, "date"));
        put("writeOff list page number", new NonType(this, "number"));
        put("writeOff list page sumTotal", new NonType(this, "sumTotal"));
    }

    public void listItemCheck(String skuValue) {
        commonViewInterface.itemCheck(skuValue);
    }

    public void checkListItemHasExpectedValueByFindByLocator(String value, String elementName, String expectedValue) {
        By findBy = getItems().get(elementName).getFindBy();
        commonViewInterface.checkListItemHasExpectedValueByFindByLocator(value, elementName, findBy, expectedValue);
    }
}
