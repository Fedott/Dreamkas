package ru.dreamkas.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import ru.dreamkas.common.pageObjects.BootstrapPageObject;
import ru.dreamkas.elements.bootstrap.buttons.PrimaryBtnFacade;
import ru.dreamkas.elements.items.NonType;
import ru.dreamkas.exceptions.NotImplementedException;

public class QuickStartPage extends BootstrapPageObject {

    public QuickStartPage(WebDriver driver) {
        super(driver);
    }

    @Override
    public void createElements() {
//        Шаги
        put("Шаг магазины", new NonType(this, By.xpath("//*[@name='stores']")));
        put("Шаг товары", new NonType(this, By.xpath("//*[@name='products']")));
        put("Шаг продажа", new NonType(this, By.xpath("//*[@name='sales']")));
//        Номер шагов
        put("Номер шага магазины", new NonType(this, By.xpath("//*[@name='stores']/*[@class='page_dashboard__stepNumber']")));
        put("Номер шага товары", new NonType(this, By.xpath("//*[@name='products']/*[@class='page_dashboard__stepNumber']")));
        put("Номер шага продажа", new NonType(this, By.xpath("//*[@name='sales']/*[@class='page_dashboard__stepNumber']")));
//        Названия шагов
        put("Название шага магазины", new NonType(this, By.xpath("//*[@name='stores']/*[@class='page_dashboard__stepTitle']")));
        put("Название шага товары", new NonType(this, By.xpath("//*[@name='products']/*[@class='page_dashboard__stepTitle']")));
        put("Название шага продажа", new NonType(this, By.xpath("//*[@name='sales']/*[@class='page_dashboard__stepTitle']")));
//        Описания шагов
        put("Описание шага магазины", new NonType(this, By.xpath("//*[@name='stores']/*[@class='page_dashboard__stepDescription']")));
        put("Описание шага товары", new NonType(this, By.xpath("//*[@name='products']/*[@class='page_dashboard__stepDescription']")));
        put("Описание шага продажа", new NonType(this, By.xpath("//*[@name='sales']/*[@class='page_dashboard__stepDescription']")));
//        Кнопки
        put("Добавить магазин", new PrimaryBtnFacade(this, "ДОБАВИТЬ МАГАЗИН"));
        put("Принять товар", new PrimaryBtnFacade(this, "ПРИНЯТЬ ТОВАР"));
        put("Запустить кассу", new PrimaryBtnFacade(this, "ЗАПУСТИТЬ КАССУ"));
        put("Продолжить работу", new PrimaryBtnFacade(this, "ПРОДОЛЖИТЬ РАБОТУ"));
//        Плитки
//        Магазин
        put("созданный магазин", new NonType(this, By.xpath("//*[@name='store']/*[@name='storeName']")));
//        Товары
        put("сумма товаров в магазине на сумму", new NonType(this, By.xpath("//*[@name='products']/*[@name='costOfInventory']")));
//        Продажа
        put("продажа", new NonType(this, By.xpath("//*[@name='sales']/*[@name='sumTotal']")));
        put("себестоимость", new NonType(this, By.xpath("//*[@name='sales']//*[@name='costOfGoods']")));
        put("прибыль", new NonType(this, By.xpath("//*[@name='sales']//*[@name='grossMargin']")));
    }

    @Override
    public void addObjectButtonClick() {
        throw new NotImplementedException();
    }
}
