{include file="documentHeader"}
<head>
	<title>{lang}wcf.business.visit.pageTitle{/lang} - {lang}{$link->subject}{/lang} - {lang}{$category->title}{/lang} - {lang}wcf.business.links{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
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
		<img id="moderatorOptions{@$linkID}" src="{icon}{@$link->getIconName()}L.png{/icon}" alt=""{if $this->user->getPermission('mod.business.canEnableLinks') || $this->user->getPermission('mod.business.canEditLinks')}  title="{lang}wcf.business.moderation.pageTitle{/lang}" class="pointer"{/if} />
		<div class="headlineContainer">
			<h2>{lang}{$link->subject}{/lang} {if $this->user->getPermission('mod.business.canEnableLinks') || $this->user->getPermission('mod.business.canEditLinks')}<img id="moderatorOptions{@$linkID}" src="{icon}moderatorS.png{/icon}" alt="" title="{lang}wcf.business.moderation.pageTitle{/lang}" class="pointer" />{/if}</h2>
			<p style="display: inline;">{@$link->getRatingOutput()}</p>
		</div>
		{if $this->user->getPermission('mod.business.canEnableLinks') || $this->user->getPermission('mod.business.canEditLinks')}
			{include file='businessModeratorOptions' sandbox=false}
		{/if}
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	<div class="columnLayout-2">
		<div class="leftColumn">
			{include file='businessUserSidebar'}
			{include file='businessLinkGeneralSelection'}
			{include file='businessLinkTagsList'}
		</div>
		<div class="centerColumn">
			{include file='businessMenu'}
			
				<div class="border content">
					<div class="container-1">
						{lang}wcf.business.visist.reason{/lang}

						<div class="formSubmit">
							<input type="button" onclick="document.location.href='index.php?page=BusinessLinkVisit&amp;linkID={@$linkID}&amp;visit=1'" value="{lang}wcf.business.visit.pageTitle{/lang}" /> 
							<input type="button" onclick="document.location.href='index.php?page=BusinessLink&amp;linkID={@$linkID}'" value="{lang}wcf.business.visit.back{/lang}" />
						</div>
					</div>
				</div>	
		</div>
	</div>
</div>

{include file='footer' sandbox=false}

</body>
</html>
