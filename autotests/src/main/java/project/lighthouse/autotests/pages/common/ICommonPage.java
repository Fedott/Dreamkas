package project.lighthouse.autotests.pages.common;

import net.thucydides.core.pages.PageObject;
import net.thucydides.core.pages.WebElementFacade;
import org.jbehave.core.model.ExamplesTable;
import org.openqa.selenium.Alert;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.ICommonPageInterface;
import project.lighthouse.autotests.pages.invoice.InvoiceListPage;
import project.lighthouse.autotests.pages.product.ProductListPage;
import sun.reflect.generics.reflectiveObjects.NotImplementedException;

import java.text.DateFormatSymbols;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.*;

public class ICommonPage extends PageObject implements ICommonPageInterface {

    public static final String ERROR_MESSAGE = "No such option for '%s'";

    public ICommonPage(WebDriver driver) {
        super(driver);
    }

    public void isRequiredPageOpen(String pageObjectName){
        String defaultUrl = GetPageObjectDefaultUrl(pageObjectName).substring(50, 64);
        String actualUrl = getDriver().getCurrentUrl();
        if(!actualUrl.contains(defaultUrl)){
            String errorMessage = String.format("The %s is not open!\nActual url: %s\nExpected url: %s", pageObjectName, actualUrl, defaultUrl);
            throw new AssertionError(errorMessage);
        }
    }

    public String GetPageObjectDefaultUrl(String pageObjectName){
        switch (pageObjectName){
            case "ProductListPage":
                return ProductListPage.class.getAnnotations()[0].toString();
            case "InvoiceListPage":
                return InvoiceListPage.class.getAnnotations()[0].toString();
            default:
                return String.valueOf(new AssertionError("No such value!"));
        }
    }

    public String GenerateTestData(int n){
        String str = "a";
        String testData = new String(new char[n]).replace("\0", str);
        StringBuilder formattedData = new StringBuilder(testData);
        for(int i = 0; i < formattedData.length(); i++){
            if (i%26 == 1){
                formattedData.setCharAt(i, ' ');
            }
        }
        return formattedData.toString();
    }

    public boolean IsPresent(String xpath){
        try {
            return findBy(xpath).isPresent();
        }
        catch (Exception e){
            return false;
        }
    }

    public void CheckCreateAlertSuccess(String name){
        boolean isAlertPresent;
        Alert alert = null;
        try {
            alert = getAlert();
            isAlertPresent = true;
        }
        catch (Exception e){
            isAlertPresent = false;
        }
        if(isAlertPresent){
            String errorAlertMessage = "Ошибка";
            String alertText = alert.getText();
            if(alertText.contains(errorAlertMessage)){
                String errorMessage = String.format("Can't create new '%s'. Error alert is present. Alert text: %s", name, alertText);
                throw new AssertionError(errorMessage);
            }
        }
    }

    public void CheckFieldLength(String elementName, int fieldLength, WebElement element) {
        int length;
        switch (element.getTagName()){
            case "input":
                length = $(element).getTextValue().length();
                break;
            case "textarea":
                length = $(element).getValue().length();
                break;
            default:
                length = $(element).getText().length();
                break;
        }
        if(length != fieldLength){
            String errorMessage = String.format("The '%s' field doesn't contains '%s' symbols. It actually contains '%s' symbols.", elementName, fieldLength, length);
            throw new AssertionError(errorMessage);
        }
    }

    public static String GetTodayDate(){
        String pattern = "dd.MM.yyyy HH:mm";
        return new SimpleDateFormat(pattern).format(new Date());
    }

    public String getInputedText(String inputText){
        switch (inputText){
            case "todayDate":
                return GetTodayDate();
            default:
                return inputText;
        }
    }

    public void CheckErrorMessages(ExamplesTable errorMessageTable){
        for (Map<String, String> row : errorMessageTable.getRows()){
            String expectedErrorMessage = row.get("error message");
            String xpath = String.format("//*[contains(@lh_field_error,'%s')]", expectedErrorMessage);
            if(!IsPresent(xpath)){
                String errorXpath = "//*[@lh_field_error]";
                String errorMessage;
                if(IsPresent(errorXpath)){
                    List<WebElementFacade> webElementList = findAll(By.xpath(errorXpath));
                    StringBuilder builder = new StringBuilder("Another validation errors are present:");
                    for (WebElementFacade element : webElementList){
                        builder.append(element.getAttribute("lh_field_error"));
                    }
                    errorMessage = builder.toString();
                }
                else{
                    errorMessage = "There are no error field validation messages on the page!";
                }
                throw new AssertionError(errorMessage);
            }
        }
    }

    public void CheckNoErrorMessages(){
        String xpath = "//*[@lh_field_error]";
        if(IsPresent(xpath)){
            String errorMessage = "There are error field validation messages on the page!";
            throw new AssertionError(errorMessage);
        }
    }

    public void SetValue(CommonItem item, String value){
        switch (item.GetType()) {
            case date:
                InputDate(item.GetWebElement(), value);
                break;
            case input:
            case textarea:
                Input(item.GetWebElement(), value);
                break;
            case checkbox:
                SelectByValue(item.GetWebElement(), value);
                break;
            case autocomplete:
                AutoComplete(item.GetWebElement(), value);
                break;
            default:
                String errorMessage = String.format(ERROR_MESSAGE, item.GetType());
                throw new AssertionError(errorMessage);
        }
    }

    public void Input(WebElement element, String value){
        String inputText = getInputedText(value);
        $(element).type(inputText);
    }

    public void SelectByValue(WebElement element, String value){
        $(element).selectByValue(value);
    }

    public void InputDate(WebElement element, String value){
        if(value.startsWith("!")){
            Input(element, value.substring(1));
        }
        else{
            $(element).type("");
            if(value.equals("todayDate")){
                String todayDate = GetTodayDate();
                DatePickerInput(todayDate);
            }
            else{
                DatePickerInput(value);
            }
        }
    }

    public void DatePickerInput(String datePattern){

        String [] dateArray = datePattern.split(" ");
        String [] date = dateArray[0].split("\\.");

        String time = dateArray[1];
        String dayString = date[0];
        int monthInt = Integer.parseInt(date[1]);
        String monthString = GetMonthName(monthInt);
        int yearString = Integer.parseInt(date[2]);

        if(!(yearString == GetActualDatePickerYear())){
            SetYear(yearString);
        }
        if(!monthString.equals(GetActualDatePickerMonth())){
            SetMonth(monthInt);
        }

        SetDay(dayString);
        SetTime(time);
        findBy("//button[text()='Done']").click();
    }

    public String GetActualDatePickerMonth(){
        String actualDatePickerMonthXpath = "//div[@class='ui-datepicker-title']/span[@class='ui-datepicker-month']";
        return findBy(actualDatePickerMonthXpath).getText();
    }

    public int GetActualDatePickerYear(){
        String actualDatePickerYearXpath = "//div[@class='ui-datepicker-title']/span[@class='ui-datepicker-year']";
        return Integer.parseInt(findBy(actualDatePickerYearXpath).getText());
    }

    public void SetTime(String timeString){
        String [] time = timeString.split(":");
        String timePickerHourXpath = "//div[@class='ui_tpicker_hour_slider']//input";
        String timePickerMinuteXpath = "//div[@class='ui_tpicker_minute_slider']//input";
        findBy(timePickerHourXpath).type(time[0]);
        findBy(timePickerMinuteXpath).type(time[1]);
    }

    public void SetDay(String dayString){
        String timePickerDayXpath = String.format("//td[@data-handler='selectDay']/a[text()='%s']",dayString);
        findBy(timePickerDayXpath).click();
    }

    public void SetMonth(int monthValue){
        int getActualMonth = GetMonthNumber(GetActualDatePickerMonth());
        int actualMonthValue = 0;
        if(monthValue < getActualMonth){
            actualMonthValue = 0;
            while(!(monthValue == actualMonthValue)){
                findBy("//a[@data-handler='prev']").click();
                actualMonthValue = GetMonthNumber(GetActualDatePickerMonth());
            }
        }
        else if(monthValue > actualMonthValue){
            actualMonthValue = 0;
            while(!(monthValue == actualMonthValue)){
                findBy("//a[@data-handler='next']").click();
                actualMonthValue = GetMonthNumber(GetActualDatePickerMonth());
            }
        }
    }

    public void SetYear(int yearValue){
        if(yearValue < GetActualDatePickerYear()){
            int actualYearValue = 0;
            while(!(yearValue == actualYearValue)){
                findBy("//a[@data-handler='prev']").click();
                actualYearValue = GetActualDatePickerYear();
            }
        }
        else if(yearValue > GetActualDatePickerYear()){
            String errorMessage = String.format("Year '%s' cantbe more than current year '%s'", yearValue, GetActualDatePickerYear());
            throw new AssertionError(errorMessage);
        }
    }

    public String GetMonthName(int month) {
        Locale id = new Locale("en");
        return new DateFormatSymbols(id).getMonths()[month-1];
    }

    public int GetMonthNumber(String monthName){
        Date date = null;
        try {
            date = new SimpleDateFormat("MMM", Locale.ENGLISH).parse(monthName);
        } catch (ParseException e) {
            e.printStackTrace();
        }
        Calendar cal = Calendar.getInstance();
        cal.setTime(date);
        int month = cal.get(Calendar.MONTH);
        return month + 1;
    }

    public void AutoComplete(WebElement element, String value){
        throw new NotImplementedException();
    }
}
