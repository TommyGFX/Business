	<div id="profileContent" class="tabMenu">
		<ul>
			{foreach from=$link->getBusinessMenu()->getMenuItems('') item=item}
				<li{if $item.menuItem|in_array:$link->getBusinessMenu()->getActiveMenuItems()} class="activeTabMenu"{/if}><a href="{$item.menuItemLink}">{if $item.menuItemIcon}<img src="{$item.menuItemIcon}" alt="" /> {/if}<span>{lang}{@$item.menuItem}{/lang}</span></a></li>
			{/foreach}
		</ul>
	</div>

	<div class="subTabMenu">
		<div class="containerHead">
			{assign var=activeMenuItem value=$link->getBusinessMenu()->getActiveMenuItem()}
			{if $activeMenuItem && $link->getBusinessMenu()->getMenuItems($activeMenuItem)|count > 1}
				<ul>
					{foreach from=$link->getBusinessMenu()->getMenuItems($activeMenuItem) item=item}
						<li{if $item.menuItem|in_array:$link->getBusinessMenu()->getActiveMenuItems()} class="activeSubTabMenu"{/if}><a href="{$item.menuItemLink}"><span>{lang}{@$item.menuItem}{/lang}</span></a></li>
					{/foreach}
				</ul>
			{else}
				<div> </div>
			{/if}
		</div>
	</div>


