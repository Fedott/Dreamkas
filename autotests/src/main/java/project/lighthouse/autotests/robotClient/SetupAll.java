
package project.lighthouse.autotests.robotClient;

import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlType;


/**
 * <p>Java class for setupAll complex type.
 * <p/>
 * <p>The following schema fragment specifies the expected content contained within this class.
 * <p/>
 * <pre>
 * &lt;complexType name="setupAll">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element name="cashList" type="{http://www.w3.org/2001/XMLSchema}string" minOccurs="0"/>
 *       &lt;/sequence>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "setupAll", propOrder = {
        "cashList"
})
public class SetupAll {

    protected String cashList;

    /**
     * Gets the value of the cashList property.
     *
     * @return possible object is
     *         {@link String }
     */
    public String getCashList() {
        return cashList;
    }

    /**
     * Sets the value of the cashList property.
     *
     * @param value allowed object is
     *              {@link String }
     */
    public void setCashList(String value) {
        this.cashList = value;
    }

}
