{include file="documentHeader"}
<head>
	<title>{lang}wcf.business.link.{@$action}{/lang} - {lang}{$category->title}{/lang} - {lang}wcf.business.links{/lang} - {lang}{PAGE_TITLE}{/lang}</title>

	{include file='headInclude' sandbox=false}

	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabbedPane.class.js"></script>
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
			<div class="border messagePreview">
				<div class="containerHead">
					<h3>{lang}wcf.message.preview{/lang}</h3>
				</div>
				<div class="message content">
					<div class="messageInner container-1">
						{if $subject}
							<h4><a href="{$url}" class="externalURL">{$subject}</a></h4>
						{/if}
						<div class="messageBody">
							<div>{@$preview}</div>
						</div>
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
		{if $linkID}
			<li><a href="index.php?page=BusinessLink&amp;linkID={@$linkID}{@SID_ARG_2ND}"><img src="{icon}businessLinkS.png{/icon}" alt="" /> <span>{lang}{$link->subject}{/lang}</span></a> &raquo;</li>
		{/if}
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}businessLink{@$action|ucfirst}L.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wcf.business.link.{@$action}{/lang}</h2>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	<form enctype="multipart/form-data" method="post" action="{if $action == 'add'}index.php?form=BusinessLinkAdd&amp;categoryID={@$categoryID}{else}index.php?form=BusinessLinkEdit&amp;linkID={@$linkID}{/if}">

	<div class="border content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}wcf.business.link.{@$action}{/lang}</h3>
				
			{if $this->user->getPermission('mod.business.canEditLinks')}
				<fieldset>
					<legend>{lang}wcf.business.link.add.isSticky{/lang}</legend>
						
					<div class="formGroup">
						<div class="formGroupLabel">
							{lang}wcf.business.link.add.stick{/lang}
						</div>
						<div class="formGroupField">
							<fieldset>
								<legend>{lang}wcf.business.link.add.stick{/lang}</legend>
								<div class="formField">
									<ul class="formOptions">
										<li><label><input type="radio" name="isSticky" value="0" {if $isSticky == 0}checked="checked" {/if}tabindex="{counter name='tabindex'}" /> <img src="{icon}businessLinkM.png{/icon}" alt="" /> {lang}wcf.business.link.add.isSticky0{/lang}</label></li>
										<li><label><input type="radio" name="isSticky" value="1" {if $isSticky == 1}checked="checked" {/if}tabindex="{counter name='tabindex'}" /> <img src="{icon}businessLinkStickyM.png{/icon}" alt="" /> {lang}wcf.business.link.add.isSticky1{/lang}</label></li>
									</ul>
								</div>
							</fieldset>
						</div>
					</div>
				</fieldset>
			{/if}
				
			<fieldset>
				<legend>{lang}wcf.business.link.add.general{/lang}</legend>
					
				{if $action == 'add' && $this->user->userID == 0}
					<div class="formElement{if $errorField == 'username'} formError{/if}">
						<div class="formFieldLabel">
							<label for="username">{lang}wcf.user.username{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" name="username" id="username" value="{@$username}" tabindex="{counter name='tabindex'}" />
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
					
				<div class="formElement{if $errorField == 'subject'} formError{/if}">
					<div class="formFieldLabel">
						<label for="subject">{lang}wcf.business.link.add.subject{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" name="subject" id="subject" value="{$subject}" tabindex="{counter name='tabindex'}" />
						{if $errorField == 'subject'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>

				<div class="formElement{if $errorField == 'ort'} formError{/if}">
					<div class="formFieldLabel">
						<label for="ort">{lang}wcf.business.link.add.ort{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" name="ort" id="subject" value="{$ort}" tabindex="{counter name='tabindex'}" />
						{if $errorField == 'ort'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>

				<div class="formElement{if $errorField == 'url'} formError{/if}">
					<div class="formFieldLabel">
						<label for="url">{lang}wcf.business.link.add.url{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" name="url" id="url" value="{if $url == ''}http://{else}{$url}{/if}" tabindex="{counter name='tabindex'}" />
						{if $errorField == 'url'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								{if $errorType == 'illegalURL'}{lang}wcf.business.link.add.illegalURL{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc">
						<p>{lang}wcf.business.link.add.url.description{/lang}</p>
					</div>
				</div>
				
				{if BUSINESS_LINK_KINDS != ""}
					<div class="formElement{if $errorField == 'kind'} formError{/if}">
						<div class="formFieldLabel">
							<label for="kind">{lang}wcf.business.link.add.kind{/lang}</label>
						</div>
						<div class="formField">
							<select name="kind" id="kind" tabindex="{counter name='tabindex'}">
								<option value=""></option>
								{foreach from=$kinds item=foreachKind}
									<option value="{@$foreachKind}"{if $kind == $foreachKind} selected="selected"{/if}>{lang}{@$foreachKind}{/lang}</option>
								{/foreach}
							</select>
							{if $errorField == 'kind'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>
				{/if}
					
				{if MODULE_TAGGING}
					{include file='tagAddBit'}
				{/if}

				{if BUSINESS_LINK_ENABLE_AGE}
					<div class="formElement{if $errorField == 'age'} formError{/if}">
						<div class="formFieldLabel">
							<label for="age">{lang}wcf.business.link.add.age{/lang}</label>
						</div>
						<div class="formField">
							<select name="age" id="age" tabindex="{counter name='tabindex'}">
								<option value="0"{if $age == '0'} selected="selected"{/if}>{lang}wcf.business.link.add.age0{/lang}</option>
								<option value="14"{if $age == '14'} selected="selected"{/if}>{lang}wcf.business.link.add.age14{/lang}</option>
								<option value="18"{if $age == '18'} selected="selected"{/if}>{lang}wcf.business.link.add.age18{/lang}</option>
							</select>
						</div>
						<div class="formFieldDesc">
							<p>{lang}wcf.business.link.add.age.description{/lang}</p>
						</div>
					</div>
				{/if}
					
				<div class="formElement{if $errorField == 'languageID'} formError{/if}">
					<div class="formFieldLabel">
						<label for="languageID">{lang}wcf.user.language{/lang}</label>
					</div>
					<div class="formField">
						<select name="languageID" id="languageID" tabindex="{counter name='tabindex'}">
							{foreach from=$this->language->getAvailableLanguageCodes() item=lang key=keyLanguageID}
								<option value="{$keyLanguageID}"{if $languageID == $keyLanguageID}} selected="selected"{/if}>{lang}wcf.global.language.{@$lang}{/lang}</option>
							{/foreach}
						</select>
					</div>
				</div>
					
				<div class="formElement{if $errorField == 'shortDescriptiont'} formError{/if}">
					<div class="formFieldLabel">
						<label for="shortDescription">{lang}wcf.business.link.shortDescription{/lang}</label>
					</div>			
					<div class="formField">
						<textarea id="shortDescription" name="shortDescription" rows="5" cols="40" tabindex="{counter name='tabindex'}">{$shortDescription}</textarea>
						{if $errorField == 'shortDescription'}
							<p class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.error.empty{/lang}
								{/if}
							</p>
						{/if}
					</div>
				</div>
					
				{if $additionalInformationFields|isset}{@$additionalInformationFields}{/if}
			</fieldset>
				
			<fieldset>
				<legend>{lang}wcf.business.link.message{/lang}</legend>
					
				<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">
					<div class="formFieldLabel">
						<label for="text">{lang}wcf.business.link.message{/lang}</label>
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
		
			<div class="formSubmit">
				<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
				<input type="submit" name="preview" accesskey="p" value="{lang}wcf.global.button.preview{/lang}" tabindex="{counter name='tabindex'}" />
				<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
				{@SID_INPUT_TAG}
				<input type="hidden" name="idHash" value="{$idHash}" />
			</div>
		</div>
	</div>
	</form>
</div>

{include file='footer' sandbox=false}
</body>
</html>
