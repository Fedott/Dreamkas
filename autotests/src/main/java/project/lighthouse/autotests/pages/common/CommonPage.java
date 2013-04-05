package project.lighthouse.autotests.pages.common;

import net.thucydides.core.pages.PageObject;
import net.thucydides.core.pages.WebElementFacade;
import org.jbehave.core.model.ExamplesTable;
import org.openqa.selenium.Alert;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import project.lighthouse.autotests.CommonPageInterface;
import project.lighthouse.autotests.pages.invoice.InvoiceListPage;
import project.lighthouse.autotests.pages.product.ProductListPage;
import sun.reflect.generics.reflectiveObjects.NotImplementedException;

import java.text.DateFormatSymbols;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.*;

public class CommonPage extends PageObject implements CommonPageInterface {

    public static final String ERROR_MESSAGE = "No such option for '%s'";
    public static final String STRING_EMPTY = "";
    public static final String DATE_PATTERN = "dd.MM.yyyy";
    public static final String DATE_TIME_PATTERN = "dd.MM.yyyy HH:mm";
    public final Locale locale = new Locale("ru");

    public CommonPage(WebDriver driver) {
        super(driver);
    }

    public void isRequiredPageOpen(String pageObjectName){
        String defaultUrl = getPageObjectDefaultUrl(pageObjectName).replaceFirst(".*\\(value=(.*)\\)", "$1");
        String actualUrl = getDriver().getCurrentUrl();
        if(!actualUrl.contains(defaultUrl)){
            String errorMessage = String.format("The %s is not open!\nActual url: %s\nExpected url: %s", pageObjectName, actualUrl, defaultUrl);
            throw new AssertionError(errorMessage);
        }
    }

    public String getPageObjectDefaultUrl(String pageObjectName){
        switch (pageObjectName){
            case "ProductListPage":
                return ProductListPage.class.getAnnotations()[0].toString();
            case "InvoiceListPage":
                return InvoiceListPage.class.getAnnotations()[0].toString();
            default:
                String errorMessage = String.format(ERROR_MESSAGE, pageObjectName);
                throw new AssertionError(errorMessage);
        }
    }

    public String generateTestData(int n){
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

    public boolean isPresent(String xpath){
        try {
            return findBy(xpath).isPresent();
        }
        catch (Exception e){
            return false;
        }
    }

    public void checkCreateAlertSuccess(String name){
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

    public void checkFieldLength(String elementName, int fieldLength, WebElement element) {
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

    public static String getTodayDate(String pattern){
        return new SimpleDateFormat(pattern).format(new Date());
    }

    public String getInputedText(String inputText){
        switch (inputText){
            case "todayDateAndTime":
                return getTodayDate(DATE_TIME_PATTERN);
            case "todayDate":
                return getTodayDate(DATE_PATTERN);
            default:
                return inputText;
        }
    }

    public void checkErrorMessages(ExamplesTable errorMessageTable){
        for (Map<String, String> row : errorMessageTable.getRows()){
            String expectedErrorMessage = row.get("error message");
            String xpath = String.format("//*[contains(@lh_field_error,'%s')]", expectedErrorMessage);
            if(!isPresent(xpath)){
                String errorXpath = "//*[@lh_field_error]";
                String errorMessage;
                if(isPresent(errorXpath)){
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

    public void checkNoErrorMessages(ExamplesTable errorMessageTable){
        for (Map<String, String> row : errorMessageTable.getRows()){
            String expectedErrorMessage = row.get("error message");
            String xpath = String.format("//*[contains(@lh_field_error,'%s')]", expectedErrorMessage);
            if(isPresent(xpath)){
                String errorMessage = ("The error message is present!");
                throw new AssertionError(errorMessage);
            }
        }
    }

    public void checkNoErrorMessages(){
        String xpath = "//*[@lh_field_error]";
        if(isPresent(xpath)){
            String errorMessage = "There are error field validation messages on the page!";
            throw new AssertionError(errorMessage);
        }
    }

    public void setValue(CommonItem item, String value){
        switch (item.getType()) {
            case dateTime:
                dateInputAction(item.getWebElement(), value);
                break;
            case date:
                dateInputAction(item.getWebElement(), value) ;
                break;
            case input:
            case textarea:
                input(item.getWebElement(), value);
                break;
            case select:
                selectByValue(item.getWebElement(), value);
                break;
            case autocomplete:
                autoComplete(item.getWebElement(), value);
                break;
            default:
                String errorMessage = String.format(ERROR_MESSAGE, item.getType());
                throw new AssertionError(errorMessage);
        }
    }

    public void input(WebElement element, String value){
        String inputText = getInputedText(value);
        $(element).type(inputText);
    }

    public void selectByValue(WebElement element, String value){
        $(element).selectByValue(value);
    }

    public void dateInputAction(WebElement element, String value){
        if(value.startsWith("!")){
            input(element, value.substring(1));
        }
        else{
            /*$(element).type(STRING_EMPTY);*/
            $(element).click();
            switch (value){
                case "todayDateAndTime":
                    value = getTodayDate(DATE_TIME_PATTERN);
                    break;
                case "todayDate":
                    value = getTodayDate(DATE_PATTERN);
                    break;
            }
            dateTimePickerInput(value);
        }
    }

    public void dateTimePickerInput(String datePattern){
        String [] dateArray = datePattern.split(" ");
        String [] date = dateArray[0].split("\\.");
        String dayString = date[0];
        int monthInt = Integer.parseInt(date[1]);
        String monthString = getMonthName(monthInt);
        int yearString = Integer.parseInt(date[2]);
        if(!(yearString == getActualDatePickerYear())){
            setYear(yearString);
        }
        if(!monthString.equals(getActualDatePickerMonth())){
            setMonth(monthInt);
        }
        setDay(dayString);
        if(dateArray.length == 2){
        setTime(dateArray[1]);
        findBy("//button[normalize-space(text())='Закрыть']").click();
        }
    }

    public String getActualDatePickerMonth(){
        String actualDatePickerMonthXpath = "//div[@class='ui-datepicker-title']/span[@class='ui-datepicker-month']";
        return findBy(actualDatePickerMonthXpath).getText();
    }

    public int getActualDatePickerYear(){
        String actualDatePickerYearXpath = "//div[@class='ui-datepicker-title']/span[@class='ui-datepicker-year']";
        return Integer.parseInt(findBy(actualDatePickerYearXpath).getText());
    }

    public void setTime(String timeString){
        String [] time = timeString.split(":");
        String timePickerHourXpath = "//div[@class='ui_tpicker_hour_slider']//input";
        String timePickerMinuteXpath = "//div[@class='ui_tpicker_minute_slider']//input";
        findBy(timePickerHourXpath).type(time[0]);
        findBy(timePickerMinuteXpath).type(time[1]);
    }

    public void setDay(String dayString){
        if(dayString.startsWith("0")){
            dayString = dayString.substring(1);
        }
        String timePickerDayXpath = String.format("//td[@data-handler='selectDay']/a[normalize-space(text())='%s']",dayString);
        findBy(timePickerDayXpath).click();
    }

    public void setMonth(int monthValue){
        int getActualMonth = getMonthNumber(getActualDatePickerMonth());
        int actualMonthValue = 0;
        if(monthValue < getActualMonth){
            actualMonthValue = 0;
            while(!(monthValue == actualMonthValue)){
                findBy("//a[@data-handler='prev']").click();
                actualMonthValue = getMonthNumber(getActualDatePickerMonth());
            }
        }
        else if(monthValue > actualMonthValue){
            actualMonthValue = 0;
            while(!(monthValue == actualMonthValue)){
                findBy("//a[@data-handler='next']").click();
                actualMonthValue = getMonthNumber(getActualDatePickerMonth());
            }
        }
    }

    public void setYear(int yearValue){
        if(yearValue < getActualDatePickerYear()){
            int actualYearValue = 0;
            while(!(yearValue == actualYearValue)){
                findBy("//a[@data-handler='prev']").click();
                actualYearValue = getActualDatePickerYear();
            }
        }
        else if(yearValue > getActualDatePickerYear()){
            String errorMessage = String.format("Year '%s' cantbe more than current year '%s'", yearValue, getActualDatePickerYear());
            throw new AssertionError(errorMessage);
        }
    }

    public String getMonthName(int month) {
        return new DateFormatSymbols(locale).getMonths()[month-1];
    }

    public int getMonthNumber(String monthName){
        Date date = null;
        try {
            date = new SimpleDateFormat("MMM", locale).parse(monthName);

        } catch (ParseException e) {
            String errorMessage = String.format("SimpleDateFormat parse error! Error message: '%s'", e.getMessage());
            throw new AssertionError(errorMessage);
        }
        Calendar cal = Calendar.getInstance();
        cal.setTime(date);
        int month = cal.get(Calendar.MONTH);
        return month + 1;
    }

    public void autoComplete(WebElement element, String value){
        throw new NotImplementedException();
    }

    public void shouldContainsText(WebElement element, String expectedValue){
        String actualValue = $(element).getTextValue();
        if(!actualValue.contains(expectedValue)){
            String errorMessage = String.format("Element doesnt contains '%s'. It contains '%s'", expectedValue, actualValue);
            throw new AssertionError(errorMessage);
        }
    }
}
