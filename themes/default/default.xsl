<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:i18n="http://apache.org/cocoon/i18n/2.1"
	exclude-result-prefixes="xhtml i18n"
	version="1.0">
	
	<xsl:variable name="url" select="/command/queryinfo/query/url"/>
	<xsl:param name="webroot_yui" select="'f'"/>
	<xsl:template match="/">
		<html lang="{$lang}" xml:lang="{$lang}">
			<head>
				<title>
					<i18n:text>PageTitle</i18n:text>
				</title>
				<link rel="stylesheet" type="text/css" href="{$webroot_yui}build/reset-fonts-grids/reset-fonts-grids.css" />
				<link rel="stylesheet" type="text/css" href="{$webroot_yui}build/base/base.css" />
				
				<link rel="stylesheet" type="text/css" href="{$webrootStatic}css/default.css" media="screen" />
				
			</head>
			<body>
				
				<div id="doc4" class="yui-t4">
					<div id="hd">
						
						<h1>
							<i18n:text>WelcomeText</i18n:text>
						</h1>
						
					</div>
					
					<div id="bd">
						
						<div id="yui-main">
							<div class="yui-b">
								<form method="get" action="/api/txt/">
									
									<div class="required">
										<label for="url">url*:</label>
										<textarea id="url" class="inputTextarea" cols="50" rows="5" name="url">
										<xsl:text> </xsl:text>
										</textarea>
										<!--<small>Must be 250 characters or less.</small>-->
									</div>
									
									
									<div class="optional">
										<label for="code">code (optional):
										</label>
										<div  id="codeOk">
											<img id="codeOkSpinner" style="visibility: hidden" src="{$webroot_yui}build/assets/skins/sam/wait.gif"/>
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
							</div>
							
						</div>
						
						<div class="yui-b">
							<a href="javascript:window.location='http://liip.to/?url='+encodeURIComponent(window.location);">Liip.to this</a> Bookmarklet
						</div>
					</div>
					<div id="ft">
						Liip.to - Provided to you by <a href="http://www.liip.ch/">Liip AG</a>
						
					</div>
				</div>
				<script type="text/javascript" src="{$webroot_yui}build/yahoo-dom-event/yahoo-dom-event.js"></script>
				<script type="text/javascript" src="{$webroot_yui}build/connection/connection-min.js"></script>
				<script type="text/javascript" src="{$webroot_yui}build/json/json-min.js"></script>
				<script type="text/javascript" src="{$webrootStatic}js/liipto.js"></script>
			</body>
		</html>
		
	</xsl:template>
</xsl:stylesheet>
