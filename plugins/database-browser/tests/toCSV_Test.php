<?php
require_once( 'Output_Base_Test.php' );
class toCSV_Test extends Output_Base_Test {
	
	public function test_toCSV_with_rows_returns_correct_html() {
		
		// Arrange
		$expectedCsv = "id,date,name
\"0\",\"0000-00-00 00:00:00\",\"Row 0\"
\"1\",\"0000-00-00 00:00:00\",\"Row 1\"
\"2\",\"0000-00-00 00:00:00\",\"Row 2\"";
		$database_browser = parent::getDatabaseBrowserInstance();
		
		// Act
		$csv = $database_browser->toCSV( $this->database_name, $this->table, $this->columns, $this->rows );
		
		// Assert
		$this->assertEquals( $expectedCsv, trim( $csv ) );
    }
	
	public function test_toCSV_with_iso9959_characters_returns_correct_html() {
		
		// Arrange
		$expectedCsv = "id,date,name
\"0\",\"0000-00-00 00:00:00\",\"Row 0\"
\"1\",\"0000-00-00 00:00:00\",\"Row 1\"
\"2\",\"0000-00-00 00:00:00\",\"Row 2\"
\"3\",\"0000-00-00 00:00:00\",\"ñ\"";
		$database_browser = parent::getDatabaseBrowserInstance();
		$iso8859_character_row = [];
		$iso8859_character_row["id"] = 3;
		$iso8859_character_row["date"] = "0000-00-00 00:00:00";
		$iso8859_character_row["name"] = "Ã±";
		$this->rows[] = $iso8859_character_row;
		
		// Act
		$csv = $database_browser->toCSV( $this->database_name, $this->table, $this->columns, $this->rows );
		
		// Assert
		$this->assertEquals( $expectedCsv, trim( $csv ) );
    }
	
}