package project.lighthouse.autotests.steps.api.builder;

import net.thucydides.core.annotations.Step;
import org.json.JSONException;
import project.lighthouse.autotests.api.factories.ApiFactory;
import project.lighthouse.autotests.api.objects.Product;
import project.lighthouse.autotests.api.objects.Store;
import project.lighthouse.autotests.api.objects.sale.SaleObject;
import project.lighthouse.autotests.api.objects.sale.SaleProduct;
import project.lighthouse.autotests.helper.DateTimeHelper;
import project.lighthouse.autotests.storage.Storage;
import project.lighthouse.autotests.storage.containers.user.UserContainer;

import java.io.IOException;

public class SaleBuilderSteps {

    private SaleObject sale;

    @Step
    public void createSale(String date) throws JSONException {
        String convertedDate = DateTimeHelper.getDate(date);
        Storage.getCustomVariableStorage().getSalesMap().put(date, convertedDate);
        sale = new SaleObject(convertedDate);
    }

    @Step
    public void payWithCash(String amountTendered) throws JSONException {
        sale.setPaymentMethod("cash");
        sale.setAmountTendered(amountTendered);
    }

    @Step
    public void payWithBankCard() throws JSONException {
        sale.setPaymentMethod("bankcard");
    }

    @Step
    public void putProductToSale(String productName,
                                 String quantity,
                                 String price) throws JSONException {
        Product product = Storage.getCustomVariableStorage().getProducts().get(productName);
        SaleProduct saleProduct = new SaleProduct(product.getId(), quantity, price);
        sale.putProduct(saleProduct);
    }

    @Step
    public void registerSale(String email,
                             String storeName) throws JSONException, IOException {
        UserContainer userContainer = Storage.getUserVariableStorage().getUserContainers().getContainerWithEmail(email);
        Store store = Storage.getCustomVariableStorage().getStores().get(storeName);
        sale.setStore(store);
        new ApiFactory(userContainer.getEmail(), userContainer.getPassword()).createObject(sale);
    }
}
