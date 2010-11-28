{include file="documentHeader"}
<head>
	<title>{lang}{$category->title}{/lang} - {lang}wcf.business.links{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
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
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}{@$category->getIconName()}L.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}{$category->title}{/lang}</h2>
			<p>{lang}wcf.business.category.description{/lang}</p>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if $categories|count > 0}
			<ul id="businessCategoryList">
			{cycle name='businessCategoryListCycle' values='1,2' advance=false print=false}
			{foreach from=$categories item=child}
			{assign var="categoryList" value=$child.category}
			{counter assign=categoryNo print=false}
				<li class="border width33">
					<div class="container-{cycle values='1,2'} businessSmallCategoryListInner category{@$categoryList->categoryID}">
						<div class="containerIcon">
							<a href="index.php?page=BusinessCategory&amp;categoryID={@$categoryList->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$categoryList->getIconName()}{/icon}L.png" alt="" /></a>
						</div>
						<div class="containerContent">
							<h4>
								<a href="index.php?page=BusinessCategory&amp;categoryID={@$categoryList->categoryID}{@SID_ARG_2ND}">{lang}{$categoryList->title}{/lang}</a> <span class="smallFont light">({#$categoryList->links})</span>
							</h4>
							{if $categoryList->description && BUSINESS_CATEGORY_SHOW_CATEGORY_DESCRIPTION}
								<p class="businessCategoryListDescription">
									{if $categoryList->allowDescriptionHtml}{lang}{@$categoryList->description}{/lang}{else}{lang}{@$categoryList->description|htmlspecialchars|nl2br}{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>
				</li>
			{/foreach}
			</ul>
		{/if}
			
			<div class="contentHeader">
				{pages print=true assign=pagesOutput link="index.php?page=BusinessCategory&categoryID=$categoryID&tagID=$tagID&pageNo=%d"|concat:SID_ARG_2ND_NOT_ENCODED}
				
				<div class="largeButtons">
					<ul>
						{if $category->getPermission('canAddLink')}
							<li><a href="index.php?form=BusinessLinkAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wcf.business.link.add{/lang}"><img src="{icon}businessLinkAddM.png{/icon}" alt="" /> <span>{lang}wcf.business.link.add{/lang}</span></a></li>
						{/if}
						{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
					</ul>
				</div>
			</div>
			{if $links|count == 0}	
				<div class="border content">
					<div class="container-1">
						<p>{lang}wcf.business.category.noLinks{/lang}</p>
					</div>
				</div>
			{else}
				<div class="businessLinksList">
					{cycle values='container-1,container-2' name='className' print=false advance=false}
					{foreach from=$links item=link}
							<div class="message content {if $link->isDisabled}disabled{/if}">
								<div class="messageInner {cycle name='className'}">
									<div class="projectDetails">
										<div class="messageHeader">
											<div class="containerIcon">
												<img src="{icon}{@$link->getIconName()}M.png{/icon}" alt=""{if $this->user->getPermission('mod.business.canEnableLinks') || $this->user->getPermission('mod.business.canEditLinks')} id="moderatorOptions{@$link->linkID}" class="pointer"{/if} />
											</div>
											<div class="containerContent">
												<h4>{if $this->user->getPermission('mod.business.canEnableLinks')}<span class="prefix"><strong>[{lang}wcf.business.link.status{$link->status}{/lang}]</strong></span>{/if} {if $link->kind}<span class="prefix">{$link->kind}</span>{/if} <a href="index.php?page=BusinessLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}">{$link->subject}</a></h4>
											</div>
											{if $this->user->getPermission('mod.business.canEnableLinks') || $this->user->getPermission('mod.business.canEditLinks')}
												{assign var="linkID" value=$link->linkID}
												{include file='businessModeratorOptions' sandbox=false}
											{/if}
										
										</div>
										<div class="messageBody">
											{@$link->shortDescription}
										</div>
										<div class="formElement">
											<p class="formFieldLabel">{lang}wcf.business.link.authorInfo{/lang}</p>
											<p class="formField">{if $link->userID}{if $link->getAuthor()->isOnline()}						<img src="{icon}onlineS.png{/icon}" alt="" title="{lang username=$link->getAuthor()->username}wcf.user.online{/lang}" />{else}<img src="{icon}offlineS.png{/icon}" alt="" title="{lang username=$link->getAuthor()->username}wcf.user.offline{/lang}" />{/if} <a href="index.php?page=User&amp;userID={$link->getAuthor()->userID}{@SID_ARG_2ND}">{/if}{$link->username}{if $link->userID}</a>{/if}</p>
										</div>
										<div class="formElement">
											<p class="formFieldLabel">{lang}wcf.business.category.sortBy.lastChangeTime{/lang}</p>
											<p class="formField">{@$link->lastChangeTime|time}</p>
										</div>
										<div class="formElement">
											<p class="formFieldLabel">{lang}wcf.business.link.hits{/lang}</p>
											<p class="formField">{@$link->hits}</p>
										</div>
									
									</div>
									<div class="messageFooter">
										<div class="smallButtons">
											<ul>
												<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden"> {lang}wcf.global.scrollUp{/lang}</span></a></li>

												{if $category->getPermission('canSeeComments')}
													<li><a href="index.php?page=BusinessCommentsList&amp;linkID={$link->linkID}{@SID_ARG_2ND}" title="{lang}wcf.business.comment.comments{/lang}"><img src="{icon}businessCommentsListS.png{/icon}" alt="" /> <span>{lang}wcf.business.comment.comments{/lang}</span> <span class="smallFont light">({#$link->comments})</span></a></li>
												{/if}

												{if $link->isEditable()}<li><a href="index.php?form=BusinessLinkEdit&amp;linkID={@$link->linkID}{@SID_ARG_2ND}" title="{lang}wcf.business.link.linkEdit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}
												{if $category->getPermission('canVisitLink')}
													<li><a href="index.php?page=BusinessLinkVisit&amp;linkID={@$link->linkID}{@SID_ARG_2ND}" title="{lang}wcf.business.visit.pageTitle{/lang}"><img src="{icon}nextS.png{/icon}" alt="" /> <span>{lang}wcf.business.visit.pageTitle{/lang}</span> <span class="smallFont light">({#$link->hits})</span></a></li>
												{/if}
												{if $additionalSmallButtons|isset}{@$additionalSmallButtons}{/if}
											</ul>
										</div>
									</div>
								</div>
							</div>
		
				{/foreach}
			</div>
		{/if}
			
			<div class="contentFooter">
				{@$pagesOutput}
	
				<div class="largeButtons">
					<ul>
						{if $category->getPermission('canAddLink') && $items != 0}
							<li><a href="index.php?form=BusinessLinkAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wcf.business.link.add{/lang}"><img src="{icon}businessLinkAddM.png{/icon}" alt="" /> <span>{lang}wcf.business.link.add{/lang}</span></a></li>
						{/if}
						{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
					</ul>
				</div>
			</div>
			
			{if $links|count}
				{cycle values='container-1,container-2' print=false advance=false}
				<div class="border infoBox">
					<div class="{cycle values='container-1,container-2'}">
						<div class="containerIcon">
							<img src="{icon}sortM.png{/icon}" alt="" />
						</div>

						<div class="containerContent">
							<h3>{lang}wcf.business.category.sort{/lang}</h3>
							<form method="get" action="index.php">
								<div class="floatContainer">
									<input type="hidden" name="page" value="BusinessCategory" />
									<input type="hidden" name="categoryID" value="{@$categoryID}" />
									<input type="hidden" name="pageNo" value="{@$pageNo}" />
									{if $tagID}<input type="hidden" name="tagID" value="{@$tagID}" />{/if}
														
								<div class="floatedElement">
									<label for="sortField">{lang}wcf.business.category.sort.description{/lang}</label>
									<select name="sortField" id="sortField">
										<option value="subject"{if $sortField == 'subject'} selected="selected"{/if}>{lang}wcf.business.category.sortBy.subject{/lang}</option>
										<option value="lastChange"{if $sortField == 'lastChangeTime'} selected="selected"{/if}>{lang}wcf.business.category.sortBy.lastChangeTime{/lang}</option>
										<option value="hits"{if $sortField == 'hits'} selected="selected"{/if}>{lang}wcf.business.category.sortBy.hits{/lang}</option>
										<option value="rating"{if $sortField == 'rating'} selected="selected"{/if}>{lang}wcf.business.category.sortBy.rating{/lang}</option>
										<option value="comments"{if $sortField == 'comments'} selected="selected"{/if}>{lang}wcf.business.category.sortBy.comments{/lang}</option>
										<option value="languageID"{if $sortField == 'languageID'} selected="selected"{/if}>{lang}wcf.business.category.sortBy.language{/lang}</option>
										<option value="time"{if $sortField == 'time'} selected="selected"{/if}>{lang}wcf.business.category.sortBy.time{/lang}</option>
										{if $additionalSortFields|isset}{@$additionalSortFields}{/if}
									</select>

									<select name="sortOrder" id="sortOrder">
										<option value="ASC"{if $sortOrder == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
										<option value="DESC"{if $sortOrder == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
									</select>
								</div>
					
								<div class="floatedElement">
									<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
								</div>

								{@SID_INPUT_TAG}
								</div>
							</form>
					
						</div>
					</div>
					{if $availableTags|count > 0}
						<div class="{cycle values='container-1,container-2'}">
							<div class="containerIcon">
								<img src="{icon}tagM.png{/icon}" alt="" />
							</div>
							<div class="containerContent">
								<h3>
									<span>{lang}wcf.tagging.filter{/lang}</span>
								</h3>
								<ul class="tagCloud">
									{foreach from=$availableTags item=tag}
										<li>
											<a href="index.php?page=BusinessCategory&amp;categoryID={@$categoryID}&amp;tagID={@$tag->getID()}{@SID_ARG_2ND}" style="font-size: {@$tag->getSize()}%">{$tag->getName()}</a>
										</li>
									{/foreach}
								</ul>						
							</div>
						</div>
					{/if}
					{if $additionalBoxes|isset}{@$additionalBoxes}{/if}
				</div>
			{/if}
	</div>
{include file='footer' sandbox=false}
</body>
</html>
