<div id="userCard" class="border">
	<div class="userCardInner container-1">
		{assign var="author" value=$link->getAuthor()}
		{assign var="userData" value=$link->getUserOptions()}
		{if $author->userID}
		<ul class="userCardList">
			<li id="userCardAvatar" style="width: 149px">
				{if $author->getAvatar()}
					<div class="userAvatar">
						<a href="index.php?page=User&amp;userID={@$author->userID}{@SID_ARG_2ND}" title="{lang username=$author->username}wcf.user.viewProfile{/lang}"><img src="{$author->getAvatar()->getURL()}" alt="" /></a>
					</div>
				{else}
					<div class="userAvatar">
						<a href="index.php?page=User&amp;userID={@$author->userID}{@SID_ARG_2ND}" title="{lang username=$author->username}wcf.user.viewProfile{/lang}"><img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" /></a>
					</div>
				{/if}
			</li>			
			<li id="userCardCredits" style="margin-left: 150px">
				<div class="userCardCreditsInner">
					<div class="userPersonals">
						<p class="userName">
							{if MESSAGE_SIDEBAR_ENABLE_ONLINE_STATUS}
								{if $author->isOnline()}
									<img src="{icon}onlineS.png{/icon}" alt="" title="{lang username=$author->username}wcf.user.online{/lang}" />
								{else}
									<img src="{icon}offlineS.png{/icon}" alt="" title="{lang username=$author->username}wcf.user.offline{/lang}" />		
								{/if}
							{/if}
			
							<a href="index.php?page=User&amp;userID={@$author->userID}{@SID_ARG_2ND}" title="{lang username=$author->username}wcf.user.viewProfile{/lang}"> <span>{@$author->username}</span></a>			
						</p>						
						{if MODULE_USER_RANK && MESSAGE_SIDEBAR_ENABLE_RANK}
							{if $author->getUserTitle()}
								<p class="userTitle smallFont">{@$author->getUserTitle()}</p>
							{/if}
							{if $author->getRank() && $author->getRank()->rankImage}
								<p class="userRank">{@$author->getRank()->getImage()}</p>
							{/if}
						{/if}																																	
					</div>
					{capture assign=userContacts}
						{if $this->user->userID}
							<li><a href="index.php?form=PMNew&amp;userID={@$author->userID}{@SID_ARG_2ND}"><img src="{icon}pmM.png{/icon}" alt="" title="{lang}wcf.pm.profile.sendPM{/lang}" /> <span>{lang}wcf.pm.profile.sendPM{/lang}</span></a></li>
						{/if}
							<li><a href="index.php?page=Register{@SID_ARG_2ND}" title="{lang}wcf.user.register.invitation{/lang}"><img src="{icon}registerM.png{/icon}" alt="" /> <span>{lang}wcf.user.register.invitation{/lang}</span></a></li>
					{/capture}
					{if $userContacts|trim}
					<div class="smallButtons userCardOptions">
						<ul>
							{@$userContacts}
						</ul>
					</div>
					{/if}
					</div>				
					<div class="friendsNone">
						<h3 class="light"></h3>
					</div>
				<!--[if IE]>
					<hr class="hidden" style="display: block; clear: both;" />
				<![endif]-->
			</li>
		</ul>
		{else}
		<ul class="userCardList">
			<li id="userCardAvatar" style="width: 149px">
			<div class="userAvatar">
				<img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt=""
					style="width: 100px; height: 100px;" />
			</div>
			</li>
			<li id="userCardCredits" style="margin-left: 150px">
				<div class="userCardCreditsInner">
					<div class="userPersonals">
						<p class="userName">{$link->username}</p>		
						<p class="userTitle smallFont">{lang}wcf.user.guest{/lang}</p>																													
					</div>

					<div class="smallButtons userCardOptions">
						<ul>
							<li><a href="index.php?page=Register{@SID_ARG_2ND}" title="{lang}wcf.user.register.invitation{/lang}"><img src="{icon}registerM.png{/icon}" alt="" /> <span>{lang}wcf.user.register.invitation{/lang}</span></a></li>
						</ul>
					</div>
				</div>				
				<div class="friendsNone">
					<h3 class="light"></h3>
				</div>
				<!--[if IE]>
					<hr class="hidden" style="display: block; clear: both;" />
				<![endif]-->
			</li>
		</ul>
		{/if}
	</div>
</div>
