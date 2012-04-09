{include file="documentHeader"}
<head>
	<title>{lang}wcf.business.links{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WCF_DIR}style/business.css" />
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
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}businessL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wcf.business.links{/lang}</h2>
			<p>{lang}wcf.business.links.description{/lang}</p>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if $reportedLinks || $disabledLinks && BUSINESS_LINK_ENABLE_MODERATION_NOTE && $this->user->getPermission('mod.business.canSeeModerationOverview')}
			<div class="info"><p>{lang}wcf.business.links.moderationNote{/lang}</p>
			<p class="smallFont">
			{if $reportedLinks}
				{if $reportedLinks > 1}
					{lang}wcf.business.links.reportedLinks{/lang}
				{else} 
					{lang}wcf.business.links.reportedLink{/lang}
				{/if}
			{/if}
			{if $disabledLinks}
				{if $reportedLinks}|{/if}
				{if $disabledLinks == 1}
					{lang}wcf.business.links.disabledLinks{/lang}
				{else} 
					{lang}wcf.business.links.disabledLink{/lang}
				{/if}</p>
			{/if}
			</div>
		{/if}
	
	{if $additionalTopContents|isset}{@$additionalTopContents}{/if}
	
		{if $categories|count > 0}
			<ul id="businessCategoryList">
			{cycle name='businessCategoryListCycle' values='1,2' advance=false print=false}
			{foreach from=$categories item=child}
			{assign var="category" value=$child.category}
			{assign var="subCategories" value=$child.subCategories}
			{assign var="categoryID" value=$category->categoryID}
			{counter assign=categoryNo print=false}
				<li class="border width33">
					<div class="container-{cycle values='1,2'} businessCategoryListInner category{@$categoryID}">
						<div class="containerIcon">
							<a href="index.php?page=BusinessCategory&amp;categoryID={@$categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$category->getIconName()}XL.png{/icon}" alt="" /></a>
						</div>
						<div class="containerContent">
							<h4>
								<a href="index.php?page=BusinessCategory&amp;categoryID={@$categoryID}{@SID_ARG_2ND}">{lang}{$category->title}{/lang}</a> <span class="smallFont light">({#$category->links})</span>
							</h4>
							{if $category->description && BUSINESS_CATEGORY_SHOW_CATEGORY_DESCRIPTION}
								<p class="businessCategoryListDescription">
									{if $category->allowDescriptionHtml}{lang}{@$category->description}{/lang}{else}{lang}{@$category->description|htmlspecialchars|nl2br}{/lang}{/if}
								</p>
							{/if}
							{if $subCategories|count}
								<div class="categoryListSubcategories">
									{implode from=$subCategories item=subCategory}
										
									{assign var="subCategoryID" value=$subCategory->categoryID}<img src="{icon}{@$subCategory->getIconName()}S.png{/icon}" alt="" /> <a href="index.php?page=BusinessCategory&amp;categoryID={@$subCategoryID}{@SID_ARG_2ND}">{lang}{$subCategory->title}{/lang}</a> <span class="smallFont light">({#$subCategory->links})</span>{/implode}
								</div>
							{/if}
						</div>
					</div>
				</li>
			{/foreach}
			</ul>
		{/if}
		
		{cycle values='container-1,container-2' print=false advance=false}
		<div class="border infoBox">
			<div class="{cycle}">
				<div class="containerIcon"><img src="{icon}statisticsM.png{/icon}" alt="" /></div>
				<div class="containerContent">
					<h3>{lang}wcf.business.statistics{/lang}</h3> 
					<p class="smallFont">{lang}wcf.business.statistics.details{/lang}</p>
				</div>
			</div>
			
			{if $additionalBoxes|isset}{@$additionalBoxes}{/if}
			
		</div>
		
		<div class="contentFooter">
			<div class="largeButtons">
				<ul>
					{if $this->user->getPermission('mod.business.canSeeModerationOverview')}
						<li><a href="index.php?page=BusinessModeration{@SID_ARG_2ND}" title="{lang}wcf.business.moderation.pageTitle{/lang}"><img src="{icon}businessModerationM.png{/icon}" alt="" /> <span>{lang}wcf.business.moderation.pageTitle{/lang}</span></a></li>
					{/if}
					{if $additionalPageButtons|isset}{@$additionalPageButtons}{/if}
				</ul>
			</div>
		</div>
</div>
	
{include file='footer' sandbox=false}

</body>
</html>
