package ru.dreamkas.pos;

import android.app.Application;
import android.content.Context;

public class DreamkasApp extends Application {

    private static Context mContext;

    @Override
    public void onCreate() {
        super.onCreate();
        mContext = this;
    }

    public static Context getContext(){
        return mContext;
    }

    public static String getResourceString(Integer id){
        return mContext.getResources().getString(id);
    }
}
