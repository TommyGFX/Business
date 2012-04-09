<div class="contentBox">
	<h3 class="subHeadline">{lang}wcf.tagging.taggable.de.wcf.tommygfx.business{/lang} <span>({#$items})</span></h3>

	<ul class="dataList">
		{foreach from=$taggedObjects item=link}
			<li class="{cycle values='container-1,container-2'}">
				<div class="containerIcon">
					<img src="{icon}businessLinkM.png{/icon}" alt="" style="width: 24px;" />
				</div>
				
				<div class="containerContent">
					<h4><a href="index.php?page=BusinessLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}">{$link->subject}</a></h4>
					<p class="firstPost smallFont light">{lang}wcf.business.link.by{/lang} <a href="index.php?page=User&amp;userID={@$link->userID}{@SID_ARG_2ND}">{$link->username}</a> ({@$link->time|time})</p>
				</div>
			</li>
		{/foreach}
	</ul>
</div>

<div class="buttonBar">
	<div class="smallButtons">
		<ul>
			<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
		</ul>
	</div>
</div>
