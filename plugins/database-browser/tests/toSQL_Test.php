<?php
require_once( 'Output_Base_Test.php' );
class toSQL_Test extends Output_Base_Test {
	
	public function test_toSQL_with_rows_returns_correct_html() {
		
		// Arrange
		$expectedSql = "INSERT INTO table_name (date,name) VALUES ('0000-00-00 00:00:00','Row 0');
INSERT INTO table_name (date,name) VALUES ('0000-00-00 00:00:00','Row 1');
INSERT INTO table_name (date,name) VALUES ('0000-00-00 00:00:00','Row 2');";
		$database_browser = parent::getDatabaseBrowserInstance();
		
		// Act
		$sql = $database_browser->toSQL( $this->database_name, $this->table, $this->columns, $this->rows );
		
		// Assert
		$this->assertEquals( $expectedSql, trim( $sql ) );
    }
	
}