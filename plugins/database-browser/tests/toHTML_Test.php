<?php
require_once( 'Output_Base_Test.php' );
class toHTML_Test extends Output_Base_Test {
	
	public function test_toHTML_with_rows_returns_correct_html() {
		
		// Arrange
		$expectedHtml = "<table class=\"\">
	<thead>
		<tr>
			<th>id</th>
			<th>date</th>
			<th>name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>0</td>
			<td>0000-00-00 00:00:00</td>
			<td>Row 0</td>
		</tr>
		<tr>
			<td>1</td>
			<td>0000-00-00 00:00:00</td>
			<td>Row 1</td>
		</tr>
		<tr>
			<td>2</td>
			<td>0000-00-00 00:00:00</td>
			<td>Row 2</td>
		</tr>
	</tbody>
</table>";
		$database_browser = parent::getDatabaseBrowserInstance();
		
		// Act
		$html = $database_browser->toHTML( $this->database_name, $this->table, $this->columns, $this->rows );
		
		// Assert
		$this->assertEquals( $expectedHtml, trim( $html ) );
    }
	
	public function test_toHTML_given_class_returns_correct_html() {
		
		// Arrange
		$expectedHtml = "<table class=\"test\">
	<thead>
		<tr>
			<th>id</th>
			<th>date</th>
			<th>name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>0</td>
			<td>0000-00-00 00:00:00</td>
			<td>Row 0</td>
		</tr>
		<tr>
			<td>1</td>
			<td>0000-00-00 00:00:00</td>
			<td>Row 1</td>
		</tr>
		<tr>
			<td>2</td>
			<td>0000-00-00 00:00:00</td>
			<td>Row 2</td>
		</tr>
	</tbody>
</table>";
		$database_browser = parent::getDatabaseBrowserInstance();
		
		// Act
		$html = $database_browser->toHTML( $this->database_name, $this->table, $this->columns, $this->rows, "test" );
		
		// Assert
		$this->assertEquals( $expectedHtml, trim( $html ) );
    }
	
	public function test_toHTML_truncated_characters_returns_correct_html() {
		
		// Arrange
		$expectedHtml = "<table class=\"\">
	<thead>
		<tr>
			<th>id</th>
			<th>date</th>
			<th>name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>0</td>
			<td>0...</td>
			<td>R...</td>
		</tr>
		<tr>
			<td>1</td>
			<td>0...</td>
			<td>R...</td>
		</tr>
		<tr>
			<td>2</td>
			<td>0...</td>
			<td>R...</td>
		</tr>
	</tbody>
</table>";
		$database_browser = parent::getDatabaseBrowserInstance();
		
		// Act
		$html = $database_browser->toHTML( $this->database_name, $this->table, $this->columns, $this->rows, "", 1 );
		
		// Assert
		$this->assertEquals( $expectedHtml, trim( $html ) );
    }
	
	public function test_toHTML_with_iso8859_characters_returns_correct_html() {
		
		// Arrange
		$expectedHtml = "<table class=\"\">
	<thead>
		<tr>
			<th>id</th>
			<th>date</th>
			<th>name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>0</td>
			<td>0000-00-00 00:00:00</td>
			<td>Row 0</td>
		</tr>
		<tr>
			<td>1</td>
			<td>0000-00-00 00:00:00</td>
			<td>Row 1</td>
		</tr>
		<tr>
			<td>2</td>
			<td>0000-00-00 00:00:00</td>
			<td>Row 2</td>
		</tr>
		<tr>
			<td>3</td>
			<td>0000-00-00 00:00:00</td>
			<td>ñ</td>
		</tr>
	</tbody>
</table>";
		$database_browser = parent::getDatabaseBrowserInstance();
		$iso8859_character_row = [];
		$iso8859_character_row["id"] = 3;
		$iso8859_character_row["date"] = "0000-00-00 00:00:00";
		$iso8859_character_row["name"] = "Ã±";
		$this->rows[] = $iso8859_character_row;
		
		// Act
		$html = $database_browser->toHTML( $this->database_name, $this->table, $this->columns, $this->rows );
		
		// Assert
		$this->assertEquals( $expectedHtml, trim( $html ) );
    }
	
}