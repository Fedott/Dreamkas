package project.lighthouse.autotests.objects.api;

import org.json.JSONException;
import org.json.JSONObject;
import project.lighthouse.autotests.objects.api.abstraction.AbstractObject;

public class Product extends AbstractObject {

    public static final String TYPE_UNIT = "unit";
    public static final String TYPE_WEIGHT = "weight";
    private static final String API_URL = "/products";

    public Product(JSONObject jsonObject) {
        this.jsonObject = jsonObject;
    }

    @Deprecated
    public Product(String name,
                   String type,
                   String vat,
                   String purchasePrice,
                   String barcode,
                   String vendorCountry,
                   String vendor,
                   String subCategory,
                   String retailMarkupMax,
                   String retailMarkupMin,
                   String rounding) throws JSONException {
        this(new JSONObject()
                        .put("name", name)
                        .put("type", type)
                        .put("vat", vat)
                        .put("purchasePrice", purchasePrice)
                        .put("barcode", barcode)
                        .put("vendorCountry", vendorCountry)
                        .put("vendor", vendor)
                        .put("subCategory", subCategory)
                        .put("retailMarkupMax", retailMarkupMax)
                        .put("retailMarkupMin", retailMarkupMin)
                        .put("rounding", rounding)
        );
    }

    @Deprecated
    public Product(String name,
                   String type,
                   String vat,
                   String purchasePrice,
                   String barcode,
                   String vendorCountry,
                   String vendor,
                   String subCategory,
                   String retailMarkupMax,
                   String retailMarkupMin,
                   String rounding,
                   String nameOnScales,
                   String descriptionOnScales,
                   String ingredients,
                   String nutritionFacts,
                   String shelfLife) throws JSONException {
        this(new JSONObject()
                        .put("name", name)
                        .put("type", type)
                        .put("vat", vat)
                        .put("purchasePrice", purchasePrice)
                        .put("barcode", barcode)
                        .put("vendorCountry", vendorCountry)
                        .put("vendor", vendor)
                        .put("subCategory", subCategory)
                        .put("retailMarkupMax", retailMarkupMax)
                        .put("retailMarkupMin", retailMarkupMin)
                        .put("rounding", rounding)
                        .put("typeProperties", new JSONObject()
                                        .put("nameOnScales", nameOnScales)
                                        .put("descriptionOnScales", descriptionOnScales)
                                        .put("ingredients", ingredients)
                                        .put("nutritionFacts", nutritionFacts)
                                        .put("shelfLife", shelfLife)
                        )
        );
    }

    @Deprecated
    public Product(String name,
                   String type,
                   String vat,
                   String purchasePrice,
                   String barcode,
                   String vendorCountry,
                   String vendor,
                   String subCategory,
                   String retailMarkupMax,
                   String retailMarkupMin,
                   String rounding,
                   String alcoholByVolume,
                   String volume) throws JSONException {
        this(new JSONObject()
                        .put("name", name)
                        .put("type", type)
                        .put("vat", vat)
                        .put("purchasePrice", purchasePrice)
                        .put("barcode", barcode)
                        .put("vendorCountry", vendorCountry)
                        .put("vendor", vendor)
                        .put("subCategory", subCategory)
                        .put("retailMarkupMax", retailMarkupMax)
                        .put("retailMarkupMin", retailMarkupMin)
                        .put("rounding", rounding)
                        .put("typeProperties", new JSONObject()
                                        .put("alcoholByVolume", alcoholByVolume)
                                        .put("volume", volume)
                        )
        );
    }

    public Product(String name,
                   String units,
                   String barcode,
                   String vat,
                   String purchasePrice,
                   String sellingPrice,
                   String groupId) throws JSONException {
        this(new JSONObject()
                        .put("type", "unit")
                        .put("name", name)
                        .put("units", units)
                        .put("barcode", barcode)
                        .put("vat", vat)
                        .put("purchasePrice", purchasePrice)
                        .put("sellingPrice", sellingPrice)
                        .put("subCategory", groupId)
        );
    }

    @Override
    public String getApiUrl() {
        return API_URL;
    }

    public String getSku() throws AssertionError {
        return getPropertyAsString("sku");
    }

    public String getBarCode() throws AssertionError {
        return getPropertyAsString("barcode");
    }
}
