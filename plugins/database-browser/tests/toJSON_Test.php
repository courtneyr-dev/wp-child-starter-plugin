<?php
require_once( 'Output_Base_Test.php' );
class toJSON_Test extends Output_Base_Test {
	
	public function test_toJSON_with_rows_returns_correct_html() {
		
		// Arrange
		$expectedJson = "{
	\"database\": \"database_name\",
	\"table\": \"table_name\",
	\"columns\":
	{
		\"column\":
		{
			\"name\": \"id\",
			\"type\": \"bigint(20) unsigned\",
			\"null\": \"NO\",
			\"key\": \"PRI\",
			\"default\": \"\",
			\"extra\": \"auto_increment\"
		},
		
		\"column\":
		{
			\"name\": \"date\",
			\"type\": \"datetime\",
			\"null\": \"NO\",
			\"key\": \"\",
			\"default\": \"0000-00-00 00:00:00\",
			\"extra\": \"\"
		},
		
		\"column\":
		{
			\"name\": \"name\",
			\"type\": \"longtext\",
			\"null\": \"\",
			\"key\": \"\",
			\"default\": \"\",
			\"extra\": \"\"
		}
	},
	\"rows\":
	{
		\"row\":
		{
		\"id\": \"0\",
			\"date\": \"0000-00-00 00:00:00\",
			\"name\": \"Row 0\"
		},
		\"row\":
		{
		\"id\": \"1\",
			\"date\": \"0000-00-00 00:00:00\",
			\"name\": \"Row 1\"
		},
		\"row\":
		{
		\"id\": \"2\",
			\"date\": \"0000-00-00 00:00:00\",
			\"name\": \"Row 2\"
		}
	}
}";
		$database_browser = parent::getDatabaseBrowserInstance();
		
		// Act
		$json = $database_browser->toJSON( $this->database_name, $this->table, $this->columns, $this->rows );
		
		// Assert
		$this->assertEquals( $expectedJson, trim( $json ) );
    }
	
}