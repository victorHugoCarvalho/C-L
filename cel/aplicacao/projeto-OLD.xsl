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
    COLOR: Blue;
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
		<h3 xml:space="preserve">
			<xsl:value-of select="titulo" />
		</h3>
		<table border="0" cellspacing="2">
			<tr>
				<th width="100">Objetivo</th>
				<td>
					<xsl:value-of select="objetivo" />
				</td>
			</tr>
			<tr valign="top">
				<th>Contexto</th>
				<td>
					<xsl:value-of select="contexto" />
				</td>
			</tr>
			<tr valign="top">
				<th>Atores</th>
				<td>
					<xsl:value-of select="atores" />
				</td>
			</tr>
			<tr valign="top">
				<th>Recursos</th>
				<td>
					<xsl:value-of select="recursos" />
				</td>
			</tr>
			<tr valign="top">
				<th>Episódios</th>
                                <td>
					<xsl:value-of select="episodios" disable-output-escaping="yes" />
				</td>
			</tr>
			<tr valign="top">
				<th>Excecao</th>
				<td>
					<xsl:value-of select="excecao" />
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
		<h3 xml:space="preserve">
			<xsl:value-of select="nome_simbolo" />
		</h3>
		<table border="0" cellspacing="2">
			<tr>
				<th width="100">Noção</th>
				<td>
					<xsl:value-of select="nocao" />
				</td>
			</tr>
			<tr valign="top">
				<th>Impacto</th>
				<td>
					<xsl:value-of select="impacto" />
				</td>
			</tr>
		</table>
	</xsl:template>
</xsl:stylesheet>
