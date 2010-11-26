{if $tags|count}
	<div class="border titleBarPanel">
		<div class="containerHead">
			<h3>{lang}wcf.tagging.tags.used{/lang}</h3>
		</div>
		<div class="container-1 content">
			{include file='tagCloud'}
		</div>
	</div>
{/if}