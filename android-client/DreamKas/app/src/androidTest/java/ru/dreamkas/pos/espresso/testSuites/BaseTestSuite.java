package ru.dreamkas.pos.espresso.testSuites;

import android.app.Activity;
import android.app.Instrumentation;
import android.content.Context;
import android.content.SharedPreferences;
import android.preference.PreferenceManager;
import android.test.ActivityInstrumentationTestCase2;
import android.util.Log;
import android.util.Pair;

import com.google.android.apps.common.testing.testrunner.ActivityLifecycleMonitorRegistry;
import com.google.android.apps.common.testing.testrunner.Stage;

import ru.dreamkas.pos.BuildConfig;
import ru.dreamkas.pos.ServerTuner;
import ru.dreamkas.pos.espresso.ScreenshotFailureHandler;
import ru.dreamkas.pos.remoteCommand.Status;
import ru.dreamkas.pos.view.activities.LoginActivity_;

import static com.google.android.apps.common.testing.ui.espresso.Espresso.setFailureHandler;
import static com.google.common.collect.Iterables.getOnlyElement;

public abstract class BaseTestSuite<T extends Activity> extends ActivityInstrumentationTestCase2<T> {
    private final ServerTuner mServerTuner;

    @SuppressWarnings("deprecation")
    protected BaseTestSuite(String pkg, Class<T> activityClass) {
        super(pkg, activityClass);

        String[] parts = BuildConfig.ServerAddress.split(".");
        String env = parts[parts.length - 4];
        String host = parts[parts.length - 5];

        mServerTuner = new ServerTuner("coquille.lighthouse.pro", 11300, env, host);
    }

    Activity getCurrentActivity() throws Throwable {
        getInstrumentation().waitForIdleSync();
        final Activity[] activity = new Activity[1];
        runTestOnUiThread(new Runnable() {
            @Override
            public void run() {
                java.util.Collection<Activity> activites = ActivityLifecycleMonitorRegistry.getInstance().getActivitiesInStage(Stage.RESUMED);
                activity[0] = getOnlyElement(activites);
            }});
        return activity[0];
    }

    protected ServerTuner getServerTuner(){
        return mServerTuner;
    }

    @Override
    public void tearDown() throws Exception {
        super.tearDown();
    }


    @Override
    protected void setUp() throws Exception
    {
        super.setUp();

        Pair<Status, String> result = mServerTuner.rebuildDatabase();
        checkPreconditionResult(result);

        String username = "androidpos@lighthouse.pro";
        String pswd = "lighthouse";
        result = mServerTuner.createUser(username, pswd);
        checkPreconditionResult(result);

        String groupName = "Товары";
        mServerTuner.createStoreByUserWithEmail("Магазин №1", "Москва, ул. Лесная, д. 5", username);
        mServerTuner.createStoreByUserWithEmail("Магазин №2", "пос. Лесное, ул. Ореховая, д. 25",username);
        mServerTuner.createGroupThroughPostByUserWithEmail(groupName, username);

        mServerTuner.createProduct("Товар1", "кг.", "111111111", "10", 150d, 250d, groupName, username);
        mServerTuner.createProduct("Вар2", "литр", "'22222222'", "0", 110d, 130d, groupName, username);
        mServerTuner.createProduct("Товар3", "пятюни", "'33333333'", "18", 80d, 100d, groupName, username);
        mServerTuner.createProduct("Товар без цены продажи", "пятюни", "2666666", "18", 80d, null, groupName, username);

        clearPreferences();
        getActivity();
    }

    private void checkPreconditionResult(Pair<Status, String> result) {
        System.out.print(result.first + "____" + result.second);

        if(result.first == Status.UNKNOWN || result.first == Status.FAILED){
            throw new ExceptionInInitializerError(result.second);
        }
    }

    private void clearPreferences() {
        Instrumentation instrumentation = getInstrumentation();
        SharedPreferences preferences = PreferenceManager.getDefaultSharedPreferences(instrumentation.getTargetContext());
        preferences.edit().clear().commit();
    }
}
