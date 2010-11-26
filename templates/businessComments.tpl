{include file="documentHeader"}
<head>
	<title>{lang}wcf.business.comment.commentsList{/lang} - {lang}{$link->subject}{/lang} - {lang}{$category->title}{/lang} - {lang}wcf.business.links{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WCF_DIR}style/business.css" />
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH}; 
		//]]>
	</script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ImageResizer.class.js"></script>
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
			<h3 class="subHeadline">{lang}wcf.business.comment.commentsList{/lang}</h3>
			
			{if $link->isClosed == 1}
				<p class="info">{lang}wcf.business.comment.linkIsClosed{/lang}</p>
			{/if}
			
			{if !$comments|count}
				<div class="border tabMenuContent">
					<div class="container-1">
						<p>{lang}wcf.business.comment.noComments{/lang}</p>
					</div>
				</div>
			{/if}
			
			<div class="contentHeader">
				{pages print=true assign=pagesOutput link="index.php?page=BusinessCommentsList&linkID=$linkID&pageNo=%d"|concat:SID_ARG_2ND_NOT_ENCODED}
				
				<div class="largeButtons">
					<ul>
						{if $link->isCommentable()}
							<li><a href="index.php?form=BusinessCommentAdd&amp;linkID={@$linkID}{@SID_ARG_2ND}" title="{lang}wcf.business.comment.commentAdd{/lang}"><img src="{icon}messageAddM.png{/icon}" alt="" /> <span>{lang}wcf.business.comment.commentAdd{/lang}</span></a></li>
						{/if}
						{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
					</ul>
				</div>
			</div>
			
			{* build message css classes *}
			{if $this->getStyle()->getVariable('messages.color.cycle')}
				{cycle name=messageCycle values='2,1' print=false}
			{else}
				{cycle name=messageCycle values='1' print=false}
			{/if}

			{if $this->getStyle()->getVariable('messages.sidebar.color.cycle')}
				{if $this->getStyle()->getVariable('messages.color.cycle')}
					{cycle name=postCycle values='1,2' print=false}
				{else}
					{cycle name=postCycle values='3,2' print=false}
				{/if}
			{else}
				{cycle name=postCycle values='3' print=false}
			{/if}
	
			{capture assign='messageClass'}message{if $this->getStyle()->getVariable('messages.framed')}Framed{/if}{@$this->getStyle()->getVariable('messages.sidebar.alignment')|ucfirst}{if $this->getStyle()->getVariable('messages.sidebar.divider.use')} dividers{/if}{/capture}
			{capture assign='messageFooterClass'}messageFooter{@$this->getStyle()->getVariable('messages.footer.alignment')|ucfirst}{/capture}

			{assign var=startIndex value=$items-$startIndex+1}
			{foreach from=$comments item=comment}
				{assign var="sidebar" value=$sidebarFactory->get('businessComment', $comment->commentID)}
				{assign var="author" value=$sidebar->getUser()}
			
				<div class="deletable message" id="commentRow{@$comment->commentID}">
					<div class="messageInner {@$messageClass} container-{cycle name=postCycle}{if !$author->userID} guestPost{/if}">
						<a id="comment{@$comment->commentID}"></a>
							{include file='messageSidebar'}
					
								<div class="messageContent">
									<div class="messageContentInner color-{cycle name=messageCycle}">
										<div class="messageHeader">
											<p class="messageCount">
												<a href="index.php?page=BusinessCommentsList&amp;linkID={@$linkID}&amp;commentID={@$comment->commentID}#comment{@$comment->commentID}" class="messageNumber">{#$startIndex}</a>
											</p>
											<div class="containerIcon">
												<img src="{icon}businessCommentM.png{/icon}" alt="" />
											</div>
											<div class="containerContent">
												<p class="smallFont light">{@$comment->time|time}</p>
											</div>
										</div>
													
										<div class="messageBody" id="commentMessage{@$comment->commentID}">
											{@$comment->getFormattedMessage()}
										</div>
							
										<div class="{@$messageFooterClass}">
											<div class="smallButtons">
												<ul>
													<li class="extraButton"><a href="#top"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" title="{lang}wcf.global.scrollUp{/lang}" /></a></li>
													{if $link->isClosed == 0 || $this->user->getPermission('mod.business.canEditComments')}
														{if $comment->isEditable()}
															<li><a href="index.php?form=BusinessCommentEdit&amp;commentID={@$comment->commentID}{@SID_ARG_2ND}" title="{lang}wcf.business.comment.commentEdit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>
														{/if}
										
														{if $comment->isDeletable()}
															<li><a href="index.php?action=BusinessCommentDelete&amp;commentID={@$comment->commentID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" class="deleteButton" title="{lang}wcf.business.comment.commentDelete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" longdesc="{lang}wcf.business.comment.deleteSure{/lang}" /> <span>{lang}wcf.global.button.delete{/lang}</span></a></li>
														{/if}
													{/if}
													{if MODULE_USER_INFRACTION == 1 && $this->user->getPermission('admin.user.infraction.canWarnUser') && $comment->userID}
														<li><a href="index.php?form=UserWarn&amp;userID={@$comment->userID}&amp;objectType=businessComment&amp;objectID={@$comment->commentID}{@SID_ARG_2ND}" title="{lang}wcf.user.infraction.button.warn{/lang}"><img src="{icon}infractionWarningS.png{/icon}" alt="" /> <span>{lang}wcf.user.infraction.button.warn{/lang}</span></a></li>
													{/if}	
													{if $additionalSmallButtons|isset}{@$additionalSmallButtons}{/if}			
												</ul>
											</div>
										</div>
										<hr />
									</div>
								</div>
							</div>
						</div>
				{assign var="startIndex" value=$startIndex - 1}
			{/foreach}
			
			
			<div class="contentFooter">
				{@$pagesOutput}
						
				<div class="largeButtons">
					<ul>
						{if $link->isCommentable() && $comments|count}
							<li><a href="index.php?form=BusinessCommentAdd&amp;linkID={@$linkID}{@SID_ARG_2ND}" title="{lang}wcf.business.comment.commentAdd{/lang}"><img src="{icon}messageAddM.png{/icon}" alt="" /> <span>{lang}wcf.business.comment.commentAdd{/lang}</span></a></li>
						{/if}
						{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
					</ul>
				</div>
			</div>

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
