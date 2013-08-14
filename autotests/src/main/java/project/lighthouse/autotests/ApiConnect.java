package project.lighthouse.autotests;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.methods.HttpEntityEnclosingRequestBase;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.methods.HttpPut;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicHeader;
import org.apache.http.protocol.HTTP;
import org.apache.http.util.EntityUtils;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import project.lighthouse.autotests.elements.DateTime;
import project.lighthouse.autotests.objects.*;

import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.util.Iterator;

import static junit.framework.Assert.assertEquals;
import static junit.framework.Assert.fail;

public class ApiConnect {

    String userName;
    String password;

    String retailMarkupMax;
    String retailMarkupMin;

    public ApiConnect(String userName, String password) throws JSONException {
        this.userName = userName;
        this.password = password;
    }

    public void createStoreDepartmentThroughPost(String number, String name) throws JSONException, IOException {
        if (!StaticData.hasStore(Store.DEFAULT_NUMBER)) {
            createStoreThroughPost();
        }
        createStoreDepartmentThroughPost(number, name, Store.DEFAULT_NUMBER);
    }

    public void createStoreDepartmentThroughPost(String number, String name, String storeName) throws JSONException, IOException {
        if (!StaticData.departments.containsKey(number)) {
            String storeId = StaticData.stores.get(storeName).getId();
            String getApiUrl = UrlHelper.getApiUrl() + "/api/1/departments";
            String jsonData = Department.getJsonObject(number, name, storeId).toString();
            String postResponse = executePostRequest(getApiUrl, jsonData);

            Department department = new Department(new JSONObject(postResponse));
            StaticData.departments.put(number, department);
        }
    }

    public void сreateProductThroughPost(String name, String sku, String barcode, String units, String purchasePrice) throws JSONException, IOException {
        if (!StaticData.hasSubCategory(SubCategory.DEFAULT_NAME)) {
            createSubCategoryThroughPost();
        }
        createProductThroughPost(name, sku, barcode, units, purchasePrice, SubCategory.DEFAULT_NAME);
    }

    public void createProductThroughPost(String name, String sku, String barcode, String units, String purchasePrice, String subCategoryName) throws JSONException, IOException {
        if (!StaticData.products.containsKey(sku)) {
            String subCategoryId = StaticData.subCategories.get(subCategoryName).getId();
            getSubCategoryMarkUp(subCategoryId);
            String getApiUrl = UrlHelper.getApiUrl() + "/api/1/products.json";
            String jsonData = Product.getJsonObject(name, units, "0", purchasePrice, barcode, sku, "Тестовая страна", "Тестовый производитель", "", subCategoryId, retailMarkupMax, retailMarkupMin).toString();
            String postResponse = executePostRequest(getApiUrl, jsonData);

            Product product = new Product(new JSONObject(postResponse));
            StaticData.products.put(sku, product);
        }
    }

    private void getSubCategoryMarkUp(String subCategoryId) throws IOException, JSONException {
        String apiUrl = String.format("%s/%s", UrlHelper.getApiUrl("/subcategories"), subCategoryId);
        String response = executeSimpleGetRequest(apiUrl, true);
        JSONObject jsonObject = new JSONObject(response);
        retailMarkupMax = (!jsonObject.isNull("retailMarkupMax"))
                ? jsonObject.getString("retailMarkupMax")
                : null;
        retailMarkupMin = (!jsonObject.isNull("retailMarkupMin"))
                ? jsonObject.getString("retailMarkupMin")
                : null;
    }

    public String getProductPageUrl(String productSku) throws JSONException {
        String productId = StaticData.products.get(productSku).getId();
        return String.format("%s/products/%s", UrlHelper.getWebFrontUrl(), productId);
    }

    public void createInvoiceThroughPost(String invoiceName) throws JSONException, IOException {
        createInvoiceThroughPostWithoutNavigation(invoiceName);
    }

    public void createInvoiceThroughPostWithoutNavigation(String invoiceName) throws JSONException, IOException {
        if (!StaticData.invoices.containsKey(invoiceName)) {
            String getApiUrl = String.format("%s/api/1/invoices.json", UrlHelper.getApiUrl());
            String jsonData = Invoice.getJsonObject(invoiceName, "supplier", DateTime.getTodayDate(DateTime.DATE_TIME_PATTERN), "accepter", "legalEntity", "", "").toString();
            String postResponse = executePostRequest(getApiUrl, jsonData);

            Invoice invoice = new Invoice(new JSONObject(postResponse));
            StaticData.invoices.put(invoiceName, invoice);
        }
    }

    public String getInvoicePageUrl(String invoiceName) throws JSONException {
        String invoiceId = StaticData.invoices.get(invoiceName).getId();
        return String.format("%s/invoices/%s?editMode=true", UrlHelper.getWebFrontUrl(), invoiceId);
    }

    public void createInvoiceThroughPost(String invoiceName, String productSku) throws IOException, JSONException {
        createInvoiceThroughPostWithoutNavigation(invoiceName);
        addProductToInvoice(invoiceName, productSku);
    }

    public void addProductToInvoice(String invoiceName, String productSku)
            throws JSONException, IOException {
        String productId = StaticData.products.get(productSku).getId();
        String invoiceId = StaticData.invoices.get(invoiceName).getId();
        if (!hasInvoiceProduct(invoiceId, productId)) {
            String apiUrl = String.format("%s/api/1/invoices/%s/products.json", UrlHelper.getApiUrl(), invoiceId);
            String productJsonData = InvoiceProduct.getJsonObject(productId, "1", "1").toString();
            executePostRequest(apiUrl, productJsonData);
        }
    }

    public Boolean hasInvoiceProduct(String invoiceId, String productId) throws IOException, JSONException {
        JSONArray jsonArray = getInvoiceProducts(invoiceId);
        for (int i = 0; i < jsonArray.length(); i++) {
            return jsonArray.getJSONObject(i).getJSONObject("product").getString("id").equals(productId);
        }
        return false;
    }

    public JSONArray getInvoiceProducts(String invoiceId) throws IOException, JSONException {
        String url = String.format("%s/api/1/invoices/%s/products", UrlHelper.getApiUrl(), invoiceId);
        String response = executeSimpleGetRequest(url, true);
        return new JSONArray(response);
    }

    public void averagePriceRecalculation() throws IOException, JSONException {
        String url = UrlHelper.getApiUrl() + "/api/1/service/recalculate-average-purchase-price";
        String response = executePostRequest(url, "");
        assertEquals(
                String.format("Average price recalculation failed!\nResponse: %s", response),
                response, "{\"ok\":true}"
        );
    }

    public void createWriteOffThroughPost(String writeOffNumber, String productSku, String quantity, String price, String cause)
            throws JSONException, IOException {

        createWriteOffThroughPost(writeOffNumber);
        addProductToWriteOff(writeOffNumber, productSku, quantity, price, cause);
    }

    public void createWriteOffThroughPost(String writeOffNumber) throws IOException, JSONException {
        if (!StaticData.writeOffs.containsKey(writeOffNumber)) {
            String getApiUrl = String.format("%s/api/1/writeoffs.json", UrlHelper.getApiUrl());
            String jsonData = WriteOff.getJsonObject(writeOffNumber, DateTime.getTodayDate(DateTime.DATE_PATTERN)).toString();
            String postResponse = executePostRequest(getApiUrl, jsonData);

            WriteOff writeOff = new WriteOff(new JSONObject(postResponse));
            StaticData.writeOffs.put(writeOffNumber, writeOff);
        }
    }

    public void addProductToWriteOff(String writeOffNumber, String productSku, String quantity, String price, String cause)
            throws JSONException, IOException {
        String productId = StaticData.products.get(productSku).getId();
        String writeOffId = StaticData.writeOffs.get(writeOffNumber).getId();
        String apiUrl = String.format("%s/api/1/writeoffs/%s/products.json", UrlHelper.getApiUrl(), writeOffId);

        String productJsonData = WriteOffProduct.getJsonObject(productId, quantity, price, cause).toString();
        executePostRequest(apiUrl, productJsonData);
    }

    public String getWriteOffPageUrl(String writeOffNumber) throws JSONException {
        String writeOffId = StaticData.writeOffs.get(writeOffNumber).getId();
        return String.format("%s/writeOffs/%s", UrlHelper.getWebFrontUrl(), writeOffId);
    }

    public void createGroupThroughPost(String groupName) throws IOException, JSONException {
        if (!StaticData.isGroupCreated(groupName)) {
            String getApiUrl = String.format("%s/api/1/groups.json", UrlHelper.getApiUrl());
            String jsonData = Group.getJsonObject(groupName).toString();
            String postResponse = executePostRequest(getApiUrl, jsonData);

            Group group = new Group(new JSONObject(postResponse));
            StaticData.groups.put(groupName, group);
        }
    }

    public void createCategoryThroughPost(String categoryName, String groupName) throws IOException, JSONException {
        if (!(StaticData.hasGroup(categoryName, groupName))) {
            createGroupThroughPost(groupName);
            String apiUrl = String.format("%s/api/1/categories.json", UrlHelper.getApiUrl());
            String groupId = StaticData.groups.get(groupName).getId();
            String groupJsonData = Category.getJsonObject(categoryName, groupId).toString();
            String postResponse = executePostRequest(apiUrl, groupJsonData);

            Category category = new Category(new JSONObject(postResponse));
            StaticData.categories.put(categoryName, category);
        }
    }

    public String getGroupPageUrl(String groupName) throws JSONException {
        String groupId = StaticData.groups.get(groupName).getId();
        return String.format("%s/catalog/%s", UrlHelper.getWebFrontUrl(), groupId);
    }

    public String getCategoryPageUrl(String categoryName, String groupName) throws JSONException {
        String groupPageUrl = getGroupPageUrl(groupName) + "/%s";
        String categoryId = StaticData.categories.get(categoryName).getId();
        return String.format(groupPageUrl, categoryId);
    }

    public void createSubCategoryThroughPost(String groupName, String categoryName, String subCategoryName) throws IOException, JSONException {
        if (!StaticData.hasCategory(categoryName, subCategoryName)) {
            createGroupThroughPost(groupName);
            createCategoryThroughPost(categoryName, groupName);
            String apiUrl = String.format("%s/api/1/subcategories.json", UrlHelper.getApiUrl());
            String categoryId = StaticData.categories.get(categoryName).getId();
            String subCategoryJsonData = SubCategory.getJsonObject(subCategoryName, categoryId).toString();
            String postResponse = executePostRequest(apiUrl, subCategoryJsonData);

            SubCategory subCategory = new SubCategory(new JSONObject(postResponse));
            StaticData.subCategories.put(subCategoryName, subCategory);
        }
    }

    public void createSubCategoryThroughPost() throws IOException, JSONException {
        createSubCategoryThroughPost(Group.DEFAULT_NAME, Category.DEFAULT_NAME, SubCategory.DEFAULT_NAME);
    }

    public String getSubCategoryProductListPageUrl(String subCategoryName, String categoryName, String groupName) throws JSONException {
        String categoryPageUrl = getCategoryPageUrl(categoryName, groupName);
        String subCategoryId = StaticData.subCategories.get(subCategoryName).getId();
        return categoryPageUrl + "/" + subCategoryId;
    }

    public String getSubCategoryProductCreatePageUrl(String subCategoryName) throws JSONException {
        String subCategoryId = StaticData.subCategories.get(subCategoryName).getId();
        return String.format("%s/products/create?subCategory=%s", UrlHelper.getWebFrontUrl(), subCategoryId);
    }

    public void createUserThroughPost(String name, String position, String login, String password, String role) throws JSONException, IOException {
        if (!StaticData.users.containsKey(login)) {
            String apiUrl = String.format("%s/api/1/users.json", UrlHelper.getApiUrl());
            String jsonData = User.getJsonObject(name, position, login, password, role).toString();
            String postResponse = executePostRequest(apiUrl, jsonData);

            User user = new User(new JSONObject(postResponse));
            StaticData.users.put(login, user);
        }
    }

    public Store createStoreThroughPost(Store store) throws JSONException, IOException {
        if (!StaticData.stores.containsKey(store.getNumber())) {
            String response = executePostRequest(store);
            Store newStore = new Store(new JSONObject(response));
            StaticData.stores.put(newStore.getNumber(), newStore);
            return newStore;
        } else {
            return StaticData.stores.get(store.getNumber());
        }
    }

    public Store createStoreThroughPost() throws JSONException, IOException {
        return createStoreThroughPost(new Store());
    }

    public Store createStoreThroughPost(String number, String address, String contacts) throws JSONException, IOException {
        Store store = new Store(number, address, contacts);
        return createStoreThroughPost(store);
    }

    public String getStoreId(String storeNumber) throws JSONException {
        return StaticData.stores.get(storeNumber).getId();
    }

    public String getUserPageUrl(String userName) throws JSONException {
        String userId = StaticData.users.get(userName).getId();
        return String.format("%s/users/%s", UrlHelper.getWebFrontUrl(), userId);
    }

    public void setSubCategoryMarkUp(String retailMarkupMax, String retailMarkupMin, String subCategoryName) throws IOException, JSONException {
        setSubCategoryMarkUp(retailMarkupMax, retailMarkupMin, StaticData.subCategories.get(subCategoryName));
    }

    public void setSubCategoryMarkUp(String retailMarkupMax, String retailMarkupMin, SubCategory subCategory) throws JSONException, IOException {
        String apiUrl = String.format("%s/%s", UrlHelper.getApiUrl("/subcategories"), subCategory.getId());
        executePutRequest(apiUrl, new JSONObject()
                .put("category", subCategory.getCategory().getId())
                .put("name", subCategory.getName())
                .put("retailMarkupMax", retailMarkupMax)
                .put("retailMarkupMin", retailMarkupMin)
        );
    }

    public void promoteStoreManager(Store store, String userName) throws IOException, JSONException {
        promoteStoreManager(store, StaticData.users.get(userName));
    }

    public void promoteStoreManager(Store store, User user) throws JSONException, IOException {
        if (!hasStoreManager(store, user)) {
            String apiUrl = String.format("%s/%s", UrlHelper.getApiUrl("/stores"), store.getId());
            String data = "_method=LINK";
            HttpPost httpPost = getHttpPost(apiUrl);
            httpPost.setHeader("Link", getLinkHeaderValue(user));
            StringEntity entity = new StringEntity(data, "UTF-8");
            entity.setContentType("application/x-www-form-urlencoded; charset=UTF-8");
            httpPost.setEntity(entity);
            executeHttpMethod(httpPost);
        }
    }

    private Boolean hasStoreManager(Store store, User user) throws JSONException, IOException {
        String apiUrl = String.format("%s/%s/managers", UrlHelper.getApiUrl("/stores"), store.getId());
        String responce = executeSimpleGetRequest(apiUrl, true);
        JSONArray jsonArray = new JSONArray(responce);
        for (int i = 0; i < jsonArray.length(); i++) {
            return jsonArray.getJSONObject(i).getString("id").equals(user.getId());
        }
        return false;
    }

    private void setHeaders(HttpEntityEnclosingRequestBase httpEntityEnclosingRequestBase) throws IOException, JSONException {
        httpEntityEnclosingRequestBase.setHeader("Accept", "application/json");
        httpEntityEnclosingRequestBase.setHeader("Authorization", "Bearer " + getAccessToken());
    }

    private HttpPost getHttpPost(String url) throws IOException, JSONException {
        HttpPost httpPost = new HttpPost(url);
        setHeaders(httpPost);
        return httpPost;
    }

    private HttpPut getHttpPut(String url) throws IOException, JSONException {
        HttpPut httpPut = new HttpPut(url);
        setHeaders(httpPut);
        return httpPut;
    }

    private StringEntity getStringEntity(String data) throws UnsupportedEncodingException {
        StringEntity entity = new StringEntity(data, "UTF-8");
        entity.setContentType("application/json;charset=UTF-8");
        entity.setContentEncoding(new BasicHeader(HTTP.CONTENT_TYPE, "application/json;charset=UTF-8"));
        return entity;
    }

    private String executeHttpMethod(HttpEntityEnclosingRequestBase httpEntityEnclosingRequestBase) throws IOException {
        DefaultHttpClient httpClient = new DefaultHttpClient();
        httpClient.getParams().setParameter("http.protocol.content-charset", "UTF-8");
        HttpResponse response = httpClient.execute(httpEntityEnclosingRequestBase);

        HttpEntity httpEntity = response.getEntity();
        if (httpEntity != null) {
            String responseMessage = EntityUtils.toString(httpEntity, "UTF-8");
            validateResponseMessage(response, responseMessage);
            return responseMessage;
        } else {
            return "";
        }
    }

    private String getLinkHeaderValue(User user) throws JSONException {
        return String.format("<%s/%s>; rel=\"managers\"", UrlHelper.getApiUrl("/users"), user.getId());
    }

    private String executePutRequest(String targetURL, String urlParameters) throws IOException, JSONException {
        HttpPut httpPut = getHttpPut(targetURL);
        StringEntity stringEntity = getStringEntity(urlParameters);
        httpPut.setEntity(stringEntity);
        return executeHttpMethod(httpPut);
    }

    private String executePutRequest(String targetUrl, JSONObject jsonObject) throws IOException, JSONException {
        return executePutRequest(targetUrl, jsonObject.toString());
    }

    private String executePostRequest(String targetURL, String urlParameters) throws IOException, JSONException {
        HttpPost httpPost = getHttpPost(targetURL);
        StringEntity stringEntity = getStringEntity(urlParameters);
        httpPost.setEntity(stringEntity);
        return executeHttpMethod(httpPost);
    }

    private String executePostRequest(String targetURL, JSONObject jsonObject) throws IOException, JSONException {
        return executePostRequest(targetURL, jsonObject.toString());
    }

    private String executePostRequest(AbstractObject object) throws IOException, JSONException {
        String targetUrl = UrlHelper.getApiUrl(object.getApiUrl());
        JSONObject jsonObject = object.getJsonObject();
        return executePostRequest(targetUrl, jsonObject);
    }

    private void validateResponseMessage(HttpResponse httpResponse, String responseMessage) {
        int statusCode = httpResponse.getStatusLine().getStatusCode();
        if (statusCode != 201 && statusCode != 200) {
            StringBuilder builder = new StringBuilder();
            JSONObject mainJsonObject = null;
            try {
                mainJsonObject = new JSONObject(responseMessage);

                if (!mainJsonObject.isNull("errors")) {
                    JSONArray jsonArray = mainJsonObject.getJSONArray("errors");
                    for (int i = 0; i < jsonArray.length(); i++) {
                        builder.append(jsonArray.get(i));
                    }
                } else if (!mainJsonObject.isNull("children")) {
                    JSONObject jsonObject = mainJsonObject.getJSONObject("children");
                    for (Iterator keys = jsonObject.keys(); keys.hasNext(); ) {
                        String key = (String) keys.next();
                        if (jsonObject.get(key) instanceof JSONObject) {
                            JSONArray jsonArray = jsonObject.getJSONObject(key).getJSONArray("errors");
                            for (int i = 0; i < jsonArray.length(); i++) {
                                String message = String.format("%s : '%s'", key, jsonArray.get(i));
                                builder.append(message);
                            }
                        }
                    }
                } else {
                    String message = String.format("message: '%s', statusCode: '%s', status: '%s', statusText: '%s', currentContent: '%s'.",
                            mainJsonObject.getString("message"),
                            mainJsonObject.getString("statusCode"),
                            mainJsonObject.getString("status"),
                            mainJsonObject.getString("statusText"),
                            mainJsonObject.getString("currentContent")
                    );
                    builder.append(message);
                }
            } catch (JSONException e) {
                fail(
                        String.format("Exception message: %s. Json: %s", e.getMessage(),
                                mainJsonObject != null
                                        ? mainJsonObject.toString()
                                        : null)
                );
            }
            fail(
                    String.format("Responce json error: '%s'", builder.toString())
            );
        }
    }

    private String executeSimpleGetRequest(String targetUrl, boolean forAccessToken) throws IOException, JSONException {

        //TODO Work around for token expiration 401 : The access token provided has expired.
        HttpGet request = new HttpGet(targetUrl);
        if (forAccessToken) {
            request.setHeader("Authorization", "Bearer " + getAccessToken());
        }
        DefaultHttpClient httpClient = new DefaultHttpClient();
        HttpResponse response = httpClient.execute(request);
        HttpEntity httpEntity = response.getEntity();
        String responseMessage = EntityUtils.toString(httpEntity, "UTF-8");
        validateResponseMessage(response, responseMessage);
        return responseMessage;
    }

    private String getAccessToken() throws JSONException, IOException {
        receiveAccessToken(userName, password);
        return StaticData.userTokens.get(userName).getAccessToken();
    }

    private void receiveAccessToken(String userName, String password) throws JSONException, IOException {
        if (!StaticData.userTokens.containsKey(userName)) {
            String url = String.format("%s/oauth/v2/token", UrlHelper.getApiUrl());
            String parameters = String.format("?grant_type=password&username=%s&password=%s&client_id=%s&client_secret=%s",
                    userName, password, StaticData.client_id, StaticData.client_secret);
            String response = executeSimpleGetRequest(url + parameters, false);
            OauthAuthorizeData oauthAuthorize = new OauthAuthorizeData(new JSONObject(response));
            StaticData.userTokens.put(userName, oauthAuthorize);
        }
    }
}
