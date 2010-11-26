<div class="hidden" id="moderatorOptions{@$linkID}Menu">
	<div class="pageMenu">
		<ul>
			<li><a href="index.php?page=BusinessLink&amp;linkID={@$linkID}{@SID_ARG_2ND}#statusEdit">{lang}wcf.business.link.statuEdit{/lang}</a></li>
			<li><a href="index.php?form=BusinessLinkEdit&amp;linkID={@$linkID}{@SID_ARG_2ND}">{lang}wcf.business.link.linkEdit{/lang}</a></li>
			{if $this->user->getPermission('mod.business.canDeleteLinks')}
				<li><a href="index.php?action=BusinessLinkDelete&amp;linkID={@$linkID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm('{lang}wcf.business.link.delete.sure{/lang}')" title="{lang}wcf.business.link.delete{/lang}">{lang}wcf.business.link.delete{/lang}</a></li>
			{/if}
			<li><a href="index.php?form=BusinessLinkMove&amp;linkID={@$linkID}{@SID_ARG_2ND}" title="{lang}wcf.business.link.move.pageTitle{/lang}"><span>{lang}wcf.business.link.move.pageTitle{/lang}</span></span></a></li>
			{if MODULE_USER_INFRACTION == 1 && $this->user->getPermission('admin.user.infraction.canWarnUser') && $link->userID}
				<li><a href="index.php?form=UserWarn&amp;userID={@$link->userID}&amp;objectType=businessLink&amp;objectID={@$link->linkID}{@SID_ARG_2ND}" title="{lang}wcf.user.infraction.button.warn{/lang}"> {lang}wcf.user.infraction.button.warn{/lang}</a></li>
			{/if}
			{if $link->ratingDisabled}
				<li><a href="index.php?action=BusinessLinkRatingEnable&amp;linkID={@$linkID}&amp;isDisabled=0{@SID_ARG_2ND}"><span>{lang}wcf.business.link.ratingEnable{/lang}</span></span></a></li>
			{else}
				<li><a href="index.php?action=BusinessLinkRatingEnable&amp;linkID={@$linkID}&amp;isDisabled=1{@SID_ARG_2ND}">{lang}wcf.business.link.ratingDisable{/lang}</a></li>
			{/if}
			{if $link->isClosed}
				<li><a href="index.php?action=BusinessLinkClosed&amp;linkID={@$linkID}&amp;isClosed=0{@SID_ARG_2ND}">{lang}wcf.business.link.open{/lang}</a></li>
			{else}
				<li><a href="index.php?action=BusinessLinkClosed&amp;linkID={@$linkID}&amp;isClosed=1{@SID_ARG_2ND}">{lang}wcf.business.link.close{/lang}</a></li>
			{/if}
			{if $link->isSticky}
				<li><a href="index.php?action=BusinessLinkSticky&amp;linkID={@$linkID}&amp;isSticky=0{@SID_ARG_2ND}">{lang}wcf.business.link.resticky{/lang}</a></li>
			{else}
				<li><a href="index.php?action=BusinessLinkSticky&amp;linkID={@$linkID}&amp;isSticky=1{@SID_ARG_2ND}">{lang}wcf.business.link.sticky{/lang}</a></li>
			{/if}
		</ul>
	</div>
</div>
	<script type="text/javascript">
		//<![CDATA[
		popupMenuList.register('moderatorOptions{@$linkID}');
		//]]>
	</script>
