<h3>
	<?php echo $title ?>
	<button type="button" class="navbutton button-primary" id="newquery"><?php _e( "New query", "databasebrowser" ) ?></button>
	<?php if ( $this->querytype == "table" ) { ?>
	<button type="button" class="navbutton button-primary" id="editquery"><?php _e( "Where/Order by/Limit", "databasebrowser" ) ?></button>
	<?php } ?>
</h3>

<div id="querynav" class="hider">
	<?php include_once( "navigation.php" ); ?>
</div>