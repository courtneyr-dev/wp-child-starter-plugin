<form action="tools.php?page=databasebrowser&amp;queryname=<?php echo urlencode( $this->queryname ) ?>&amp;_wpnonce=<?php echo wp_create_nonce( "query" ) ?>" method="post">
    <h4><button type="button" class="button-primary" id="queryheader"><?php _e( "Edit query", "databasebrowser" ) ?></button></h4>
	<div class="hider" id="query">
		<p>
			<textarea name="query" cols="30" rows="10" style="width:100%;height:10em"><?php echo $this->query ?></textarea>
		</p>
		<p>
			<input type="submit" class="button-primary" value="<?php _e( "Run query", "databasebrowser" ) ?>" />
			<?php _e( "Enter a name to save this query: ", "databasebrowser" ) ?>
			<input type="text" name="queryname" value="<?php echo $this->queryname ?>" />
		</p>
	</div>
</form>