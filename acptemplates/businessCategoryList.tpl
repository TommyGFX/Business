{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function init() {
		{if $categories|count > 0 && $categories|count < 100 && $this->user->getPermission('admin.business.canEditCategory')}
			new ItemListEditor('categoryList', { itemTitleEdit: true, itemTitleEditURL: 'index.php?action=BusinessCategoryRename&categoryID=', tree: true, treeTag: 'ol' });
		{/if}
	}
	
	// when the dom is fully loaded, execute these scripts
	document.observe("dom:loaded", init);
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/businessCategoriesL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.business.acp.list.pageTitle{/lang}</h2>
	</div>
</div>

{if $successfulSorting}
	<p class="success">{lang}wcf.business.acp.list.successfulSorting{/lang}</p>	
{/if}

{if $update}
	<p class="success">{lang}wcf.business.acp.list.update{/lang}</p>	
{/if}


<script type="text/javascript">
	//<![CDATA[
		initList('businessInformations', 0);
	//]]>
</script>

{if $this->user->getPermission('admin.business.canAddCategory')}
<div class="contentHeader">
	<div class="largeButtons">
			<ul>			
				<li><a href="index.php?form=BusinessCategoryAdd{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/businessCategoryAddM.png" alt="" /> <span>{lang}wcf.business.acp.add.pageTitle{/lang}</span></a></li>
			</ul>
	</div>
</div>
{/if}

{if $categories|count > 0}
	{if $this->user->getPermission('admin.business.canEditCategory')}
	<form method="post" action="index.php?action=BusinessCategorySort">
	{/if}
	<div class="border content">
		<div class="container-1">
			<ol class="itemList" id="categoryList">
				{foreach from=$categories item=child}
					{assign var='category' value=$child.category}
						
					<li id="item_{@$category->categoryID}" class="deletable">
						<div class="buttons">
							{if $this->user->getPermission('admin.business.canEditCategory')}
								<a href="index.php?form=BusinessCategoryEdit&amp;categoryID={@$category->categoryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wcf.business.acp.edit.pageTitle{/lang}" /></a>
							{else}
								<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}wcf.business.acp.edit.pageTitle{/lang}" />
							{/if}						
							{if $this->user->getPermission('admin.business.canEditCategory')}
								<a href="index.php?action=BusinessCategoryDelete&amp;categoryID={@$category->categoryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.business.acp.list.delete{/lang}" class="deleteButton"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" longdesc="{lang}wcf.business.acp.list.deleteSure{/lang}" /></a>
							{else}
								<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wcf.business.acp.list.delete{/lang}" />
							{/if}
								
							{if $child.additionalButtons|isset}{@$child.additionalButtons}{/if}
						</div>
							
						<h3 class="itemListTitle">
							<img src="{@RELATIVE_WCF_DIR}icon/{@$category->getIconName()}M.png" alt="" />
							
							{if $this->user->getPermission('admin.business.canEditCategory')}
								<select name="categoryListPositions[{@$category->categoryID}][{@$category->parentID}]">
									{section name='positions' loop=$child.maxPosition}
										<option value="{@$positions+1}"{if $positions+1 == $child.position} selected="selected"{/if}>{@$positions+1}</option>
									{/section}
								</select>
							{/if}
								
							ID-{@$category->categoryID} <a href="index.php?form=BusinessCategoryEdit&amp;categoryID={@$category->categoryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" class="title">{$category->title}</a>
						</h3>
						
						{if $child.hasChildren}<ol id="parentItem_{@$category->categoryID}">{else}<ol id="parentItem_{@$category->categoryID}"></ol></li>{/if}
						{if $child.openParents > 0}{@"</ol></li>"|str_repeat:$child.openParents}{/if}
					{/foreach}
				</ol>
			</div>
		</div>
	{if $this->user->getPermission('admin.business.canEditCategory')}
		<div class="formSubmit">
			<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
			<input type="reset" accesskey="r" id="reset" value="{lang}wcf.global.button.reset{/lang}" />
			<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
	 		{@SID_INPUT_TAG}
	 	</div>
	</form>
	{/if}
{else}
	<div class="border content">
		<div class="container-1">
			<p>{lang}wcf.business.acp.list.noCategories{/lang}</p>
		</div>
	</div>
{/if}

{if $this->user->getPermission('admin.business.canAddCategory') && categories|count}
<div class="contentFooter">
	<div class="largeButtons">
			<ul>			
				<li><a href="index.php?form=BusinessCategoryAdd{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/businessCategoryAddM.png" alt="" /> <span>{lang}wcf.business.acp.add.pageTitle{/lang}</span></a></li>
			</ul>
	</div>
</div>
{/if}
	
{include file='footer'}
