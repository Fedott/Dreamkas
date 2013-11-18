package project.lighthouse.autotests.steps.api.commercialManager;

import net.thucydides.core.annotations.Step;
import org.json.JSONException;
import project.lighthouse.autotests.objects.api.Store;

import java.io.IOException;

public class StoreApiSteps extends CommercialManagerApi {

    public StoreApiSteps() throws JSONException {
    }

    @Step
    public Store createStoreThroughPost() throws IOException, JSONException {
        return apiConnect.createStoreThroughPost(new Store());
    }

    @Step
    public Store createStoreThroughPost(String number, String address, String contacts) throws IOException, JSONException {
        Store store = new Store(number, address, contacts);
        return apiConnect.createStoreThroughPost(store);
    }

    @Step
    public String getStoreId(String storeNumber) throws JSONException {
        return apiConnect.getStoreId(storeNumber);
    }
}
