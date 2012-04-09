{include file="documentHeader"}
<head>
	<title>{lang}wcf.business.moderation.pageTitle{/lang} - Links - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH}; 
		//]]>
	</script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ImageResizer.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>
	<script type="text/javascript">
	//<![CDATA[
	var tabMenu = new TabMenu();
	onloadEvents.push(function() { tabMenu.showSubTabMenu('reportedLinks'); });
	//]]>
	</script>
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
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}businessModerationL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wcf.business.moderation.pageTitle{/lang}</h2>
			<p>{lang}wcf.business.moderation.pageTitle.description{/lang}</p>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	<div class="tabMenu">
		<ul>
			<li id="reportedLinks"><a onclick="tabMenu.showSubTabMenu('reportedLinks');"> <span>{lang}wcf.business.moderation.reportedLinks{/lang}</span></a></li>
			<li id="disabledLinks"><a onclick="tabMenu.showSubTabMenu('disabledLinks');"> <span>{lang}wcf.business.moderation.disabledLinks{/lang}</span></a></li>
			{if $additionalTabs|isset}{@$additionalTabs}{/if}
		</ul>
	</div>
	<div class="subTabMenu">
		<div class="containerHead">
			<div></div>
		</div>
	</div>
			
	<div class="border tabMenuContent hidden" id="reportedLinks-content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}wcf.business.moderation.reportedLinks{/lang}</h3>
			{if $reportedLinks|count}
				{cycle name='container' values='1,2' print=false advance=false}
				{foreach from=$reportedLinks item=reportLink}
					<div id="postRow{@$reportLink.linkID}" class="deletable message content">
						<a id="report{@$reportLink.reportID}"></a>
						<div class="messageInner container-{cycle name='container'}">
							<div class="messageHeader">
								<p class="messageCount">
									<a href="index.php?page=BusinessLink&amp;linkID={@$reportLink.linkID}{@SID_ARG_2ND}" class="messageNumber">{#$startIndex}</a>
								</p>
								<div class="containerIcon">
									<img src="{icon}{@$reportLink.link->getIconName()}M.png{/icon}" alt="" />
								</div>
								<div class="containerContent">
									<p class="smallFont light">{@$reportLink.link->time|time}</p>
									<p class="smallFont light">{lang}wcf.business.link.by{/lang} {if $reportLink.link->userID}<a href="index.php?page=User&amp;userID={@$reportLink.link->userID}{@SID_ARG_2ND}">{$reportLink.link->username}</a>{else}{$reportLink.link->username}{/if}</p>
								</div>
							</div>
							
							<h4 class="messageHeading"><a href="index.php?page=BusinessLink&amp;linkID={@$reportLink.linkID}{@SID_ARG_2ND}"><span>{$reportLink.link->subject}</span></a></h4>
							
							<div class="messageBody">
								<div id="postText{@$reportLink.linkID}">
									{@$reportLink.link->shortDescription}
								</div>
							</div>
							
							<p class="editNote">{lang}wcf.business.moderation.reportedBy{/lang} {if $reportLink.reportUserID}<a href="index.php?page=User&amp;userID={@$reportLink.reportUserID}{@SID_ARG_2ND}">{$reportLink.reportUsername}</a>{else}{$reportLink.reportUsername}{/if} ({@$reportLink.reportTime|time})</p>
							<p>{$reportLink.report}</p>
							<div class="messageFooter">
								<div class="smallButtons">
									<ul>
										<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" title="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
										<li><a href="index.php?action=BusinessReportDelete&amp;reportID={@$reportLink.reportID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" class="deleteButton" title="{lang}wcf.business.moderation.report.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" longdesc="{lang}wcf.business.moderation.report.delete.sure{/lang}" /> <span>{lang}wcf.business.moderation.report.delete{/lang}</span></a></li>
									</ul>
								</div>
							</div>
						<hr />
					</div>
				</div>
				{assign var="startIndex" value=$startIndex + 1}
				{/foreach}
				
			{else}
				{lang}wcf.business.moderation.noReports{/lang}
			{/if}
		</div>
	</div>

	<div class="border tabMenuContent hidden" id="disabledLinks-content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}wcf.business.moderation.disabledLinks{/lang}</h3>
			{if $disabledLinks|count}
				{cycle name='container' values='1,2' print=false advance=false}
				{foreach from=$disabledLinks item=disabledLink}
					<div id="postRow{@$disabledLink->linkID}" class="message content">
						<div class="messageInner container-{cycle name='container'}">
							<div class="messageHeader">
								<div class="containerIcon">
									<img src="{icon}{@$disabledLink->getIconName()}M.png{/icon}" alt="" />
									</div>
								<div class="containerContent">
									<p class="smallFont light">{@$disabledLink->time|time}</p>
									<p class="smallFont light">{lang}wcf.business.link.by{/lang} {if $disabledLink->userID}<a href="index.php?page=User&amp;userID={@$disabledLink->getAuthor()->userID}{@SID_ARG_2ND}">{$disabledLink->getAuthor()->username}</a>{else}{$disabledLink->username}{/if}</p>
								</div>
							</div>
							
							<h4 class="messageHeading"><a href="index.php?page=BusinessLink&amp;linkID={@$disabledLink->linkID}{@SID_ARG_2ND}"><span>{$disabledLink->subject}</span></a></h4>
							
							<div class="messageBody">
								<div id="postText{@$disabledLink->linkID}">
									{@$disabledLink->shortDescription}
								</div>
							</div>
							
							<p class="editNote">{lang}wcf.business.moderation.currentStatus{/lang}: <strong>{lang}wcf.business.link.status{$disabledLink->status}{/lang}</strong></p>
							<p><span class="smallFont">{lang}wcf.business.link.statusComment.short{/lang}</span>: {if $disabledLink->statusComment != ''}{@$disabledLink->statusComment}{else}-{/if}</p>
							<div class="messageFooter">
								<div class="smallButtons">
									<ul>
										<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" title="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
										<li><a href="index.php?page=BusinessLink&amp;linkID={@$disabledLink->linkID}{@SID_ARG_2ND}#statusEdit" title="{lang}wcf.business.link.statuEdit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wcf.business.link.statuEdit{/lang}</span></a></li>
									</ul>
								</div>
							</div>
							<hr />
						</div>
					</div>
				{/foreach}
			{else}
				{lang}wcf.business.moderation.noDisabledLinks{/lang}
			{/if}			
		</div>
	</div>
</div>

{include file='footer' sandbox=false}

</body>
</html>
