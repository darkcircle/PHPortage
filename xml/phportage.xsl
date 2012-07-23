<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/result">
		<html>
			<head>
				<title>PHPortage :: The REST API of the fastest gentoo package search engine in the web</title>
			</head>
			<body>
				<div align="center">
					<table border="0">
						<tr><td rowspan="4"> <img src="image/php.gif" border="0"/></td><td> </td></tr>
						<tr><td valign="top"> <font size="7"><b><i>ortage</i></b></font></td></tr>
						<tr><td>  </td></tr>
						<tr><td>  </td></tr>
					</table>
				</div>
				<center><i>The REST API of the fastest gentoo package search engine in the web</i></center>
				<br/>
				<hr/>
				<br/>
				<h1>Results: <xsl:value-of select="actualnumofres"/></h1>
				<div align="center">
					<table border="1">
						<tr>
							<td>Category</td>
							<td>Name</td>
							<td>Version</td>
							<td>Description</td>
							<td>Website</td>
							<td>License</td>
							<td>Architecture</td>
							<td>Mask</td>
						</tr>
						<xsl:for-each select="/result/packages/pkg">
							<tr>
								<td><xsl:value-of select="category"/></td>
								<td><xsl:value-of select="name"/></td>
								<td><xsl:value-of select="version"/></td>
								<td><xsl:value-of select="description"/></td>
								<td><xsl:value-of select="homepage"/></td>
								<td><xsl:value-of select="license"/></td>
								<td>
									<xsl:for-each select="arch/archelement">
										<xsl:value-of select="@name"/>,
									</xsl:for-each>
								</td>
								<td align="center">	
									<xsl:for-each select="masked">
										<xsl:value-of select="@value"/>
									</xsl:for-each>
								</td>
							</tr>
						</xsl:for-each>
					</table>
				</div>
				<br/>
				<hr/>
				<table border="0" width="100%">
					<tr><td align="left"><i>Powered by PHP</i></td><td align="right">Copyright (c) 2012 by Darkcircle</td></tr>
				</table>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
