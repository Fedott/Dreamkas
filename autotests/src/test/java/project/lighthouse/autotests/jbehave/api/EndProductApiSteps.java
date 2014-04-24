package project.lighthouse.autotests.jbehave.api;

import net.thucydides.core.annotations.Steps;
import org.jbehave.core.annotations.Alias;
import org.jbehave.core.annotations.Given;
import org.jbehave.core.model.ExamplesTable;
import org.json.JSONException;
import org.junit.Assert;
import project.lighthouse.autotests.StaticData;
import project.lighthouse.autotests.helper.UUIDGenerator;
import project.lighthouse.autotests.objects.api.Category;
import project.lighthouse.autotests.objects.api.Group;
import project.lighthouse.autotests.objects.api.Product;
import project.lighthouse.autotests.objects.api.SubCategory;
import project.lighthouse.autotests.steps.api.commercialManager.CatalogApiSteps;
import project.lighthouse.autotests.steps.api.commercialManager.ProductApiSteps;

import java.io.IOException;
import java.util.Map;

public class EndProductApiSteps {

    @Steps
    ProductApiSteps productApiSteps;

    @Steps
    CatalogApiSteps catalogApiSteps;

    @Given("there is the product in subCategory with name '$subCategoryName' with data $exampleTable")
    public void givenThereIsTheProductWithData(String subCategoryName, ExamplesTable examplesTable) throws IOException, JSONException {
        String name = "",
                units = "",
                vat = "",
                purchasePrice = "",
                barcode = "",
                vendorCountry = "",
                vendor = "",
                info = "",
                retailMarkupMax = "",
                retailMarkupMin = "",
                rounding = "";
        for (Map<String, String> row : examplesTable.getRows()) {
            String elementName = row.get("elementName");
            String elementValue = row.get("elementValue");
            switch (elementName) {
                case "name":
                    name = elementValue;
                    break;
                case "units":
                    units = elementValue;
                    break;
                case "vat":
                    vat = elementValue;
                    break;
                case "purchasePrice":
                    purchasePrice = elementValue;
                    break;
                case "barcode":
                    barcode = elementValue;
                    break;
                case "vendorCountry":
                    vendorCountry = elementValue;
                    break;
                case "vendor":
                    vendor = elementValue;
                    break;
                case "info":
                    info = elementValue;
                    break;
                case "retailMarkupMax":
                    retailMarkupMax = elementValue;
                    break;
                case "retailMarkupMin":
                    retailMarkupMin = elementValue;
                    break;
                case "rounding":
                    rounding = elementValue;
                    break;
                default:
                    Assert.fail(String.format("No such elementName '%s'", elementName));
                    break;
            }
        }
        productApiSteps.createProduct(name, units, vat, purchasePrice, barcode, vendorCountry, vendor, info, subCategoryName, retailMarkupMax, retailMarkupMin, rounding);
    }

    public Product сreateProductThroughPost(String name, String barcode, String units, String purchasePrice) throws JSONException, IOException {
        if (!StaticData.hasSubCategory(SubCategory.DEFAULT_NAME)) {
            catalogApiSteps.createDefaultSubCategoryThroughPost();
        }
        return productApiSteps.createProductThroughPost(name, barcode, units, purchasePrice, SubCategory.DEFAULT_NAME, null);
    }

    @Given("there is the product with '$name' name, '$barcode' barcode")
    public void givenTheUserCreatesProductWithParams(String name, String barcode) throws JSONException, IOException {
        сreateProductThroughPost(name, barcode, "kg", "123");
    }

    @Given("there is created product with name '$name'")
    public void givenThereIsCreatedProductWithNameValue(String name) throws JSONException, IOException {
        givenTheUserCreatesProductWithParams(name, name, "kg");
    }

    @Given("there is the product with '$name' name, '$barcode' barcode, '$units' units")
    public void givenTheUserCreatesProductWithParams(String name, String barcode, String units) throws JSONException, IOException {
        сreateProductThroughPost(name, barcode, units, "123");
    }

    @Given("there is the product with '$name' name, '$barcode' barcode, '$units' units, '$purchasePrice' purchasePrice")
    public void givenTheUserCreatesProductWithParamsPrice(String name, String barcode, String units, String purchasePrice) throws JSONException, IOException {
        сreateProductThroughPost(name, barcode, units, purchasePrice);
    }

    @Given("there is the product with '$name' name, '$sku' sku, '$barcode' barcode, '$units' units, '$purchasePrice' purchasePrice in the subcategory named '$subCategoryName'")
    public void createProductThroughPost(String name, String barcode, String units, String purchasePrice, String subCategoryName) throws JSONException, IOException {
        catalogApiSteps.createSubCategoryThroughPost(Group.DEFAULT_NAME, Category.DEFAULT_NAME, subCategoryName);
        productApiSteps.createProductThroughPost(name, barcode, units, purchasePrice, subCategoryName, null);
    }

    @Given("there is the product with '$name' name, '$barcode' barcode, '$units' units, '$purchasePrice' purchasePrice of group named '$groupName', category named '$categoryName', subcategory named '$subCategoryName'")
    public void createProductThroughPost(String name, String barcode, String units, String purchasePrice,
                                         String groupName, String categoryName, String subCategoryName) throws IOException, JSONException {
        catalogApiSteps.createSubCategoryThroughPost(groupName, categoryName, subCategoryName);
        productApiSteps.createProductThroughPost(name, barcode, units, purchasePrice, subCategoryName, null);
    }

    @Given("there is the product with '$productName' name, '$productSku' sku, '$barcode' barcode, '$units' units, '$purchasePrice' purchasePrice of group named '$groupName', category named '$categoryName', subcategory named '$subCategoryName' with '$rounding' rounding")
    @Alias("there is the product with <productName>, <productSku>, '$barcode' barcode, '$units' units, '$purchasePrice' purchasePrice of group named '$groupName', category named '$categoryName', subcategory named '$subCategoryName' with '$rounding' rounding")
    public void createProductThroughPost(String productName, String barcode, String units, String purchasePrice,
                                         String rounding, String groupName, String categoryName, String subCategoryName) throws IOException, JSONException {
        catalogApiSteps.createSubCategoryThroughPost(groupName, categoryName, subCategoryName);
        productApiSteps.createProductThroughPost(productName, barcode, units, purchasePrice, groupName, categoryName, subCategoryName, rounding);
    }

    @Given("there is the product with '$name' name, '$sku' sku, '$barcode' barcode, '$units' units, '$purchasePrice' purchasePrice of group named '$groupName', category named '$categoryName', subcategory named '$subCategoryName' with '$rounding' rounding, retailMarkUpMax '$retailMarkupMax' and retailMarkUpmin '$retailMarkupMin'")
    public void createProductThroughPost(String name, String barcode, String units, String purchasePrice,
                                         String rounding, String groupName, String categoryName, String subCategoryName, String retailMarkupMax, String retailMarkupMin) throws IOException, JSONException {
        catalogApiSteps.createSubCategoryThroughPost(groupName, categoryName, subCategoryName);
        productApiSteps.createProductThroughPost(name, barcode, units, purchasePrice, subCategoryName, retailMarkupMax, retailMarkupMin, rounding);
    }

    @Given("there is the product with '$name' name, '$sku' sku, '$barcode' barcode, '$units' units, '$purchasePrice' purchasePrice, '$rounding' rounding in the subcategory named '$subCategoryName'")
    public void createProductThroughPost(String name, String barcode, String units, String purchasePrice,
                                         String rounding, String subCategoryName) throws IOException, JSONException {
        catalogApiSteps.createSubCategoryThroughPost(Group.DEFAULT_NAME, Category.DEFAULT_NAME, subCategoryName);
        productApiSteps.createProductThroughPost(name, barcode, units, purchasePrice, subCategoryName, rounding);
    }

    //check
    @Given("there is the product with <productSku> and <rounding> in the subcategory named '$subCategoryName'")
    public void createProductThroughPost(String rounding, String productSku, String subCategoryName) throws IOException, JSONException {
        createProductThroughPost(productSku, productSku, productSku, "kg", "1", rounding, subCategoryName);
    }

    //check
    //проверить, что переход по параметру нэйм
    @Given("the user navigates to the product with sku '$productSku'")
    @Alias("the user navigates to the product with <productSku>")
    public void givenTheUserNavigatesToTheProduct(String productSku) throws JSONException {
        productApiSteps.navigateToTheProductPage(productSku);
    }

    //check
    //проверить экзамплес тейбл
    @Given("the user navigates to the product with <sku>")
    public void givenTheUserNavigatesToTheProdcutWithSku(String sku) throws JSONException, IOException {
        givenTheUserCreatesProductWithParamsPrice(sku, sku, "kg", "0,01");
        givenTheUserNavigatesToTheProduct(sku);
    }

    @Given("the user navigates to the product with random name")
    public void givenTheUserNavigatesToTheProductWithRandomName() throws IOException, JSONException {
        String uuid = new UUIDGenerator().generate();
        givenTheUserCreatesProductWithParamsPrice(uuid, uuid, "kg", "0,01");
        givenTheUserNavigatesToTheProduct(uuid);
    }
}
