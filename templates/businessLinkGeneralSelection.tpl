{if $generalSelection|count > 0}
	<div class="border titleBarPanel">
		<div class="containerHead"> 
			<h3>{lang}wcf.business.link.generalInformations{/lang}</h3> 
		</div> 
		<div class="pageMenu"> 
			<ul class="twoRows">
				{foreach from=$generalSelection item=selection}
					<li class="conatiner-2{if $selection.active} active{/if}">
						<a{if $selection.url} href="{@$selection.url}"{/if}>{if $selection.icon}<img src="{@$selection.icon}" alt="" /> {/if}<label class="smallFont">{@$selection.title}</label> <span>{@$selection.value}</span>{if $selection.url}</a>{/if}
					</li>
				{/foreach}
			</ul>
		</div> 
	</div>
{/if}
