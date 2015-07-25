{* purpose of this template: header for user area *}
{pageaddvar name='javascript' value='prototype'}
{pageaddvar name='javascript' value='validation'}
{pageaddvar name='javascript' value='zikula'}
{pageaddvar name='javascript' value='livepipe'}
{pageaddvar name='javascript' value='zikula.ui'}
{pageaddvar name='javascript' value='zikula.imageviewer'}
{pageaddvar name='javascript' value='modules/MUImage/javascript/MUImage.js'}
{modgetvar module='MUImage' name='layout' assign='layout'}
{if $layout eq 'normal'}
	{pageaddvar name='stylesheet' value='modules/MUImage/style/bootstrap_boxsizing.css'}
{else}
	{if $layout eq 'bootstrap'}
		{pageaddvar name='stylesheet' value='modules/MUImage/style/bootstrap.css'}
		{userloggedin assign="loggedin"}
		{if $loggedin eq true}		
			<link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
			<link rel="stylesheet" href="modules/MUImage/Vendor/Bootstrap-Image-Gallery/css/bootstrap-image-gallery.min.css">	
	{/if}		
{/if}
{* initialise additional gettext domain for translations within javascript *}
{pageaddvar name='jsgettext' value='module_muimage_js:MUImage'}

{if !isset($smarty.get.theme) || $smarty.get.theme ne 'Printer'}
	<h2>{modgetinfo info='displayname'}{if $templateTitle}: {$templateTitle}{/if}</h2>
	{userloggedin assign="loggedin"}
	{if $loggedin eq true}
	<div id="muimage-bootstrap-navbar-default">
		<nav class="navbar navbar-default">
  			<div class="container-fluid">
    		<!-- Titel und Schalter werden für eine bessere mobile Ansicht zusammengefasst -->
    		<div class="navbar-header">
      			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        			<span class="sr-only">Navigation ein-/ausblenden</span>
        			<span class="icon-bar"></span>
        			<span class="icon-bar"></span>
        			<span class="icon-bar"></span>
      			</button>
   			 </div>

    		<!-- Alle Navigationslinks, Formulare und anderer Inhalt werden hier zusammengefasst und können dann ein- und ausgeblendet werden -->
    		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      			<ul class="nav navbar-nav">
      				{checkpermissionblock component='MUImage::' instance='.*' level='ACCESS_ADMIN'}
        				<li><a href="{modurl modname='MUImage' type='admin' func='main'}"><i class="fa fa-wrench fa-fw"></i> {gt text='Backend'}<span class="sr-only">(aktuell)</span></a></li>
      				{/checkpermissionblock}
       			 	<li><a href="{modurl modname='MUImage' type='user' func='main'}"><i class="fa fa-folder fa-fw"></i> {gt text='Albums'}</a></li>
      				{checkpermissionblock component='MUImage::' instance='.*' level='ACCESS_ADD'}
        				<li><a href="{modurl modname='MUImage' type='user' func='edit' ot='album'}"><i class="fa fa-pencil-square-o fa-fw"></i> {gt text='Create Album'}</a></li>
      				{/checkpermissionblock}
      				{checkpermissionblock component='MUImage::' instance='.*' level='ACCESS_ADD'}
      				{if $func eq 'display' && $modvars.MUImage.supportSubAlbums eq true}
      					{formutil_getpassedvalue name="id" assign="albumid"}
        				<li><a href="{modurl modname='MUImage' type='user' func='edit' ot='album' parent=$albumid returnTo='userDisplayAlbum'}"><i class="fa fa-pencil fa-fw"></i> {gt text='Create SubAlbum'}</a></li>
      				{/if}
      				{/checkpermissionblock}
      			</ul>
    		</div><!-- /.navbar-collapse -->
  		</div><!-- /.container-fluid -->
	</nav>
	</div>
    {/if}
{/if}

{insert name='getstatusmsg'}
