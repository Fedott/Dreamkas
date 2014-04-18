package project.lighthouse.autotests.steps.api.departmentManager;

import net.thucydides.core.annotations.Step;
import org.json.JSONException;
import org.junit.Assert;
import project.lighthouse.autotests.StaticData;
import project.lighthouse.autotests.api.factories.InvoicesFactory;
import project.lighthouse.autotests.helper.UrlHelper;
import project.lighthouse.autotests.objects.api.User;
import project.lighthouse.autotests.objects.api.invoice.Invoice;
import project.lighthouse.autotests.objects.api.invoice.InvoiceProduct;

import java.io.IOException;

public class InvoiceApiSteps extends DepartmentManagerApi {

    private Invoice invoice;

    @Step
    @Deprecated
    public Invoice createInvoiceThroughPost(String invoiceName, String date, String supplier, String accepter, String legalEntity, String supplierInvoiceSku, String supplierInvoiceDate, String storeName, String userName) throws JSONException, IOException {
        Assert.fail("this method is deprecated and must be deleted");
        return null;
    }

    @Step
    @Deprecated
    public void addProductToInvoice(String invoiceName, String productSku, String quantity, String price, String userName) throws JSONException, IOException {
        Assert.fail("this method is deprecated and must be deleted");
    }

    @Step
    @Deprecated
    public void navigateToTheInvoicePage(String invoiceName) throws JSONException {
        Assert.fail("this method is deprecated and must be deleted");
    }

    @Step
    public Invoice createInvoice(String supplierId,
                                 String acceptanceDate,
                                 String accepter,
                                 String legalEntity,
                                 String supplierInvoiceNumber,
                                 String userName,
                                 InvoiceProduct[] invoiceProducts) throws IOException, JSONException {
        User user = StaticData.users.get(userName);
        Invoice invoice = new InvoicesFactory(userName, "lighthouse")
                .create(supplierId, acceptanceDate, accepter, legalEntity, supplierInvoiceNumber, invoiceProducts, user.getStore().getId());
        this.invoice = invoice;
        return invoice;
    }

    @Step
    public void openInvoicePage() throws JSONException {
        String url = String.format("%s/stores/%s/invoices/%s",
                UrlHelper.getWebFrontUrl(),
                invoice.getStoreId(),
                invoice.getId());
        getDriver().navigate().to(url);
    }
}
