package project.lighthouse.autotests;

import project.lighthouse.autotests.objects.api.Category;
import project.lighthouse.autotests.objects.api.Group;
import project.lighthouse.autotests.objects.api.Product;
import project.lighthouse.autotests.objects.api.SubCategory;
import project.lighthouse.autotests.storage.Storage;

import java.util.ArrayList;
import java.util.HashMap;

public class StaticData {

    public static HashMap<String, Group> groups = new HashMap<>();
    public static HashMap<String, Category> categories = new HashMap<>();
    public static HashMap<String, SubCategory> subCategories = new HashMap<>();
    public static HashMap<String, ArrayList<Product>> subCategoryProducts = new HashMap<>();

    public static Boolean isGroupCreated(String groupName) {
        return groups.containsKey(groupName);
    }

    public static Boolean hasSubCategory(String subCategoryName) {
        return subCategories.containsKey(subCategoryName);
    }

    public static void clear() {
        groups.clear();
        categories.clear();
        subCategories.clear();
        subCategoryProducts.clear();
        Storage.getOrderVariableStorage().resetNumber();
        Storage.getInvoiceVariableStorage().resetNumber();
        Storage.getUserVariableStorage().getUserContainers().clear();
        Storage.getCustomVariableStorage().getSuppliers().clear();
        Storage.getUserVariableStorage().getUserTokens().clear();
        Storage.getCustomVariableStorage().getStores().clear();
        Storage.getCustomVariableStorage().getProducts().clear();
    }
}
