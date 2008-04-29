<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns="http://www.w3.org/1999/xhtml" 
        xmlns:i18n="http://apache.org/cocoon/i18n/2.1"
        exclude-result-prefixes="xhtml i18n"
        version="1.0">
    
    <xsl:template match="/">
        <html lang="{$lang}" xml:lang="{$lang}">
	        <head>
	            <title><i18n:text>PageTitle</i18n:text></title>
	        </head>
	        <body>
	            <h1><i18n:text>WelcomeText</i18n:text></h1>
	            
	            <form method="get" action="/api/txt/">
	                
	                url: <input name="url"/>
	            </form>
	            
	            <p>
	            <a href="javascript:window.location='http://liip.to/api/txt/?url='+encodeURIComponent(window.location);">Liip.to this</a> Bookmarklet
	            </p>
	        </body>
        </html>
        
    </xsl:template>
</xsl:stylesheet>