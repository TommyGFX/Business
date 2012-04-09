{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/businessCategoryAddL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.business.acp.{@$action}.pageTitle{/lang}</h2>
	</div>
</div>

{if $success|isset}
	<p class="success">{lang}wcf.business.acp.{@$action}.success{/lang}</p>	
{/if}

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

<div class="contentHeader">
	<div class="largeButtons">
		<ul><li><a href="index.php?page=BusinessCategoryList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.business.acp.list.pageTitle{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/businessCategoriesM.png" alt="" /> <span>{lang}wcf.business.acp.list.pageTitle{/lang}</span></a></li></ul>
	</div>
</div>

<form method="post" action="{if $action == 'add'}index.php?form=BusinessCategoryAdd{else}index.php?form=BusinessCategoryEdit&amp;categoryID={@$categoryID}{/if}" id="categoryAddForm">

	{if $categoryID|isset && $categoryQuickJumpOptions|count > 1}
		<fieldset>
			<legend>{lang}wcf.business.acp.edit.pageTitle{/lang}</legend>
			<div class="formElement">
				<div class="formFieldLabel">
					<label for="categoryChange">{lang}wcf.business.acp.edit.pageTitle{/lang}</label>
				</div>
				<div class="formField">
					<select id="categoryChange" onchange="document.location.href=fixURL('index.php?form=BusinessCategoryEdit&amp;categoryID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
						{htmloptions options=$categoryQuickJumpOptions selected=$categoryID disableEncoding=true}
					</select>
				</div>
			</div>
		</fieldset>
	{/if}
			
	<div class="border tabMenuContent" id="data-content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}wcf.business.acp.add.data{/lang}</h3>
			<fieldset>
				<legend>{lang}wcf.acp.group.data{/lang}</legend>
					
				<div class="formElement{if $errorField == 'title'} formError{/if}">
					<div class="formFieldLabel">
						<label for="title">{lang}wcf.business.acp.add.title{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="title" name="title" value="{$title}" />
						{if $errorField == 'title'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>
			
				<div id="descriptionDiv" class="formElement">
					<div class="formFieldLabel">
						<label for="description">{lang}wcf.business.acp.add.description{/lang}</label>
					</div>
					<div class="formField">
						<textarea id="description" name="description" cols="40" rows="10">{$description}</textarea>
						<label><input type="checkbox" name="allowDescriptionHtml" value="1" {if $allowDescriptionHtml}checked="checked" {/if}/> {lang}wcf.business.acp.add.allowDescriptionHtml{/lang}</label>
					</div>
				</div>
				
				<div class="formElement" id="imageDiv">
					<div class="formFieldLabel">
						<label for="image">{lang}wcf.business.acp.add.image{/lang}</label>
					</div>
					<div class="formField">	
						<input type="text" class="inputText" id="image" name="image" value="{$image}" />
					</div>
					<div class="formFieldDesc hidden" id="imageHelpMessage">
						<p>{lang}wcf.business.acp.add.image.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">
					//<![CDATA[
					inlineHelp.register('image');
					//]]>
				</script>
					
				{if $additionalGeneralFields|isset}{@$additionalGeneralFields}{/if}
			</fieldset>

			<fieldset>
				<legend>{lang}wcf.business.acp.add.moreDatas{/lang}</legend>
				{if $categorySelect|count > 0}			
					<div class="formElement{if $errorField == 'parentID'} formError{/if}" id="parentIDDiv">
						<div class="formFieldLabel">
							<label for="parentID">{lang}wcf.business.acp.add.parentCategory{/lang}</label>
						</div>
						<div class="formField">
							<select name="parentID" id="parentID">
								<option value="0"></option>
								{htmlOptions options=$categorySelect disableEncoding=true selected=$parentID}
							</select>
							{if $errorField == 'parentID'}
								<p class="innerError">
									{if $errorType == 'invalid'}{lang}wcf.business.acp.add.parentCategory.invalid{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc hidden" id="parentIDHelpMessage">
							<p>{lang}wcf.business.acp.add.parentCategory.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('parentID');
					//]]>
					</script>
				{/if}
					
				<div class="formElement{if $errorField == 'position'} formError{/if}" id="positionDiv">
					<div class="formFieldLabel">
						<label for="position">{lang}wcf.business.acp.add.position{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="position" name="position" value="{@$position}" />
						{if $errorField == 'position'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="positionHelpMessage">
						<p>{lang}wcf.business.acp.add.position.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('position');
				//]]>
				</script>
			
				{if $additionalPositionFields|isset}{@$additionalPositionFields}{/if}
			</fieldset>
		</div>
	</div>
			
	

	{if $additionalFields|isset}{@$additionalFields}{/if}

	<div class="formSubmit">
		<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		{@SID_INPUT_TAG}
	</div>
</form>

{include file='footer'}
