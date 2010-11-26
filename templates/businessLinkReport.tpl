{include file="documentHeader"}
<head>
	<title>{lang}wcf.business.report.pageTitle{/lang} - {lang}{$link->subject}{/lang} - {lang}{$category->title}{/lang} - {lang}wcf.business.links{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
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
		<li><a href="index.php?page=Business{@SID_ARG_2ND}"><img src="{icon}businessS.png{/icon}" alt="" /> <span>{lang}wcf.business.links{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=BusinessCategory&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$parentCategory->getIconName()}S.png{/icon}" alt="" /> <span>{$parentCategory->title}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=BusinessCategory&amp;categoryID={@$categoryID}{@SID_ARG_2ND}"><img src="{icon}{$category->getIconName()}S.png{/icon}" alt="" /> <span>{lang}{$category->title}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=BusinessLink&amp;linkID={@$linkID}{@SID_ARG_2ND}"><img src="{icon}businessLinkS.png{/icon}" alt="" /> <span>{lang}{$link->subject}{/lang}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}businessLinkReportL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wcf.business.report.pageTitle{/lang}</h2>
			<p>{lang}wcf.business.report.pageTitle.description{/lang}</p>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if $errorField}
		<p class="error">{lang}wcf.global.form.error{/lang}</p>
	{/if}
	
	
	{include file='businessUserSidebar'}
	{include file='businessMenu'}

	<div class="border" id="info-content">
		<div class="layout-2">
			<div class="columnContainer">
				<div class="container-1 column first">
					<div class="columnInner">	
	
		<form method="post" action="index.php?form=BusinessLinkReport&amp;linkID={@$linkID}">
				<div class="container-1">
					<h3 class="subHeadline">{lang}wcf.business.report.pageTitle{/lang}</h3>
						
						{if !$this->user->userID}
							<div class="formElement{if $errorField == 'username'} formError{/if}">
								<div class="formFieldLabel">
									<label for="username">{lang}wcf.user.username{/lang}</label>
								</div>
								<div class="formField">
									<input type="text" class="inputText" name="username" id="username" value="{$username}" tabindex="{counter name='tabindex'}" />
									{if $errorField == 'username'}
										<p class="innerError">
											{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
											{if $errorType == 'notValid'}{lang}wcf.user.error.username.notValid{/lang}{/if}
											{if $errorType == 'notAvailable'}{lang}wcf.user.error.username.notUnique{/lang}{/if}
										</p>
									{/if}
								</div>
							</div>
						{/if}
					
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="text">{lang}wcf.business.report.reason{/lang}</label>
						</div>	
						
						<div class="formField{if $errorField == 'text'} formError{/if}">
							<textarea id="text" name="text" rows="10" cols="20">{$text}</textarea>
							{if $errorField == 'text'}
								<p class="innerError">{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}</p>
							{/if}
						</div>
						<div class="formFieldDesc">
							<p>{lang}wcf.business.report.reason.description{/lang}</p>
						</div>
					</div>
					
					{include file='captcha'}
					{if $additionalFields|isset}{@$additionalFields}{/if}
				</div>
		
			<div class="formSubmit">
				<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
				<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
				{@SID_INPUT_TAG}
			</div>
		</form>
		
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
