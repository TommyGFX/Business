{include file="documentHeader"}
<head>
	<title>{lang}wcf.business.link.move.pageTitle{/lang} - {lang}{$link->subject}{/lang} - {lang}{$category->title}{/lang} - {lang}wcf.business.links{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
			var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH};
		//]]>
	</script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WCF_DIR}style/business.css" />
	{include file='imageViewer'}
	
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{* --- quick search controls --- *}
{assign var='searchFieldTitle' value='{lang}wcf.business.link.search.query{/lang}'}
{capture assign=searchHiddenFields}
	<input type="hidden" name="types[]" value="businessLink" />
{/capture}
{* --- end --- *}
{include file='header' sandbox=false}

<div id="main">
	
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=Business{@SID_ARG_2ND}"><img src="{icon}businessS.png{/icon}" alt="" /> <span>{lang}wcf.business.links{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=BusinessCategory&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$parentCategory->getIconName()}S.png{/icon}" alt="" /> <span>{$parentCategory->title}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=BusinessCategory&amp;categoryID={@$categoryID}{@SID_ARG_2ND}"><img src="{icon}{$category->getIconName()}S.png{/icon}" alt="" /> <span>{lang}{$category->title}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=BusinessLink&amp;linkID={@$linkID}{@SID_ARG_2ND}"><img src="{icon}businessLinkS.png{/icon}" alt="" /> <span>{lang}{$link->subject}{/lang}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}{@$link->getIconName()}L.png{/icon}" alt=""{if $this->user->getPermission('mod.business.canEnableLinks') || $this->user->getPermission('mod.business.canEditLinks')}  title="{lang}wcf.business.moderation.pageTitle{/lang}" id="moderatorOptions{@$linkID}" class="pointer"{/if} />
		<div class="headlineContainer">
			<h2>{lang}{$link->subject}{/lang} {if $this->user->getPermission('mod.business.canEnableLinks') || $this->user->getPermission('mod.business.canEditLinks')}<img src="{icon}moderatorS.png{/icon}" alt="" title="{lang}wcf.business.moderation.pageTitle{/lang}" id="moderatorOptions{@$linkID}" class="pointer" />{/if}</h2>
			<p style="display: inline;">{@$link->getRatingOutput()}</p>
		</div>
		{if $this->user->getPermission('mod.business.canEnableLinks') || $this->user->getPermission('mod.business.canEditLinks')}
			{include file='businessModeratorOptions' sandbox=false}
		{/if}
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if $link->status != 3}
		<div class="info">
			<p>{lang}wcf.business.link.status{$link->status}.description{/lang}</p>
			<p class="smallFont"><strong>{lang}wcf.business.link.statusComment.short{/lang}:</strong> {if $link->statusComment != ''}{@$link->statusComment}{else}-{/if}</p>
		</div>
	{/if}

	
	{include file='businessUserSidebar'}
	{include file='businessMenu'}

	<div class="border" id="info-content">
		<div class="layout-2">
			<div class="columnContainer">
				<div class="container-1 column first">
					<div class="columnInner">	

			<div class="container-1">
	
				<form method="post" action="index.php?form=BusinessLinkMove&amp;linkID={@$link->linkID}">
					<h3 class="subHeadline">{lang}wcf.business.link.move.title{/lang}</h3>

					<fieldset>
						<legend>{lang}wcf.business.link.move.title{/lang}</legend>
						<div class="formElement">
							<div class="formFieldLabel">
								<label for="text">{lang}Kategorie{/lang}</label>
							</div>
							<div class="formField{if $errorField == 'categoryID'} formError{/if}">
								<select name="categoryID" id="categoryID">
									{htmlOptions options=$categorySelect disableEncoding=true selected=$categoryID}
								</select>
								{if $errorField == 'categoryID'}
									<p class="innerError">
										{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
										{if $errorType == 'illegalCategory'}{lang}wcf.business.link.move.illegalCategory{/lang}{/if}
									</p>
								{/if}
							</div>
						</div>
					</fieldset>
				
					<div class="formSubmit">
						<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
						<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
						{@SID_INPUT_TAG}
					</div>
				</form>
			</div>
					</div>
				</div>

				<div class="container-3 column second sidebar profileSidebar">
					<div class="columnInner">
						{include file='businessLinkGeneralSelection'}
						{include file='businessLinkTagsList'}
					</div>
				</div>

			</div>
		</div>
</div>
</div>
{include file="footer"}
</body>
</html>
