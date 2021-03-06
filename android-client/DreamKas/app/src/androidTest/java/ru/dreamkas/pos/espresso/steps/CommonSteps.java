package ru.dreamkas.pos.espresso.steps;


import android.util.Pair;

import com.squareup.spoon.Spoon;

import java.util.concurrent.Callable;

import ru.dreamkas.pos.R;
import ru.dreamkas.pos.controller.Command;
import ru.dreamkas.pos.model.DrawerMenu;

import static com.google.android.apps.common.testing.ui.espresso.Espresso.onView;
import static com.google.android.apps.common.testing.ui.espresso.Espresso.pressBack;
import static com.google.android.apps.common.testing.ui.espresso.action.ViewActions.clearText;
import static com.google.android.apps.common.testing.ui.espresso.action.ViewActions.click;
import static com.google.android.apps.common.testing.ui.espresso.action.ViewActions.closeSoftKeyboard;
import static com.google.android.apps.common.testing.ui.espresso.action.ViewActions.pressKey;
import static com.google.android.apps.common.testing.ui.espresso.action.ViewActions.typeText;
import static com.google.android.apps.common.testing.ui.espresso.assertion.ViewAssertions.matches;
import static com.google.android.apps.common.testing.ui.espresso.matcher.ViewMatchers.isDisplayed;
import static com.google.android.apps.common.testing.ui.espresso.matcher.ViewMatchers.withId;
import static com.google.android.apps.common.testing.ui.espresso.matcher.ViewMatchers.withText;
import static ru.dreamkas.pos.espresso.EspressoHelper.waitForView;
import static ru.dreamkas.pos.espresso.EspressoHelper.waitKeyboard;

public class CommonSteps {

    static Command clickOnViewWithIdCommand = new Command<Integer>() {
        @Override
        public void execute(Integer viewId){
            onView(withId(viewId)).perform(click());
        }};

    static Command clickOnViewWithTextCommand = new Command<String>() {
        @Override
        public void execute(String text){
            onView(withText(text)).perform(click());
        }};

    static Command typeOnViewWithIdCommand = new Command<Pair<Integer, String>>() {
        @Override
        public void execute(Pair<Integer, String> data) {
            Integer viewId = data.first;
            String text = data.second;
            onView(withId(viewId)).perform(clearText()).perform(typeText(text), closeSoftKeyboard());
            waitKeyboard(500);
        }
    };



    public static void pressBackButton() {
        pressBack();
    }

    public static void clickOnViewWithId(int viewId) throws Throwable {
        //waitForView(viewId, 10000, isDisplayed());

        tryInTime(clickOnViewWithIdCommand, viewId);
    }

    public static void clickOnViewWithText(String text) throws Throwable {
        tryInTime(clickOnViewWithTextCommand, text);
    }

    public static void typeOnViewWithId(int viewId, String text) throws Throwable {
        tryInTime(typeOnViewWithIdCommand, new Pair<Integer, String>(viewId, text));
    }

    public static void exitFromApp() {
        DrawerSteps.select(DrawerMenu.AppStates.Exit);
        onView(withText("Да")).perform(click());
    }

    static void tryInTime(Command callable, Object data) throws Throwable {
       tryInTime(callable, data, 10000);
    }

    static void tryInTime(Command callable, Object data, long timeout) throws Throwable {
        final long startTime = System.currentTimeMillis();
        final long endTime = startTime + timeout;
        Throwable possibleException;

        do {
            try {
                callable.execute(data);
                return;
                //todo replace with PerformException NoMatchingViewException AssertionFailedError???
            } catch (Throwable ex) {
                possibleException = ex;
                Thread.yield();
            }
        } while (System.currentTimeMillis() < endTime);

        throw new Exception("tryInTime() failed by timeout" + " currentTime: " + System.currentTimeMillis() + " endTime: " +  endTime + " with: " + possibleException.getMessage(), possibleException);
    }


}
