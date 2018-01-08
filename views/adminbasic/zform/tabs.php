<?php
defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
if (isset ( $tabs ) && is_array ( $tabs )) {
	?>
<div id="tabs">
	<ul class="nav nav-tabs" id="nav-tabs">
	<?php
	foreach ( $tabs as $title => $view ) {
		?>
		<li><a href="#fragment-<?=$title?>"><?=$title?></a></li>
		<?php
	}
	?>
	</ul>
	<div class="tab-content">
		<?php
	foreach ( $tabs as $title => $view ) {
		?>
	<div id="fragment-<?=$title?>"><?=$view?></div>
			<?php
	}
	?>
    </div>
</div>
<?php
}