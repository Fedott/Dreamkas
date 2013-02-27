<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    
    <xsl:template
        match=" mayak_product "
        >
        <div mayak_card="true">
            <div mayak_card_title="true">Создание товара</div>
            <form mayak_product_editor="maker">
                <xsl:apply-templates select=" . " mode="mayak_product_fields" />
                <div mayak_block="true">
                    <button
                        mayak_button="success"
                        type="submit"
                        >
                        Создать товар
                    </button>
                </div>
            </form>
        </div>
    </xsl:template>
    
    <xsl:template
        match=" mayak_product[ @id ] "
        >
        <div mayak_card="true">
            <div mayak_card_title="true">Редактирование товара</div>
            <form mayak_product_editor="maker">
                <xsl:apply-templates select=" . " mode="mayak_product_fields" />
                <div mayak_block="true">
                    <button
                        mayak_button="success"
                        type="submit"
                        >
                        Сохранить данные
                    </button>
                    <button
                        mayak_button="reset"
                        type="reset"
                        >
                        Отменить изменения
                    </button>
                </div>
            </form>
        </div>
    </xsl:template>
    
    <xsl:template match="mayak_product" mode="mayak_product_fields" >
        <div mayak_block="true">
            <xsl:apply-templates select=" . " mode="mayak_product_name" />
            <xsl:apply-templates select=" . " mode="mayak_product_vendor" />
            <xsl:apply-templates select=" . " mode="mayak_product_vendorCountry" />
        </div>
        <div mayak_block="true">
            <xsl:apply-templates select=" . " mode="mayak_product_purchasePrice" />
            <xsl:apply-templates select=" . " mode="mayak_product_barcode" />
            <xsl:apply-templates select=" . " mode="mayak_product_unit" />
            <xsl:apply-templates select=" . " mode="mayak_product_vat" />
        </div>
        <div mayak_block="true">
            <xsl:apply-templates select=" . " mode="mayak_product_sku" />
            <xsl:apply-templates select=" . " mode="mayak_product_info" />
        </div>
    </xsl:template>
    
    <xsl:template match="mayak_product" mode="mayak_product_sku" >
        <input
            mayak_field="short"
            placeholder="Артикул"
            title="Артикул"
            type="text"
            name="sku"
            value="{@sku}"
        />
    </xsl:template>
    
    <xsl:template match="mayak_product" mode="mayak_product_name" >
        <input
            mayak_field="normal"
            placeholder="Наименование"
            title="Наименование"
            required="required"
            type="text"
            name="name"
            value="{@name}"
        />
    </xsl:template>
    
    <xsl:template match="mayak_product" mode="mayak_product_unit" >
        <select
            mayak_field="short"
            required="required"
            title="Мерность"
            name="unit"
            >
            <option value="">Мерность</option>
            <option value="unit">Штуки</option>
            <option value="liter">Литры</option>
            <option value="kg">Килограммы</option>
        </select>
    </xsl:template>
    
    <xsl:template match="mayak_product" mode="mayak_product_vat" >
        <select
            mayak_field="short"
            required="required"
            title="НДС"
            name="vat"
            >
            <option value="">НДС</option>
            <option value="1">1%</option>
            <option value="5">5%</option>
            <option value="10">10%</option>
        </select>
    </xsl:template>
    
    <xsl:template match="mayak_product" mode="mayak_product_purchasePrice" >
        <input
            mayak_field="short"
            type="number"
            step="any"
            required="required"
            placeholder="Цена закупки"
            title="Цена закупки"
            name="purchasePrice"
            value="{ @purchasePrice }"
        />
    </xsl:template>
    
    <xsl:template match="mayak_product" mode="mayak_product_barcode" >
        <input
            mayak_field="short"
            type="number"
            required="required"
            placeholder="Штрих код"
            title="Штрих код"
            name="barcode"
            value="{ @barcode }"
        />
    </xsl:template>
    
    <xsl:template match="mayak_product" mode="mayak_product_vendor" >
        <input
            mayak_field="normal"
            placeholder="Производитель"
            title="Производитель"
            required="required"
            type="text"
            name="vendor"
            value="{ @vendor }"
        />
    </xsl:template>

    <xsl:template match="mayak_product" mode="mayak_product_vendorCountry" >
        <input
            mayak_field="normal"
            placeholder="Страна"
            title="Страна"
            required="required"
            type="text"
            name="vendorCountry"
            value="{ @vendorCountry }"
        />
    </xsl:template>
    
    <xsl:template match="mayak_product" mode="mayak_product_info" >
        <input
            mayak_field="long"
            placeholder="Дополнительная информация"
            title="Дополнительная информация"
            type="text"
            name="info"
            value="{ @info }"
        />
    </xsl:template>
    
</xsl:stylesheet>