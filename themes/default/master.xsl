<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:i18n="http://apache.org/cocoon/i18n/2.1"
	exclude-result-prefixes="xhtml i18n"
	version="1.0">
	<xsl:import href="params.xsl"/>
	
	<xsl:variable name="url" select="$queryinfo/query/url"/>
	
	<xsl:template match="/">
		<html lang="{$lang}" xml:lang="{$lang}">
			<head>
				<title>
					<xsl:call-template name="page_head_title"/>
				</title>
				
				<xsl:call-template name="html_head_css"/>
				
			</head>
			<body>
				<xsl:call-template name="page_body"/>
			</body>
		</html>
		
		
	</xsl:template>
	
	<xsl:template name="page_head_title">
		<i18n:text>PageTitle</i18n:text>
	</xsl:template>

	<xsl:template name="html_head_css">
		<link rel="stylesheet" type="text/css" href="{$webroot_yui}build/reset-fonts-grids/reset-fonts-grids.css" />
		<link rel="stylesheet" type="text/css" href="{$webroot_yui}build/base/base.css" />
		<link rel="stylesheet" type="text/css" href="{$webrootStatic}css/default.css" media="screen" />
		<xsl:call-template name="page_head_css"/>
	</xsl:template>
	
	<xsl:template name="page_head_css">
		
	</xsl:template>
	
	<xsl:template name="page_body">
		<div id="doc4" class="yui-t4">
			<xsl:call-template name="html_body_header"/>
			<xsl:call-template name="html_body_content"/>
			<xsl:call-template name="html_body_footer"/>
		</div>
		<xsl:call-template name="page_bottom_javascript"/>
	</xsl:template>

	<xsl:template name="html_body_header">
		<div id="hd">
			<xsl:call-template name="page_body_header"/>
		</div>
	</xsl:template>

	<xsl:template name="page_body_header">
		<h1>
			<i18n:text>WelcomeText</i18n:text>
		</h1>
	</xsl:template>
	
	<xsl:template name="html_body_content">
		<div id="bd">
			<div id="yui-main">
				<div class="yui-b">
					<xsl:call-template name="page_body_content"></xsl:call-template>
				</div>
			</div>
			<xsl:call-template name="html_body_right"/>
		</div>
	</xsl:template>

	<xsl:template name="page_body_content">
		No content
	</xsl:template>

	<xsl:template name="html_body_right">
		<div class="yui-b">
			<xsl:call-template name="page_body_right"/>
		</div>
	</xsl:template>
	
	<xsl:template name="page_body_right">
		<a href="javascript:window.location='http://liip.to/?url='+encodeURIComponent(window.location);">Liip.to this</a> Bookmarklet
	</xsl:template>
	
	<xsl:template name="html_body_footer">
		<div id="ft">
			<xsl:call-template name="page_body_footer"/>
		</div>
	</xsl:template>
	
	<xsl:template name="page_body_footer">
		Liip.to - Provided to you by <a href="http://www.liip.ch/">Liip AG</a>
	</xsl:template>
	
	<xsl:template name="page_bottom_javascript">
		<script type="text/javascript" src="{$webroot_yui}build/yahoo-dom-event/yahoo-dom-event.js"></script>
		<script type="text/javascript" src="{$webroot_yui}build/connection/connection-min.js"></script>
		<script type="text/javascript" src="{$webroot_yui}build/json/json-min.js"></script>
		<script type="text/javascript" src="{$webrootStatic}js/liipto.js"></script>
	</xsl:template>
	
	
</xsl:stylesheet>
