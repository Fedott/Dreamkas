package project.lighthouse.autotests.pages.elements;

import org.openqa.selenium.By;
import project.lighthouse.autotests.common.CommonItem;
import project.lighthouse.autotests.common.CommonPageObject;

import java.text.DateFormatSymbols;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

public class DateTime extends CommonItem {

    public static final String DATE_PATTERN = "dd.MM.yyyy";
    public static final String DATE_TIME_PATTERN = "dd.MM.yyyy HH:mm";
    public final Locale locale = new Locale("ru");

    public DateTime(CommonPageObject pageObject, By findBy) {
        super(pageObject, findBy);
    }

    public DateTime(CommonPageObject pageObject, String name) {
        super(pageObject, name);
    }

    @Override
    public void setValue(String value) {
        if (value.startsWith("!")) {
            String parsedValue = getDate(value.substring(1));
            $().type(parsedValue);
            dateTimePickerClose();
        } else {
            $().click();
            String parsedValue = getDate(value);
            dateTimePickerInput(parsedValue);
        }
    }

    public String getDate(String value) {
        switch (value) {
            case "todayDateAndTime":
                return getTodayDate(DATE_TIME_PATTERN);
            case "todayDate":
                return getTodayDate(DATE_PATTERN);
            default:
                if (value.contains("-")) {
                    String replacedValue = value.replaceFirst(".+-([0-3]?[0-9]).*", "$1");
                    int numberOfDay = Integer.parseInt(replacedValue);
                    return getTodayDate(DATE_TIME_PATTERN, numberOfDay);
                }
                return value;
        }
    }

    public static String getTodayDate(String pattern) {
        return new SimpleDateFormat(pattern).format(new Date());
    }

    public static String getTodayDate(String pattern, int day) {
        return new SimpleDateFormat(pattern).format(new org.joda.time.DateTime().minusDays(day).toDate());
    }

    public void dateTimePickerInput(String datePattern) {
        String[] dateArray = datePattern.split(" ");
        String[] date = dateArray[0].split("\\.");
        String dayString = date[0];
        int monthInt = Integer.parseInt(date[1]);
        String monthString = getMonthName(monthInt);
        int yearString = Integer.parseInt(date[2]);
        if (!(yearString == getActualDatePickerYear())) {
            setYear(yearString);
        }
        if (!monthString.equals(getActualDatePickerMonth())) {
            setMonth(monthInt);
        }
        setDay(dayString);
        if (dateArray.length == 2) {
            setTime(dateArray[1]);
            dateTimePickerClose();
        }
    }

    public void dateTimePickerClose() {
        String dateTimePickerCloseButtonXpath = "//*[@class='button button_color_blue datepicker__saveLink']";
        pageObject.findBy(dateTimePickerCloseButtonXpath).click();
    }

    public String getActualDatePickerMonth() {
        String actualDatePickerMonthXpath = "//*[@class='datepicker__monthName']";
        return pageObject.findBy(actualDatePickerMonthXpath).getText();
    }

    public int getActualDatePickerYear() {
        String actualDatePickerYearXpath = "//*[@class='datepicker__yearNum']";
        return Integer.parseInt(pageObject.findBy(actualDatePickerYearXpath).getText());
    }

    public void setTime(String timeString) {
        String[] time = timeString.split(":");
        pageObject.find(By.name("hours")).type(time[0]);
        pageObject.find(By.name("minutes")).type(time[1]);
    }

    public void setDay(String dayString) {
        if (dayString.startsWith("0")) {
            dayString = dayString.substring(1);
        }
        String timePickerDayXpath =
                String.format("//*[@class='datepicker__dateList']/*[normalize-space(@class='datepicker__dateItem') and normalize-space(text())='%s']", dayString);
        pageObject.findBy(timePickerDayXpath).click();
    }

    public void setMonth(int monthValue) {
        int getActualMonth = getMonthNumber(getActualDatePickerMonth());
        int actualMonthValue = 0;
        if (monthValue < getActualMonth) {
            actualMonthValue = 0;
            while (!(monthValue == actualMonthValue)) {
                pageObject.findBy("//*[@class='datepicker__prevMonthLink']").click();
                actualMonthValue = getMonthNumber(getActualDatePickerMonth());
            }
        } else if (monthValue > actualMonthValue) {
            actualMonthValue = 0;
            while (!(monthValue == actualMonthValue)) {
                pageObject.findBy("//*[@class='datepicker__nextMonthLink']").click();
                actualMonthValue = getMonthNumber(getActualDatePickerMonth());
            }
        }
    }

    public void setYear(int yearValue) {
        int actualYear = Calendar.getInstance().get(Calendar.YEAR);
        if (yearValue < getActualDatePickerYear()) {
            int actualYearValue = 0;
            while (!(yearValue == actualYearValue)) {
                pageObject.findBy("//*[@class='datepicker__prevMonthLink']").click();
                actualYearValue = getActualDatePickerYear();
            }
        } else if (yearValue > actualYear) {
            String errorMessage = String.format("Year '%s' cantbe older than current year '%s'", yearValue, actualYear);
            throw new AssertionError(errorMessage);
        }
    }

    public String getMonthName(int month) {
        return new DateFormatSymbols(locale).getMonths()[month - 1];
    }

    public int getMonthNumber(String monthName) {
        Date date;
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
}
