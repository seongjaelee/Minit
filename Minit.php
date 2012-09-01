<?php
/**
 * Minit - A minimalistic MediaWiki skin
 *
 * @ingroup Skins
 * @package MediaWiki
 * @download https://github.com/seongjaelee/Minit
 */

if( !defined( 'MEDIAWIKI' ) )
	die( 'This is a skin file for mediawiki and should not be viewed directly.\n' );

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @ingroup Skins
 */
class SkinMinit extends SkinTemplate {

	function initPage( OutputPage $out ) {
		parent::initPage( $out );
		$this->skinname = 'minit';
		$this->stylename = 'minit';
		$this->template = 'MinitTemplate';
		$this->useHeadElement = true;
		
		$out->addModuleScripts( 'skins.minit' );
	}

	function setupSkinUserCss( OutputPage $out ) {
		global $wgHandheldStyle;
		
		parent::setupSkinUserCss( $out );
		
		$out->addStyle( 'minit/main.css', 'screen' );

		if ( $wgHandheldStyle ) {
			$out->addStyle( $wgHandheldStyle, 'handheld' );
		}
		$out->addModuleStyles( 'skins.minit' );
	}
}

/**
 * @todo document
 * @ingroup Skins
 */
class MinitTemplate extends BaseTemplate {

	/**
	 * @var Skin
	 */
	var $skin;

	/**
	 * Template filter callback for MonoBook skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		$this->skin = $this->data['skin'];

		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();

		$this->html( 'headelement' );
?><div id="globalWrapper">
<a id="top"></a>
<div id="firstHeading"><h1><?php $this->html('title') ?></h1></div>
<div id="siteSub"><?php $this->msg('tagline') ?></div>
<div id="contentSub"<?php $this->html('userlangattributes') ?>><?php $this->html('subtitle') ?></div>
<div id="columnContent"><div id="content">
	<?php if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>
	<div id="bodyContent">
<?php if($this->data['undelete']) { ?>
		<div id="contentSub2"><?php $this->html('undelete') ?></div>
<?php } ?><?php if($this->data['newtalk'] ) { ?>
		<div class="usermessage"><?php $this->html('newtalk')  ?></div>
<?php } ?><?php if($this->data['showjumplinks']) { ?>
		<div id="jump-to-nav"><?php $this->msg('jumpto') ?> <a href="#column-one"><?php $this->msg('jumptonavigation') ?></a>, <a href="#searchInput"><?php $this->msg('jumptosearch') ?></a></div>
<?php } ?>
		<!-- start content -->
<?php $this->html('bodytext') ?>
		<?php if($this->data['catlinks']) { $this->html('catlinks'); } ?>
		<!-- end content -->
		<?php if($this->data['dataAfterContent']) { $this->html('dataAfterContent'); } ?>
		<!--<?php if($this->data['printfooter']) { $this->html('printfooter'); } ?>-->
		<div class="visualClear"></div>
	</div>
</div></div>
<div id="columnOne"<?php $this->html('userlangattributes')  ?>>
	<!--
	<div class="portlet" id="p-logo">
		<?php
			$logoAttribs = array() + Linker::tooltipAndAccesskeyAttribs('p-logo');
			$logoAttribs['style'] = "background-image: url({$this->data['logopath']});";
			$logoAttribs['href'] = $this->data['nav_urls']['mainpage']['href'];
			echo Html::element( 'a', $logoAttribs );
		?>

	</div>
	-->
	<script type="<?php $this->text('jsmimetype') ?>"> if (window.isMSIE55) fixalpha(); </script>
<?php
	$this->renderPortals( $this->data['sidebar'] );
?>
</div><!-- end of the left (by default at least) column -->
<div class="visualClear"></div>

<div id="footer"<?php $this->html('userlangattributes') ?>>
<?php foreach( $this->getFooterLinks() as $category => $links ): ?>
	<ul id="footer-<?php echo $category ?>">
	<?php foreach( $links as $link ): ?>
		<li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
	<?php endforeach; ?>
	</ul>
<?php endforeach; ?>
Powered by MediaWiki, Skined by BlueBrown
</div>
<?php
		$this->printTrail();
		echo Html::closeElement( 'body' );
		echo Html::closeElement( 'html' );
		wfRestoreWarnings();
	} // end of execute() method

	/*************************************************************************************************/

	protected function renderPortals( $sidebar ) {
		if ( !isset( $sidebar['SEARCH'] ) ) $sidebar['SEARCH'] = true;
		if ( !isset( $sidebar['TOOLBOX'] ) ) $sidebar['TOOLBOX'] = true;
		if ( !isset( $sidebar['LANGUAGES'] ) ) $sidebar['LANGUAGES'] = true;
		if ( !isset( $sidebar['VIEW'] ) ) $sidebar['VIEW'] = true;
		if ( !isset( $sidebar['PERSONAL'] ) ) $sidebar['PERSONAL'] = true;
		if ( !isset( $sidebar['RC'] ) ) $sidebar['RC'] = true;

		foreach( $sidebar as $boxName => $content ) {
			if ( $content === false )
				continue;

			if ( $boxName == 'SEARCH' ) {
				$this->searchBox();
			} elseif ( $boxName == 'TOOLBOX' ) {
				$this->toolbox();
			} elseif ( $boxName == 'LANGUAGES' ) {
				$this->languageBox();
			} elseif ( $boxName == 'PERSONAL' ) {
				$this->personalBox();
			} elseif ( $boxName == 'VIEW' ) {
				$this->cactions();
			} elseif ( $boxName == 'RC' ) {
				$this->recentChangeBox();
			} else {
				$this->customBox( $boxName, $content );
			}
		}
	}

	function searchBox() {
		global $wgUseTwoButtonsSearchForm;
?>
	<div id="p-search" class="portlet">
		<h2><?php $this->msg('search') ?></h2>
		<div id="searchBody" class="pBody">
			<form action="<?php $this->text('wgScript') ?>" id="searchform">
				<input type='hidden' name="title" value="<?php $this->text('searchtitle') ?>"/>
				<?php echo $this->makeSearchInput(array( "id" => "searchInput" )); ?>

				<?php echo $this->makeSearchButton("go", array( "id" => "searchGoButton", "class" => "searchButton" ));
				if ($wgUseTwoButtonsSearchForm): ?>
				<?php echo $this->makeSearchButton("fulltext", array( "id" => "mw-searchButton", "class" => "searchButton" ));
				else: ?>

				<div><a href="<?php $this->text('searchaction') ?>" rel="search"><?php $this->msg('powersearch-legend') ?></a></div><?php
				endif; ?>

			</form>
		</div>
	</div>
<?php
	}

	/**
	 * Prints the cactions bar.
	 * Shared between MonoBook and Modern
	 */
	function cactions() {
?>
	<div id="p-cactions" class="portlet">
		<h2><?php $this->msg('views') ?></h2>
		<div class="pBody">
			<ul><?php
				foreach($this->data['content_actions'] as $key => $tab) {
					$linkAttribs = array( 'href' => $tab['href'] );

				 	if( isset( $tab["tooltiponly"] ) && $tab["tooltiponly"] ) {
						$title = Linker::titleAttrib( "ca-$key" );
						if ( $title !== false ) {
							$linkAttribs['title'] = $title;
						}
				 	} else {
						$linkAttribs += Linker::tooltipAndAccesskeyAttribs( "ca-$key" );
				 	}
				 	$linkHtml = Html::element( 'a', $linkAttribs, $tab['text'] );

				 	/* Surround with a <li> */
				 	$liAttribs = array( 'id' => Sanitizer::escapeId( "ca-$key" ) );
					if( $tab['class'] ) {
						$liAttribs['class'] = $tab['class'];
					}
				 	echo '
				' . Html::rawElement( 'li', $liAttribs, $linkHtml );
				} ?>
			</ul>
		</div>
	</div>
<?php
	}
	/*************************************************************************************************/
	function personalBox() {
?>
	<div class="portlet" id="p-personal">
		<h2><?php $this->msg('personaltools') ?></h2>
		<div class="pBody">
			<ul>
<?php
		foreach ( $this->getPersonalTools() as $key => $item ) {
			echo $this->makeListItem($key, $item);
		}
?>
			</ul>
		</div>
	</div>
<?php
	}
	/*************************************************************************************************/
	function recentChangeBox() {
?>
 	<div class="portlet" id="p-rc">
		<h2>Recent Changes</h2>
		<div class="pBody">
			<ul>
<?php
		$conds = array();
		$opts = array();
		$tables = array( 'recentchanges' );
		$join_conds = array();
		$query_options = array( 'USE INDEX' => array('recentchanges' => 'rc_timestamp') );
 		$dbr = wfGetDB( DB_SLAVE );
		$rows = $dbr->select( $tables, '*', $conds, __METHOD__, array( 'ORDER BY' => 'rc_timestamp DESC', 'LIMIT' => 50 ) + $query_options, $join_conds );
		$history = array();
		foreach ($rows as $row) {
			$title = Title::makeTitle( $row->rc_namespace, $row->rc_title );
			if (in_array($title, $history)) {
				continue;
			}
			array_push($history, $title);
			$str = $row->rc_title;
			$str = str_replace("_", " ", $str);
			$str = str_replace(":", ": ", $str);
?>
			<li><a href="<?php echo $title->getLocalURL(); ?>"><?php echo $str; ?></a></li>
<?php
		}
?>
			</ul>
		</div>
	</div>
<?php
	}
	/*************************************************************************************************/
	function toolbox() {
?>
	<div class="portlet" id="p-tb">
		<h2><?php $this->msg('toolbox') ?></h2>
		<div class="pBody">
			<ul>
<?php
		foreach ( $this->getToolbox() as $key => $tbitem ) {
			echo $this->makeListItem($key, $tbitem);
		}
		wfRunHooks( 'MonoBookTemplateToolboxEnd', array( &$this ) );
		wfRunHooks( 'SkinTemplateToolboxEnd', array( &$this, true ) );
?>
			</ul>
		</div>
	</div>
<?php
	}

	/*************************************************************************************************/
	function languageBox() {
		if( $this->data['language_urls'] ) {
?>
	<div id="p-lang" class="portlet">
		<h2<?php $this->html('userlangattributes') ?>><?php $this->msg('otherlanguages') ?></h2>
		<div class="pBody">
			<ul>
<?php		foreach($this->data['language_urls'] as $key => $langlink) { ?>
				<?php echo $this->makeListItem($key, $langlink); ?>

<?php		} ?>
			</ul>
		</div>
	</div>
<?php
		}
	}

	/*************************************************************************************************/
	function customBox( $bar, $cont ) {
		$portletAttribs = array( 'class' => 'generated-sidebar portlet', 'id' => Sanitizer::escapeId( "p-$bar" ) );
		$tooltip = Linker::titleAttrib( "p-$bar" );
		if ( $tooltip !== false ) {
			$portletAttribs['title'] = $tooltip;
		}
		echo '	' . Html::openElement( 'div', $portletAttribs );
?>

		<h2><?php $msg = wfMessage( $bar ); echo htmlspecialchars( $msg->exists() ? $msg->text() : $bar ); ?></h2>
		<div class='pBody'>
<?php   if ( is_array( $cont ) ) { ?>
			<ul>
<?php 			foreach($cont as $key => $val) { ?>
				<?php echo $this->makeListItem($key, $val); ?>

<?php			} ?>
			</ul>
<?php   } else {
			# allow raw HTML block to be defined by extensions
			print $cont;
		}
?>
		</div>
	</div>
<?php
	}
} // end of class


