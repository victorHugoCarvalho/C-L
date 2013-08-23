<?xml version="1.0" encoding="ISO-8859-1" ?> 
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format"> 

<xsl:template match="/"> 
 <html> 
  <head> 
   <title><xsl:value-of select="//Projeto" /></title> 
   <style><![CDATA[ 
BODY 
{ 
    MARGIN: 10px 0px; 
    COLOR: black; 
    FONT-FAMILY: Verdana; 
    BACKGROUND-COLOR: white 
} 
A 
{ 
    FONT-WEIGHT: bolder; 
    COLOR: blue; 
    FONT-FAMILY: Verdana; 
    TEXT-DECORATION: none 
} 
A:hover 
{ 
    FONT-WEIGHT: bolder; 
    COLOR: Tomato; 
    FONT-FAMILY: Verdana 
} 
H3 
{ 
    BORDER-TOP: #3366ff 1px solid; 
    PADDING-LEFT: 4px; 
    MARGIN-BOTTOM: 0.5em; 
    MARGIN-LEFT: 1em; 
    BORDER-LEFT: #3366ff 8px solid 
} 
TH 
{ 
    FONT-WEIGHT: bolder; 
    COLOR: WHITE; 
    BACKGROUND-COLOR: #3366ff; 
} 
TD 
{ 
    COLOR: black; 
    BACKGROUND-COLOR: #E0FFFF; 
} 
TABLE 
{ 
MARGIN-LEFT: 1.2em; 
MARGIN-RIGHT: 1.2em; 
} 
P 
{ 
    PADDING-LEFT: 4px; 
    MARGIN-BOTTOM: 0.5em; 
    MARGIN-LEFT: 1.5em 
} 
]]> 
   </style> 
  </head> 
  <body> 
   <h2>Cenários</h2><br/> 
   <xsl:apply-templates select="//cenario"> 
    <xsl:sort select="titulo" order="ascending" /> 
   </xsl:apply-templates><br/> 
   <h2>Léxicos</h2><br/> 
   <xsl:apply-templates select="//lexico"> 
    <xsl:sort select="nome_simbolo" order="ascending" /> 
   </xsl:apply-templates> 
  </body> 
 </html> 
</xsl:template> 

<xsl:template match="cenario"> 
 <A> 
  <xsl:attribute name="ID"> 
   <xsl:value-of select="ID" /> 
  </xsl:attribute> 
 </A> 
 <h3 xml:space="preserve"><xsl:element name="a"><xsl:attribute name="name">cenario_<xsl:value-of select="titulo/@id"/></xsl:attribute><xsl:element name="Font"><xsl:attribute name="color">black</xsl:attribute><xsl:value-of select="titulo"/></xsl:element></xsl:element>
 </h3> 
 <table border="0" cellspacing="2"> 
  <tr> 
   <th width="115">Objetivo</th> 
   <td> 
    <xsl:for-each select="objetivo/sentenca/texto"> 
     <xsl:choose> 
      <xsl:when test="@referencia_lexico"> 
       <xsl:element name="a"><xsl:attribute name="title">Léxico</xsl:attribute><xsl:attribute name="HREF">#lexico_<xsl:value-of select="@referencia_lexico" /></xsl:attribute><xsl:value-of select="." /></xsl:element> 
	  </xsl:when> 
      <xsl:otherwise> 
       <xsl:value-of select="." /> 
      </xsl:otherwise> 
     </xsl:choose> 
    </xsl:for-each> 
   </td> 
  </tr> 
  <tr valign="top"> 
   <th>Contexto</th> 
   <td> 
    <xsl:for-each select="contexto/sentenca/texto"> 
     <xsl:choose> 
      <xsl:when test="@referencia_lexico"> 
       <xsl:element name="a"><xsl:attribute name="title">Léxico</xsl:attribute><xsl:attribute name="HREF">#lexico_<xsl:value-of select="@referencia_lexico" /></xsl:attribute><xsl:value-of select="." /></xsl:element> 
      </xsl:when>
	  <xsl:when test="@referencia_cenario"> 
       <xsl:element name="a"><xsl:attribute name="title">Cenário</xsl:attribute><xsl:attribute name="HREF">#cenario_<xsl:value-of select="@referencia_cenario" /></xsl:attribute><xsl:element name="span"><xsl:attribute name="style">font-variant: small-caps</xsl:attribute><xsl:value-of select="." /></xsl:element></xsl:element> 
      </xsl:when> 
      <xsl:otherwise> 
       <xsl:value-of select="." /> 
      </xsl:otherwise> 
     </xsl:choose> 
    </xsl:for-each> 
   </td> 
  </tr> 
  <tr valign="top"> 
   <th>Atores</th> 
   <td> 
    <xsl:for-each select="atores/sentenca/texto"> 
     <xsl:choose> 
      <xsl:when test="@referencia_lexico"> 
       <xsl:element name="a"><xsl:attribute name="title">Léxico</xsl:attribute><xsl:attribute name="HREF">#lexico_<xsl:value-of select="@referencia_lexico" /></xsl:attribute><xsl:value-of select="." /></xsl:element> 
      </xsl:when> 
      <xsl:otherwise> 
       <xsl:value-of select="." /> 
      </xsl:otherwise> 
     </xsl:choose> 
    </xsl:for-each> 
   </td> 
  </tr> 
  <tr valign="top"> 
   <th>Recursos</th> 
   <td> 
    <xsl:for-each select="recursos/sentenca/texto"> 
     <xsl:choose> 
      <xsl:when test="@referencia_lexico"> 
       <xsl:element name="a"><xsl:attribute name="title">Léxico</xsl:attribute><xsl:attribute name="HREF">#lexico_<xsl:value-of select="@referencia_lexico" /></xsl:attribute><xsl:value-of select="." /></xsl:element> 
      </xsl:when> 
      <xsl:otherwise> 
       <xsl:value-of select="." /> 
      </xsl:otherwise> 
     </xsl:choose> 
    </xsl:for-each> 
   </td> 
  </tr> 
  <tr valign="top"> 
   <th>Episódios</th> 
   <td> 
    <xsl:for-each select="episodios/sentenca/texto"> 
     <xsl:choose> 
      <xsl:when test="@referencia_lexico"> 
       <xsl:element name="a"><xsl:attribute name="title">Léxico</xsl:attribute><xsl:attribute name="HREF">#lexico_<xsl:value-of select="@referencia_lexico" /></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes" /></xsl:element> 
      </xsl:when> 
	  <xsl:when test="@referencia_cenario"> 
       <xsl:element name="a"><xsl:attribute name="title">Cenário</xsl:attribute><xsl:attribute name="HREF">#cenario_<xsl:value-of select="@referencia_cenario" /></xsl:attribute><xsl:element name="span"><xsl:attribute name="style">font-variant: small-caps</xsl:attribute><xsl:value-of select="." /></xsl:element></xsl:element> 
      </xsl:when> 
      <xsl:otherwise> 
       <xsl:value-of select="." disable-output-escaping="yes"/> 
      </xsl:otherwise> 
     </xsl:choose> 
    </xsl:for-each> 
   </td> 
  </tr> 
  <tr valign="top"> 
   <th>Excecao</th> 
   <td> 
    <xsl:for-each select="excecao/sentenca/texto"> 
     <xsl:choose> 
      <xsl:when test="@referencia_lexico"> 
       <xsl:element name="a"><xsl:attribute name="title">Léxico</xsl:attribute><xsl:attribute name="HREF">#lexico_<xsl:value-of select="@referencia_lexico" /></xsl:attribute><xsl:value-of select="." /></xsl:element> 
      </xsl:when> 
      <xsl:otherwise> 
       <xsl:value-of select="." /> 
      </xsl:otherwise> 
     </xsl:choose> 
    </xsl:for-each> 
   </td> 
  </tr> 
 </table> 
</xsl:template> 

<xsl:template match="lexico"> 
 <A> 
  <xsl:attribute name="ID"> 
   <xsl:value-of select="ID" /> 
  </xsl:attribute> 
 </A> 
 <h3 xml:space="preserve"><xsl:element name="a"><xsl:attribute name="name">lexico_<xsl:value-of select="nome_simbolo/@id"/></xsl:attribute><xsl:element name="Font"><xsl:attribute name="color">black</xsl:attribute><xsl:value-of select="nome_simbolo"/></xsl:element></xsl:element>
 </h3> 
 <table border="0" cellspacing="2"> 
  <tr> 
   <th width="115">Sinônimo(s)</th> 
   <td> 
    <xsl:for-each select="sinonimos">
	 <xsl:choose> 
      <xsl:when test="sinonimo">
       <xsl:for-each select="sinonimo">
	    <xsl:value-of select="." />
		<xsl:if test="not(position()=last())">, </xsl:if>
	   </xsl:for-each>.
	  </xsl:when>
	  <xsl:otherwise>
	  </xsl:otherwise>
	 </xsl:choose> 
	</xsl:for-each> 
   </td> 
  </tr>
  <tr> 
   <th width="115">Noção</th> 
   <td> 
    <xsl:for-each select="nocao/sentenca/texto"> 
     <xsl:choose> 
      <xsl:when test="@referencia_lexico"> 
       <xsl:element name="a"><xsl:attribute name="title">Léxico</xsl:attribute><xsl:attribute name="HREF">#lexico_<xsl:value-of select="@referencia_lexico" /></xsl:attribute><xsl:value-of select="." /></xsl:element>&#32;
      </xsl:when> 
      <xsl:otherwise> 
       <xsl:value-of select="." /> 
      </xsl:otherwise> 
     </xsl:choose> 
    </xsl:for-each> 
   </td> 
  </tr> 
  <tr valign="top"> 
   <th>Impacto</th> 
   <td> 
    <xsl:for-each select="impacto/sentenca/texto"> 
     <xsl:choose> 
      <xsl:when test="@referencia_lexico"> 
       <xsl:element name="a"><xsl:attribute name="title">Léxico</xsl:attribute><xsl:attribute name="HREF">#lexico_<xsl:value-of select="@referencia_lexico" /></xsl:attribute><xsl:value-of select="." /></xsl:element> 
      </xsl:when> 
      <xsl:otherwise> 
       <xsl:value-of select="." /> 
      </xsl:otherwise> 
     </xsl:choose> 
    </xsl:for-each> 
   </td> 
  </tr> 
 </table> 
</xsl:template> 

</xsl:stylesheet>