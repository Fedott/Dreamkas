package project.lighthouse.autotests.storage.variable;

import project.lighthouse.autotests.objects.api.Product;
import project.lighthouse.autotests.objects.api.Store;
import project.lighthouse.autotests.objects.api.Supplier;
import project.lighthouse.autotests.objects.api.product.ExtraBarcode;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class CustomVariableStorage {

    private List<ExtraBarcode> extraBarcodes = new ArrayList<>();

    private String email;

    private String name;

    private String mainWindowHandle;

    public List<ExtraBarcode> getExtraBarcodes() {
        return extraBarcodes;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getMainWindowHandle() {
        return mainWindowHandle;
    }

    public void setMainWindowHandle(String mainWindowHandle) {
        this.mainWindowHandle = mainWindowHandle;
    }

    private Map<String, Supplier> suppliers = new HashMap<>();

    public Map<String, Supplier> getSuppliers() {
        return suppliers;
    }

    private HashMap<String, Store> stores = new HashMap<>();

    public HashMap<String, Store> getStores() {
        return stores;
    }

    private HashMap<String, Product> products = new HashMap<>();

    public HashMap<String, Product> getProducts() {
        return products;
    }
}
