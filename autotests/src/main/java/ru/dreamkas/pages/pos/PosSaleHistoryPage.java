package ru.dreamkas.pages.pos;

import net.thucydides.core.annotations.findby.By;
import org.openqa.selenium.WebDriver;
import ru.dreamkas.collection.receiptHistory.HistoryReceiptCollection;
import ru.dreamkas.elements.items.JSInput;
import ru.dreamkas.elements.items.autocomplete.ProductAutoComplete;

public class PosSaleHistoryPage extends CommonPosPage {

    public PosSaleHistoryPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void addObjectButtonClick() {
        throw new AssertionError("Not implemented!");
    }

    @Override
    public void createElements() {
        put("дата с", new JSInput(this, "dateFrom"));
        put("дата по", new JSInput(this, "dateTo"));
        put("автокомплитное поле поиска товара", new ProductAutoComplete(this, By.xpath("//*[@name='product.name' and contains(@class, 'input')]")));
        putDefaultCollection(new HistoryReceiptCollection(getDriver()));
    }
}
