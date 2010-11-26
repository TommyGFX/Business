<div class="contentBox">
	<h3 class="subHeadline"><a href="index.php?page=Business{@SID_ARG_2ND}">{lang}wcf.business.links{/lang}</a> <span>({#$linkItems})</span></h3>
		
	<ul class="dataList">
		{foreach from=$links item=link}
			<li class="{cycle values='container-1,container-2'}">
				<div class="containerIcon">
					<img src="{icon}businessLinkM.png{/icon}" alt="" style="width: 24px; height: 24px" />
				</div>
				<div class="containerContent">
					<h4>{if $this->user->getPermission('mod.business.canEnableLinks')}<span class="prefix"><strong>[{lang}wcf.business.link.status{$link->status}{/lang}]</strong></span>{/if} <a href="index.php?page=BusinessLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}">{@$link->subject}</a></h4>
					<p class="firstPost smallFont light">({@$link->time|time})</p>
				</div>
			</li>
		{/foreach}
	</ul>
	
	<div class="buttonBar">
		<div class="smallButtons">
			<ul>
				<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
			</ul>
		</div>
	</div>
</div>
