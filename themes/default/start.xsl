<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:i18n="http://apache.org/cocoon/i18n/2.1"
	exclude-result-prefixes="xhtml i18n"
	version="1.0">
	
	<xsl:import href="master.xsl"/>
	
	<xsl:template name="page_body_content">
		<form method="get" action="/api/txt/">
			
			<div class="required">
				<label for="url">url*:</label>
				<textarea id="url" class="inputTextarea" cols="50" rows="5" name="url"><xsl:value-of select="$url"/>
					<xsl:text> </xsl:text>
				</textarea>
				<!--<small>Must be 250 characters or less.</small>-->
			</div>
			
			
			<div class="optional">
				<label for="code">Custom alias (optional):
				</label>
				<div  id="codeOk">
					<img id="codeOkSpinner" style="visibility: hidden" src="{$webroot_yui}assets/skins/sam/wait.gif"/>
				</div>                                      
				<input id="code" name="code" class="inputText" type="text" value="" maxlength="20" size="10"/>
				
				<!-- <small>We will never sell or disclose your email address to anyone. Once your account is setup, you may add additional email addresses.</small>
				-->
			</div>
			
			<div>
				<label><xsl:text> </xsl:text></label>
				<input type="submit" value="Send"/>
			</div>
		</form>
	</xsl:template>
		
	
</xsl:stylesheet>
