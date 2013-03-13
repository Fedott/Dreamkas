<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    
    <xsl:template
        match=" *[ @lh_product_create ] "
        >
        <div lh_card_stack="true">
            <a
                lh_card_back="true"
                href="?product/list"
                >
                Список товаров
            </a>
            <div lh_card="true">
                <div lh_card_header="true">
                    <div lh_card_headerButtons="true">
                        <a
                            lh_link="close"
                            href="?product/list"
                            >
                            <xsl:apply-templates select="." mode="lh_link_close" />
                            закрыть
                        </a>
                    </div>
                    <div lh_card_title="true">
                        Добавление нового товара
                    </div>
                </div>
                
                <form lh_product_edit="maker">
                    <xsl:apply-templates select=" . " mode="lh_product_fields" />
                    
                    <div lh_prop_set="true">
                        <span lh_prop_name="true"></span>
                        <button
                            lh_button="success"
                            type="submit"
                            disabled="disabled"
                            >
                            Добавить товар
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </xsl:template>
    
</xsl:stylesheet>