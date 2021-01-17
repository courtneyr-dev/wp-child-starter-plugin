<form action="tools.php?page=databasebrowser" method="post">
	<p>
		<label for="tablelist"><?php _e( "Select table", "databasebrowser" ) ?></label>
		<select name="table" id="tablelist">
		<?php
            foreach ( $this->tables as $table ) {
            ?>
			<option value="<?php echo $table ?>"><?php echo $table ?></option>
			<?php
            }
		?>
        </select>
		<input type="submit" class="button-primary" value="<?php _e( "Select table", "databasebrowser" ) ?>" />
		<?php wp_nonce_field( "query" ) ?>
	</p>
</form>
			
<?php
// display the stored queries
if ( $this->storedqueries != null ) {
?>
	<h3><?php _e( "Run a stored query", "databasebrowser" ) ?></h3>
	<ul>
	<?php
		foreach( $this->storedqueries as $key => $value ) {
		?>
		<li><a href="<?php echo wp_nonce_url( $this->formURL . '&amp;queryname=' . urlencode( $key ), "query" ) ?>"><?php echo $key ?></a></li>
		<?php
		}
		?>
	</ul>
<?php
}
?>