<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                version="1.0">

 <xsl:output method="html"/>
 
 <xsl:template match="directory">
  <dl>
	<xsl:apply-templates/>
  </dl>
 </xsl:template>

 <xsl:template match="app">
  <dt>
   <xsl:value-of select="name"/>
  </dt>
  <dd>
   <xsl:value-of select="description"/>
   <br />
   <b>URL</b>: 
   <a>
	<xsl:attribute name="href">
	 <xsl:value-of select="url"/>
	</xsl:attribute>
	<xsl:value-of select="url"/>
   </a>
   <br />
   <b>Maintainer:</b>
   <xsl:text> </xsl:text>
   <xsl:value-of select="maintainer"/>
  </dd>
 </xsl:template>
</xsl:stylesheet>
