package project.lighthouse.autotests.objects;

import org.json.JSONException;
import org.json.JSONObject;

public class Product extends AbstractObject {

    private static final String API_URL = "/products";

    public Product(JSONObject jsonObject) {
        this.jsonObject = jsonObject;
    }

    public Product(String name, String units, String vat, String purchasePrice, String barcode,
                   String sku, String vendorCountry, String vendor, String info,
                   String subCategory, String retailMarkupMax,
                   String retailMarkupMin) throws JSONException {
        this(new JSONObject()
                .put("name", name)
                .put("units", units)
                .put("vat", vat)
                .put("purchasePrice", purchasePrice)
                .put("barcode", barcode)
                .put("sku", sku)
                .put("vendorCountry", vendorCountry)
                .put("vendor", vendor)
                .put("info", info)
                .put("subCategory", subCategory)
                .put("retailMarkupMax", retailMarkupMax)
                .put("retailMarkupMin", retailMarkupMin)
        );
    }

    public String getSku() throws JSONException {
        return getPropertyAsString("sku");
    }

    @Override
    public String getApiUrl() {
        return API_URL;
    }
}
