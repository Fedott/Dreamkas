package project.lighthouse.autotests.steps.deprecated.api.commercialManager;

import net.thucydides.core.annotations.Step;
import org.json.JSONException;
import project.lighthouse.autotests.api.ApiConnect;
import project.lighthouse.autotests.objects.api.Store;
import project.lighthouse.autotests.storage.Storage;
import project.lighthouse.autotests.storage.containers.user.UserContainer;

import java.io.IOException;

public class StoreApiSteps extends OwnerApi {

    @Step
    public Store createStoreThroughPost() throws IOException, JSONException {
        return apiConnect.createStoreThroughPost(new Store());
    }

    @Step
    public Store createStoreThroughPost(String number,
                                        String address,
                                        String contacts) throws IOException, JSONException {
        Store store = new Store(number, address, contacts);
        store = apiConnect.createStoreThroughPost(store);

        Storage.getStoreVariableStorage().setStore(store);

        return store;
    }

    @Step
    public String getStoreId(String storeNumber) throws JSONException {
        return apiConnect.getStoreId(storeNumber);
    }

    @Step
    public Store createStoreThroughPostByUserWithEmail(String number,
                                                       String address,
                                                       String contacts,
                                                       String email) throws JSONException, IOException {
        Store store = new Store(number, address, contacts);
        UserContainer userContainer = Storage.getUserVariableStorage().getUserContainers().getContainerWithEmail(email);

        store = new ApiConnect(userContainer.getEmail(), userContainer.getPassword()).createStoreThroughPost(store);
        Storage.getStoreVariableStorage().setStore(store);
        userContainer.setStore(store);
        return store;
    }
}
