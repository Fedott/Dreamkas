package project.lighthouse.autotests.objects.api;

import org.json.JSONException;
import org.json.JSONObject;

@Deprecated
public class WriteOffProduct {

    public static JSONObject getJsonObject(String product, String quantity, String price, String cause) throws JSONException {
        return new JSONObject()
                .put("product", product)
                .put("quantity", quantity)
                .put("price", price)
                .put("cause", cause);
    }
}
