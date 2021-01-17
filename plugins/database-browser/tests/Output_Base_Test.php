<?php
class Output_Base_Test extends PHPUnit_Framework_TestCase {

	protected $database_name;
	protected $table;
	protected $columns;
	protected $rows;
	
	public function __construct() {
		
		$this->database_name = "database_name";
		$this->table = "table_name";
		
		$this->columns = [];
		
		$column1 = new stdClass();
		$column1->Field = "id";
		$column1->Type = "bigint(20) unsigned";
		$column1->Null = "NO";
		$column1->Key = "PRI";
		$column1->Default = "";
		$column1->Extra = "auto_increment";
		$this->columns[] = $column1;
	
		$column2 = new stdClass();
		$column2->Field = "date";
		$column2->Type = "datetime";
		$column2->Null = "NO";
		$column2->Key = "";
		$column2->Default = "0000-00-00 00:00:00";
		$column2->Extra = "";
		$this->columns[] = $column2;
		
		$column3 = new stdClass();
		$column3->Field = "name";
		$column3->Type = "longtext";
		$column3->Null = "";
		$column3->Key = "";
		$column3->Default = "";
		$column3->Extra = "";
		$this->columns[] = $column3;
		
		for( $x = 0; $x < 3; $x++ ) {
			$row = [];
			$row["id"] = $x;
			$row["date"] = "0000-00-00 00:00:00";
			$row["name"] = "Row " . $x;
			$this->rows[] = $row;
		}
		
    }
	
	protected function getDatabaseBrowserInstance() {
		
		require_once( '../database-browser.php' );
		return new DatabaseBrowser();
		
	}
	
	protected function setNoRows() {
		
		$this->rows = [];
		
	}
}