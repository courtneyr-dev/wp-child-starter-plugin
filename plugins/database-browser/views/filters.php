<form action="<?php echo $this->formURL ?>" method="post" id="filters" class="hider">
	<p>
		<label id="wherelabel" for="where"><?php _e( "Where:", "databasebrowser" ) ?></label>
		<input type="text" name="where" id="where" value="<?php echo @stripslashes( $this->where ) ?>" />
	</p>
	<p>
		<label id="orderbylabel" for="orderby"><?php _e( "Order by:", "databasebrowser" ) ?></label>
		<input type="text" name="orderby" id="orderby" value="<?php echo @stripslashes( $this->orderby ) ?>" />
	</p>
	<p>
		<label id="limitlabel" for="limit"><?php _e( "Rows per page:", "databasebrowser" ) ?></label>
		<input type="text" name="limit" id="limit" value="<?php echo @stripslashes( $this->limit ) ?>" />
	</p>
	<p>
		<button type="submit" class="button-primary"><?php _e( "Re-run query", "databasebrowser" ) ?></button>
		<?php wp_nonce_field( "query" ) ?>
	</p>
</form>

<?php include_once( "query.php" ); ?>