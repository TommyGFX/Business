{include file="documentHeader"}
<head>
	<title>{lang}wcf.business.comment.comment{@$action|ucfirst}{/lang} - {lang}{$link->subject}{/lang} - {$category->title} - {lang}wcf.business.links{/lang} -  {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
	
	{include file='imageViewer'}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabbedPane.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH}; 
		//]]>
	</script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ImageResizer.class.js"></script>
	{if $canUseBBCodes}{include file="wysiwyg"}{/if}
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
	{capture append='userMessages'}
		{if $errorField}
			<p class="error">{lang}wcf.global.form.error{/lang}</p>
		{/if}
		
		{if $preview|isset}
			<div class="message content">
				<div class="messageInner container-1">
					<div class="messageHeader">
						<h4>{lang}wcf.message.preview{/lang}</h4>
					</div>
					<div class="messageBody">
						<div>{@$preview}</div>
					</div>
				</div>
			</div>
		{/if}
				
	{/capture}
	
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=Business{@SID_ARG_2ND}"><img src="{icon}businessS.png{/icon}" alt="" /> <span>{lang}wcf.business.links{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=BusinessCategory&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$parentCategory->getIconName()}S.png{/icon}" alt="" /> <span>{$parentCategory->title}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=BusinessCategory&amp;categoryID={@$categoryID}{@SID_ARG_2ND}"><img src="{icon}{$category->getIconName()}S.png{/icon}" alt="" /> <span>{lang}{$category->title}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=BusinessLink&amp;linkID={@$linkID}{@SID_ARG_2ND}"><img src="{icon}businessLinkS.png{/icon}" alt="" /> <span>{lang}{$link->subject}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=BusinessCommentsList&amp;linkID={@$linkID}{@SID_ARG_2ND}"><img src="{icon}businessCommentsListS.png{/icon}" alt="" /> <span>{lang}wcf.business.comment.comments{/lang}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}message{@$action|ucfirst}L.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wcf.business.comment.comment{@$action|ucfirst}{/lang}</h2>
			<p style="display: inline;">{$link->subject}</p>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}


	{include file='businessUserSidebar'}
	{include file='businessMenu'}
	
	<form method="post" action="index.php?form=BusinessComment{@$action|ucfirst}{if $action == 'add'}&amp;linkID={@$linkID}{elseif $action == 'edit'}&amp;commentID={@$commentID}{/if}">

		<div class="border" id="info-content">
			<div class="layout-2">
				<div class="columnContainer">
					<div class="container-1 column first">
						<div class="columnInner">
	
			<div class="container-1">
				<h3 class="subHeadline">{lang}wcf.business.comment.comment{@$action|ucfirst}{/lang}</h3>
				
				{if !$this->user->userID || $additionalInformationFields|isset}
					<fieldset>
						<legend>{lang}wcf.business.comment.generalInformations{/lang}</legend>
						
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
						
						{if $additionalInformationFields|isset}{@$additionalInformationFields}{/if}
					</fieldset>
				{/if}
				
				<fieldset>
					<legend>{lang}wcf.business.comment.message{/lang}</legend>
					
					<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">
						<div class="formFieldLabel">
							<label for="text">{lang}wcf.business.comment.message{/lang}</label>
						</div>
						
						<div class="formField">				
							<textarea name="text" id="text" rows="15" cols="40" tabindex="{counter name='tabindex'}">{$text}</textarea>
							{if $errorField == 'text'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'tooLong'}{lang}wcf.message.error.tooLong{/lang}{/if}
									{if $errorType == 'censoredWordsFound'}{lang}wcf.message.error.censoredWordsFound{/lang}{/if}
								</p>
							{/if}
						</div>
						
					</div>
					
					{include file='messageFormTabs'}
				</fieldset>
				
				{include file='captcha'}
				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>
		
		<div class="formSubmit">
			<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="submit" name="preview" accesskey="p" value="{lang}wcf.global.button.preview{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
			{@SID_INPUT_TAG}
		</div>
	</form>

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
{include file='footer' sandbox=false}
</body>
</html>
