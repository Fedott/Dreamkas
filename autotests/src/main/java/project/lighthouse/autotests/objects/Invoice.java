package project.lighthouse.autotests.objects;

import org.json.JSONException;
import org.json.JSONObject;

public class Invoice extends AbstractObject {

    String storeId;

    private static final String API_URL = "/stores/%s/invoices";

    public Invoice(JSONObject jsonObject) throws JSONException {
        super(jsonObject);
    }

    public Invoice(String sku, String supplier, String acceptanceDate, String accepter, String legalEntity,
                   String supplierInvoiceSku, String supplierInvoiceDate) throws JSONException {
        this(new JSONObject()
                .put("sku", sku)
                .put("supplier", supplier)
                .put("acceptanceDate", acceptanceDate)
                .put("accepter", accepter)
                .put("legalEntity", legalEntity)
                .put("supplierInvoiceSku", supplierInvoiceSku)
                .put("supplierInvoiceDate", supplierInvoiceDate)
        );
    }

    @Override
    public String getApiUrl() {
        return String.format(API_URL, storeId);
    }

    public String getSku() throws JSONException {
        return getPropertyAsString("sku");
    }

    public void setStoreId(String storeId) {
        this.storeId = storeId;
    }
}
