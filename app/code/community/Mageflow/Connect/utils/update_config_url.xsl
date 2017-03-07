<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" indent="yes" encoding="utf-8"/>
    <xsl:param name="package_version" select="$urlparam"/>
    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>
    <xsl:template match="default/mageflow_connect/advanced/connect_url">
        <xsl:element name="connect_url">
            <xsl:value-of select="$urlparam"/><xsl:text>secure/connect</xsl:text>
        </xsl:element>
    </xsl:template>
    <xsl:template match="default/mageflow_connect/advanced/signup_url">
        <xsl:element name="signup_url">
            <xsl:value-of select="$urlparam"/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="default/mageflow_connect/advanced/ground_rules">
        <xsl:element name="ground_rules">
            <xsl:value-of select="$urlparam"/><xsl:text>groundrules</xsl:text>
        </xsl:element>
    </xsl:template>

    <xsl:template match="default/mageflow_connect/advanced/api_url">
        <xsl:element name="api_url">
            <xsl:value-of select="$urlparam"/><xsl:text>api/1.0/</xsl:text>
        </xsl:element>
    </xsl:template>

    <xsl:template match="@*|node()">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()"/>
        </xsl:copy>
    </xsl:template>
</xsl:stylesheet>