package ru.dreamkas.jbehave.api.builder.stockMovement;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.Given;
import org.json.JSONException;
import ru.dreamkas.api.objects.Product;
import ru.dreamkas.api.objects.Store;
import ru.dreamkas.api.objects.Supplier;
import ru.dreamkas.steps.api.builder.SupplierReturnBuilderSteps;
import ru.dreamkas.storage.Storage;

import java.io.IOException;

public class SupplierReturnBuilderUserSteps {

    @Steps
    SupplierReturnBuilderSteps supplierReturnBuilderSteps;

    @Given("пользователь создает апи объект возвратом поставщику с датой '$date', статусом Оплачено '$paid', магазином с именем '$storeName', поставщиком с именем '$supplierName'")
    public void givenTheUserWithEmailCreatesInvoiceApiObject(String date, Boolean paid, String storeName, String supplierName) throws JSONException {
        Store store = Storage.getCustomVariableStorage().getStores().get(storeName);
        Supplier supplier = Storage.getCustomVariableStorage().getSuppliers().get(supplierName);
        supplierReturnBuilderSteps.build(date, store.getId(), paid, supplier.getId());
    }

    @Given("пользователь добавляет продукт с именем '$name', ценой '$price' и количеством '$quantity' к апи объекту возврата поставщику")
    public void givenTheUserAddsTheProductToInvoiceApiObject(String name, String price, String quantity) throws JSONException {
        Product product = Storage.getCustomVariableStorage().getProducts().get(name);
        supplierReturnBuilderSteps.addProduct(
                product.getId(),
                quantity,
                price);
    }

    @Given("пользователь с адресом электронной почты '$email' создает апи объект возврата поставщику")
    public void givenTheUserWithEmailCreatesInvoiceWithBuilderSteps(String email) throws IOException, JSONException {
        supplierReturnBuilderSteps.send(email);
    }
}
