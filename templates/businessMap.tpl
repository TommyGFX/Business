<div id="locationMap" class="hidden">
	<div class="border">
		<div class="containerHead"> 
			<h3>{lang}wcf.business.link.ort{/lang}</h3> 
		</div> 
		<div class="container-1" id="locationMapCanvas" style="height: 250px; overflow:hidden;"></div>
	</div>
</div>

{include file='gmapSimpleRoute' id='locationMap' location=$businessMapLocation switchable=false}

<script type="text/javascript">
	//<![CDATA[
	GMAP_MAP_CONTROL = 'small';
	{if GMAP_MAPTYPE_CONTROL != 'off'}
		GMAP_MAPTYPE_CONTROL = 'dropdown';
	{/if}
	GMAP_ENABLE_OVERVIEW_MAP_CONTROL = 0;
	GMAP_ENABLE_SCALE_CONTROL = 0;
	GMAP_ZOOM = 8;
	//]]>
</script>
