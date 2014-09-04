package project.lighthouse.autotests.storage;

import project.lighthouse.autotests.guice.Injectors;
import project.lighthouse.autotests.storage.variable.*;

public class Storage {

    public static OrderVariableStorage getOrderVariableStorage() {
        return Injectors.getInjector().getInstance(OrderVariableStorage.class);
    }

    public static UserVariableStorage getUserVariableStorage() {
        return Injectors.getInjector().getInstance(UserVariableStorage.class);
    }

    public static StoreVariableStorage getStoreVariableStorage() {
        return Injectors.getInjector().getInstance(StoreVariableStorage.class);
    }

    public static InvoiceVariableStorage getInvoiceVariableStorage() {
        return Injectors.getInjector().getInstance(InvoiceVariableStorage.class);
    }

    public static StockMovementVariableStorage getStockMovementVariableStorage() {
        return Injectors.getInjector().getInstance(StockMovementVariableStorage.class);
    }

    public static CustomVariableStorage getCustomVariableStorage() {
        return Injectors.getInjector().getInstance(CustomVariableStorage.class);
    }

    public static CurrentPageObjectStorage getCurrentPageObjectStorage() {
        return Injectors.getInjector().getInstance(CurrentPageObjectStorage.class);
    }

    public static Configurable getConfigurationVariableStorage() {
        return Injectors.getInjector().getInstance(Configurable.class);
    }

    public static DemoModeConfigurable getDemoModeConfigurableStorage() {
        return Injectors.getInjector().getInstance(DemoModeConfigurable.class);
    }

    public static StorageClearable getStorageClearable() {
        return Injectors.getInjector().getInstance(StorageClearable.class);
    }
}
