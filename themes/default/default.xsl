<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns="http://www.w3.org/1999/xhtml" 
        xmlns:i18n="http://apache.org/cocoon/i18n/2.1"
        exclude-result-prefixes="xhtml i18n"
        version="1.0">
        
        <xsl:variable name="url" select="/command/queryinfo/query/url"/>
    
    <xsl:template match="/">
        <html lang="{$lang}" xml:lang="{$lang}">
	        <head>
	            <title><i18n:text>PageTitle</i18n:text></title>
	            
	            <link rel="stylesheet" type="text/css" href="{$webrootStatic}yui/build/fonts/fonts-min.css" /> 
	            
	        </head>
	        <body>
	            <h1><i18n:text>WelcomeText</i18n:text></h1>
	            <form method="get" action="/api/txt/">
	                
	                url*: <textarea cols="50" rows="5" name="url"><xsl:value-of select="$url"/></textarea><br/>
	                code:<input name="code"/><br/>
	                <input type="submit"/>
	            </form>
	            
	            <p>
	            <a href="javascript:window.location='http://liip.to/?url='+encodeURIComponent(window.location);">Liip.to this</a> Bookmarklet
	            </p>
	        </body>
        </html>
        
    </xsl:template>
</xsl:stylesheet>