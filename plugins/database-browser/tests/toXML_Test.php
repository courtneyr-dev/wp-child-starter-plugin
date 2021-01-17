<?php
require_once( 'Output_Base_Test.php' );
class toXML_Test extends Output_Base_Test {
	
	public function test_toXML_with_rows_returns_correct_html() {
		
		// Arrange
		$expectedXml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?> 
<database name=\"database_name\">
	<table name=\"table_name\">
		<columns>
			<column name=\"id\" type=\"bigint(20) unsigned\" null=\"NO\" key=\"PRI\" default=\"\" extra=\"auto_increment\" />
			<column name=\"date\" type=\"datetime\" null=\"NO\" key=\"\" default=\"0000-00-00 00:00:00\" extra=\"\" />
			<column name=\"name\" type=\"longtext\" null=\"\" key=\"\" default=\"\" extra=\"\" />
		</columns>
		<rows>
			<row>
				<id>0</id>
				<date>0000-00-00 00:00:00</date>
				<name>Row 0</name>
			</row>
			<row>
				<id>1</id>
				<date>0000-00-00 00:00:00</date>
				<name>Row 1</name>
			</row>
			<row>
				<id>2</id>
				<date>0000-00-00 00:00:00</date>
				<name>Row 2</name>
			</row>
		</rows>
	</table>
</database>";
		$database_browser = parent::getDatabaseBrowserInstance();
		
		// Act
		$xml = $database_browser->toXML( $this->database_name, $this->table, $this->columns, $this->rows );
		
		// Assert
		$this->assertEquals( $expectedXml, trim( $xml ) );
    }
	
}