<?php
// no data found
if ( $this->rowcount == 0 ) {
?>
<div id="message" class="updated">
    <p><strong><?php _e( "No data was found in this table.", "databasebrowser" ) ?></strong></p>
</div>
<?php
}

// data found
if ( $this->rowcount > 0 ) {

	// get the pages
	if ( $this->querytype == "table" ) {
		$paginator->findPages( $this->rowcount );
	}
?>
		
<p>Long text field values have had HTML tags removed and been truncated to 250 characters.</p>
		
<?php if ( $this->querytype == "table" ) { echo $paginator->links; } ?>

<?php $this->exportLinks(); ?>
		
<div class="tablewrapper">

	<?php echo $this->toHTML( $this->database_name, $this->table, $this->columns, $this->rows, "widefat", 250 ); ?>

</div>

<?php if ( $this->querytype == "table" ) { echo $paginator->links; } ?>

<?php $this->exportLinks(); ?>

<?php
}
?>