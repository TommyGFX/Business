{include file="documentHeader"}
<head>
	<title>{lang}{$link->subject}{/lang} - {lang}{$category->title}{/lang} - {lang}wcf.business.links{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
			var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH};
		//]]>
	</script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ImageResizer.class.js"></script>
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
			<fieldset class="noJavaScript">
				<legend class="noJavaScript">{lang}wcf.business.link.informations{/lang}</legend>
		
			<div class="message">
				<div class="messageHeader">
					<div class="containerIcon">
						<img src="{icon}businessLinkM.png{/icon}" alt="" />
					</div>
					<div class="containerContent">
						<p class="smallFont light">{@$link->lastChangeTime|time}</p>
					</div>
				</div>	
					
				<h3>
					<span>{$link->subject}</span>
				</h3>
				<p class="messageHeader">{@$link->shortDescription}</p>
						
				<div class="businessData messageHeader">
					<div class="formElement">
						<p class="formFieldLabel">{lang}wcf.business.link.directURL{/lang}</p>
						<p class="formField"><a href="index.php?page=BusinessLinkVisit&amp;linkID={@$linkID}" class="external">{$link->url}</a></p>
					</div>
					<div class="formElement">
						<p class="formFieldLabel">{lang}wcf.business.link.time{/lang}</p>
						<p class="formField">{@$link->time|time}</p>
					</div>
					<div class="formElement">
						<p class="formFieldLabel">{lang}wcf.user.language{/lang}</p>
						<p class="formField">{@$link->getLanguageIcon()}</p>
					</div>
					{if !$link->ratingDisabled && $category->getPermission('canRateLink') && BUSINESS_LINK_ENABLE_RATING && $link->userID != $this->user->userID}
					<div class="formElement">
						<p class="formFieldLabel">{lang}wcf.business.category.sortBy.rating{/lang}</p>
						<div class="formField">
							<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/BusinessLinkRating.class.js"></script>
							<form method="post" action="index.php?page=BusinessLink">
								<div>
									<input type="hidden" name="linkID" value="{@$linkID}" />												<input type="hidden" id="businessLinkRating" name="rating" value="0" />
									<span class="hidden" id="businessLinkRatingSpan"></span> 
										<noscript>
											<div>
												<select id="businessLinkRatingSelect" name="rating">
													<option value="1"{if $link->userRating == 1} selected="selected"{/if}>1</option>
													<option value="2"{if $link->userRating == 2} selected="selected"{/if}>2</option>
													<option value="3"{if $link->userRating == 3} selected="selected"{/if}>3</option>
													<option value="4"{if $link->userRating == 4} selected="selected"{/if}>4</option>
													<option value="5"{if $link->userRating == 5} selected="selected"{/if}>5</option>
												</select>
											<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" />
											</div>
										</noscript>
								</div>
							</form>
							<script type="text/javascript">
								//<![CDATA[
								new BusinessLinkRating('businessLinkRating', {@$link->userRating|intval});
								//]]>
							</script>
						</div>
					</div>
					{/if}
					{if BUSINESS_LINK_ENABLE_AGE && $link->age != 0}
						<div class="formElement">
							<p class="formFieldLabel">{lang}wcf.business.link.age{/lang}</p>
							<p class="formField">{@$link->age}</p>
						</div>
					{/if}
					<div class="formElement">
						<p class="formFieldLabel">{lang}wcf.business.link.hits{/lang}</p>
						<p class="formField">{@$link->hits}</p>
					</div>
					{if $link->kind}
						<div class="formElement">
							<p class="formFieldLabel">{lang}wcf.business.link.add.kind{/lang}</p>
							<p class="formField">{$link->kind}</p>
						</div>
					{/if}
				</div>
						
				<div class="messageBody">
					{@$link->getFormattedMessage()}
					
					{if $category->getPermission('canVisitLink')}	
						<fieldset>
							<h2 style="text-align:right;">									
								&raquo; <a href="index.php?page=BusinessLinkVisit&amp;linkID={@$linkID}{@SID_ARG_2ND}">{lang}wcf.business.link.visit.description{/lang}</a> <span class="smallFont light">({#$link->hits})</span>
							</h2>
						</fieldset>
					{/if}
				</div>
										
				{include file='attachmentsShow' messageID=$link->linkID author=$link->getAuthor()}
				
				{if $this->user->getPermission('mod.business.canEnableLinks')}
					<a id="statusEdit"></a>
						<div class="contentBox">
							<form method="post" action="index.php?action=BusinessLinkStatusEdit&amp;linkID={@$linkID}">
								<fieldset>
									<legend>{lang}wcf.business.link.statuEdit{/lang}</legend>

									<div class="formElement">
										<div class="formFieldLabel">
											<label for="status">{lang}wcf.business.link.status{/lang}</label>
										</div>
										<div class="formField">
											<select name="status" id="status">
												<option value="1"{if $link->status == '1'} selected="selected"{/if}>{lang}wcf.business.link.status1{/lang}</option>
												<option value="2"{if $link->status == '2'} selected="selected"{/if}>{lang}wcf.business.link.status2{/lang}</option>
												<option value="3"{if $link->status == '3'} selected="selected"{/if}>{lang}wcf.business.link.status3{/lang}</option>
												<option value="4"{if $link->status == '4'} selected="selected"{/if}>{lang}wcf.business.link.status4{/lang}</option>
											</select>
										</div>
									</div>

									<div class="formElement">
										<div class="formFieldLabel">
											<label for="statusComment">{lang}wcf.business.link.statusComment{/lang}</label>
										</div>
										<div class="formField">
											<textarea name="statusComment" id="statusComment" rows="10" cols="40">{$link->statusComment}</textarea>
											<label><input type="checkbox" name="notificationViaPN" value="1" {if $link->userID}checked="checked" {else}disabled="disabled" {/if}/> {lang}wcf.business.link.notificationViaPN{/lang}</label>
											<p class="smallFont">{lang}wcf.business.link.notificationViaPN.description{/lang}</p>
										</div>
									</div>
									
									<div class="formSubmit">
										{@SID_INPUT_TAG}
										<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
										<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
									</div>
								</fieldset>
							</form>
						</div>
					{/if}

				<div class="messageFooter">
					<div class="smallButtons">
						<ul>
							<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
							{if $link->userID == $this->user->userID && $this->user->userID && $category->getPermission('canDeleteOwnLink') || $this->user->getPermission('mod.business.canDeleteLinks')}<li><a href="index.php?action=BusinessLinkDelete&amp;linkID={@$linkID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm('{lang}wcf.business.link.delete.sure{/lang}')" title="{lang}wcf.business.link.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.delete{/lang}</span></a></li>{/if}
							{if ($category->getPermission('canEditOwnLink') && $link->isOwnLink() && $this->user->userID) || $this->user->getPermission('mod.business.canEditLinks')}<li><a href="index.php?form=BusinessLinkMove&amp;linkID={@$linkID}{@SID_ARG_2ND}" title="{lang}wcf.business.link.move.pageTitle{/lang}"><img src="{icon}nextS.png{/icon}" alt="" /> <span>{lang}wcf.business.link.move{/lang}</span></a></li>{/if}
							{if $link->isEditable()}<li><a href="index.php?form=BusinessLinkEdit&amp;linkID={@$link->linkID}{@SID_ARG_2ND}" title="{lang}wcf.business.link.linkEdit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}
							{if MODULE_USER_INFRACTION == 1 && $this->user->getPermission('admin.user.infraction.canWarnUser') && $link->userID}
								<li><a href="index.php?form=UserWarn&amp;userID={@$link->userID}&amp;objectType=businessLink&amp;objectID={@$link->linkID}{@SID_ARG_2ND}" title="{lang}wcf.user.infraction.button.warn{/lang}"><img src="{icon}infractionWarningS.png{/icon}" alt="" /> <span>{lang}wcf.user.infraction.button.warn{/lang}</span></a></li>
							{if $additionalSmallButtons|isset}{@$additionalSmallButtons}{/if}
							{/if}														
						</ul>
					</div>
				</div>
				
			</div>
			</fieldset>
						</div>
					</div>
				</div>

				<div class="container-3 column second sidebar profileSidebar">
					<div class="columnInner">
{if $additionalContent1|isset}{@$additionalContent1}{/if}
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

