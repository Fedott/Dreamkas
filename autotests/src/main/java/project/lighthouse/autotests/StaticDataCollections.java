package project.lighthouse.autotests;

import project.lighthouse.autotests.objects.*;

import java.util.HashMap;

public class StaticDataCollections {

    public static HashMap<String, Product> products = new HashMap<>();
    public static HashMap<String, Invoice> invoices = new HashMap<>();
    public static HashMap<String, WriteOff> writeOffs = new HashMap<>();
    public static HashMap<String, Klass> klasses = new HashMap<>();
    public static HashMap<String, User> users = new HashMap<>();
    public static String TIMEOUT = "5000";
    public static String WEB_DRIVER_BASE_URL;
}
