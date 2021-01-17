<?php

/*
  Plugin Name: Database Browser
  Plugin URI: http://www.stillbreathing.co.uk/wordpress/database-browser/
  Description: Easily browse the data in your database, and download in CSV, XML, SQL and JSON format
  Author: Chris Taylor
  Version: 1.4.4
  Author URI: http://www.stillbreathing.co.uk/
 */

// include the Plugin_Register class
if ( function_exists( 'plugin_dir_path' ) && class_exists( 'Plugin_Register' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . "plugin-register.class.php" );
	// create a new instance of the Plugin_Register class
	$register = new Plugin_Register();
	$register->file = __FILE__;
	$register->slug = "databasebrowser";
	$register->name = "Database Browser";
	$register->version = "1.4.4";
	$register->developer = "Chris Taylor";
	$register->homepage = "http://www.stillbreathing.co.uk";
	$register->Register();
}

if ( ! class_exists( 'DatabaseBrowser' ) ) {

	class DatabaseBrowser {

		/**
		 * Whether the plugin is in debugging mode
		 * 
		 * @since 1.2
		 */
		var $debugging = false;
	    
		/**
		 * The current version of Database Browser
		 * 
		 * @since 1.0
		 */
		var $version = "1.4.4";

		/**
		 * The array of table names in the database
		 * 
		 * @since 1.0
		 */
		var $tables = array();

		/**
		 * The name of the WordPress database
		 * 
		 * @since 1.4
		 */
		var $database_name = null;
		
		/**
		 * The name of the currently selected table; null if no table selected
		 * 
		 * @since 1.0
		 */
		var $table = null;

		/**
		 * The number of rows to show on each page; only has an effect when $querytype is 'table'
		 * 
		 * @since 1.2
		 */
		var $limit = 100;

		/**
		 * The 'where' clause for the current table query; only has an effect when $querytype is 'table'
		 * 
		 * @since 1.2
		 */
		var $where = "";

		/**
		 * The 'order by' clause for the current table query; only has an effect when $querytype is 'table'
		 * 
		 * @since 1.2
		 */
		var $orderby = "";

		/**
		 * The array of column names in the currently selected table; empty if no table selected
		 * 
		 * @since 1.0
		 */
		var $columns = array();

		/**
		 * The total number of rows found for the current query; takes into account pagination
		 * 
		 * @since 1.0
		 */
		var $rowcount = array();

		/**
		 * The array of row objects found for the current query
		 * 
		 * @since 1.0
		 */
		var $rows = array();

		/**
		 * The error returned from the database
		 * 
		 * @since 1.0
		 */
		var $error = null;

		/**
		 * The current SQL query that is being executed
		 * 
		 * @since 1.0
		 */
		var $query = null;

		/**
		 * The name of the stored query which is being executed; empty if no stored query has been selected
		 * 
		 * @since 1.2
		 */
		var $queryname = "";

		/**
		 * The current type of query; empty if no query is being executed
		 * 
		 *
		 * This is either empty, or one of the following values:
		 * - table: denoting that a simple table query is being executed
		 * - customquery: denoting that a custom query is being executed
		 * - storedquery: denoting that a stored query is being executed
		 *
		 * @since 1.2
		 */
		var $querytype = "";

		/**
		 * Loads the stored queries from the options table
		 * 
		 * @since 1.2
		 */
		var $storedqueries = null;

		/**
		 * The URL to which forms are submitted
		 * 
		 * @since 1.0
		 */
		var $formURL;

		/**
		 * Run when an instance of this class is constructed
		 * 
		 * @since 1.0
		 */
		function __construct() {
			if ( function_exists( 'plugin_dir_path' ) ) {
				require_once( plugin_dir_path( __FILE__ ) . "pagination.class.php" );
				$this->formURL = remove_query_arg( "p" );
			}
		}

		// =====================================================================================================================
		// Initialisation

		/**
		 * Sets up the plugin when the WordPress admin area is initialised
		 * 
		 * @since 1.0
		 */
		function on_admin_init() {

			// ensure a session has been started
			if( ! isset( $_SESSION ) ){
				session_start();
			}

			// if requesting a table, redirect
			if ( wp_verify_nonce( @$_POST["_wpnonce"], "query" ) && isset( $_POST["table"] ) && $_POST["table"] != "" ) {
				unset( $_SESSION["query"] );
				unset( $_SESSION["orderby"] );
				unset( $_SESSION["limit"] );
				unset( $_SESSION["where"] );
				header( "Location: tools.php?page=databasebrowser&table=" . $_POST["table"] . "&_wpnonce=" . wp_create_nonce( "query" ) );
			}
			
			// set the database name
			$this->database_name = DB_NAME;

			// load textdomain
			load_plugin_textdomain( 'databasebrowser', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// enqueue CSS
			$this->enqueue_admin_css();

			// enqueue JS
			$this->enqueue_admin_js();

			// load the tables
			$this->loadTables();

			// load the saved queries
			$this->loadQueries();

			// get the requested table name
			$this->table = @$_GET["table"];
			if ( $this->table != "" ) {
			    
				// get the filters from the session
				$this->where = @$_SESSION["where"];
				$this->orderby = @$_SESSION["orderby"];
				if ( @$_SESSION["limit"] != "" ) {
				    $this->limit = @$_SESSION["limit"];
				}
				
				// set the query type
				$this->querytype = "table";
				
				// if a where filter has been POSTed
				if ( ! empty( $_POST["where"] ) ) {
					$this->where = stripslashes( $_POST["where"] );
					$_SESSION["where"] = $this->where;
				}
				
				// if an order by filter has been POSTed
				if ( ! empty( $_POST["orderby"] ) ) {
					$this->orderby = stripslashes( $_POST["orderby"] );
					$_SESSION["orderby"] = $this->orderby;
				}
				
				// if a limit filter has been POSTed
				if ( ! empty( $_POST["limit"] ) ) {
					$this->limit = stripslashes( $_POST["limit"] );
					$_SESSION["limit"] = $this->limit;
				}
			}

			// get the query
			$this->query = @$_POST["query"];
			if ( $this->query != "" ) {
				$this->querytype = "customquery";
			}

			// if a stored query name has been given then set that as the current query
			$this->queryname = @urldecode( $_GET["queryname"] );
			if ( $this->queryname != "" ) {
			    
				// store a variable allowing the code to resave this query if it has changed
				$resave = true;
			    
				// if the query name given does not match the POSTed query name then the 
				// user is renaming the query, probably after editing it, so save the new
				// query
				if ( @$_POST["queryname"] != "" && @$_POST["queryname"] != $this->queryname ) {
					$this->queryname = $_POST["queryname"];
					$this->query = @$_POST["query"];
					$this->saveQuery( $this->queryname, $this->query, false );
					// we don't want to resave the query with the old name
					$resave = false;
					
					// debugging
					$this->debug( "<p>Saved new stored query with name '" . $this->queryname . "'</p>" );
				}

				$this->querytype = "storedquery";
				$this->query = $this->storedqueries[$this->queryname];
				
				// if we can resave the query (because the name hasn't changed) and the query has 
				// been re-POSTed then resave it, but don't notify the user
				if ( $resave && @$_POST["query"] != "" ) {
					$this->query = $_POST["query"];
					$this->saveQuery( $this->queryname, $this->query, false );
					
					// debugging
					$this->debug( "<p>Updated stored query with name '" . $this->queryname . "'</p>" );
				}
			}
			
			// if exporting, do the export
			if ( wp_verify_nonce( @$_GET["_wpnonce"], "export" ) && @$_GET["export"] != "" ) {
				$this->export();
			}
		}

		/**
		 * Adds the plugin item as a submenu item to the 'Tools' menu
		 * 
		 * @since 1.0
		 */
		function on_admin_menu() {
			add_submenu_page( 'tools.php', __( "Database browser", "databasebrowser" ), __( "Database browser", "databasebrowser" ), 'export', 'databasebrowser', array( $this, "adminPage" ) );
		}

		/**
		 * Enqueue the CSS for the admin area
		 * 
		 * @since 1.0
		 */
		function enqueue_admin_css() {
			wp_register_style( 'databasebrowser', plugin_dir_url( __FILE__ ) . "database-browser.css" );
			wp_enqueue_style( 'databasebrowser' );
		}

		/**
		 * Enqueue the JavaScript for the admin area
		 * 
		 * @since 1.0
		 */
		function enqueue_admin_js() {
			wp_enqueue_script( $handle = 'databasebrowser-js', $src = plugins_url( 'database-browser.js', __FILE__ ), $deps = array(), $ver = $this->version, true );
		}
		
		/**
		 * Display a debugging message
		 * 
		 * @since 1.2
		 */
		function debug( $message ) {
		    
		    // if not debugging return immediately
		    if ( ! $this->debugging ) {
			return;
		    }
		    
		    print '
		    <div id="message" class="updated">
			<h3>DEBUGGING</h3>
			' . $message . '
		    </div>
		    ';
		}

		// =====================================================================================================================
		// Admin page

		/**
		 * Handles displaying of the DatabaseBrowser admin page
		 * 
		 * @since 1.0
		 */
		function adminPage() {
		
			echo '
			<div class="wrap" id="databasebrowser">
				<h2>' . __( "Database browser", "databasebrowser" ) . '</h2>
			';

			// if no query is being run
			if ( $this->querytype == "" ) {

				// show the navigation and return
				include_once( "views/navigation.php" );
				return;

			}

			// if a table has been chosen
			if ( wp_verify_nonce( @$_GET["_wpnonce"], "query" ) && $this->querytype == "table" ) {

				// get the limit
				if ( @$_POST["limit"] != "" ) {
					$_SESSION["limit"] = (int)$_POST["limit"];
					$this->limit = (int)$_POST["limit"];
				}

				// create the new paginator
				$paginator = new Paginator( $this->limit );

				// load the table
				$this->loadTable( $paginator->findStart(), $this->limit );

				// include the title view
				$title = sprintf( __( "Table: %s", "databasebrowser" ), $this->table );
				include_once( "views/title.php" );

				// include the filters view
				include_once( "views/filters.php" );

				// include the results view
				include_once( "views/results.php" );

				return;
			}

			// if a custom query is being run
			if ( wp_verify_nonce( @$_GET["_wpnonce"], "query" ) && $this->querytype == "customquery" ) {

				// see if this query is being saved
				$queryname = trim( @$_POST["queryname"] );
				if ( $queryname != "" ) {
					$this->saveQuery( $queryname, $this->query, true );
				}

				// run the query
				$this->runQuery( $this->query );

				// include the title view
				$title = __( "Custom query", "databasebrowser" );
				include_once( "views/title.php" );

				// include the query view
				include_once( "views/query.php" );

				// include the results view
				include_once( "views/results.php" );

				return;
			}

			// if a stored query is being run
			if ( wp_verify_nonce( @$_GET["_wpnonce"], "query" ) && $this->querytype == "storedquery" ) {

				// save this query
				$this->saveQuery( $this->queryname, $this->query, false );

				// run the query
				$this->runQuery( $this->query );

				// include the title view
				$title = sprintf( __( "Stored query: %s", "databasebrowser" ), $this->queryname );
				include_once( "views/title.php" );

				// include the query view
				include_once( "views/query.php" );

				// include the results view
				include_once( "views/results.php" );

				return;
			}

			// if we get here something has gone wrong, so show the navigation
			include_once( "views/navigation.php" );
		}

		// =====================================================================================================================
		// Stored queries

		/**
		 * Loads the stored queries from the options table
		 * 
		 * @since 1.2
		 */
		function loadQueries() {
			$this->storedqueries = maybe_unserialize( get_option( "DatabaseBrowserQueries", null ) );
		}

		/**
		 * Saves the current query to the saved queries option using the given query name
		 * 
		 * @since 1.2
		 */
		function saveQuery( $queryname, $query, $notify ) {

			// set the current query name
			$this->queryname = $queryname;

			// see if we are adding or updating the queries
			$add = false;
			if ( $this->storedqueries == null ) {
				$add = true;
				$this->storedqueries = array();
			} else {
				unset( $this->storedqueries[$queryname] );
			}

			// add this query to the array
			$this->storedqueries[$queryname] = $query;
			
			// remove any queries with no name and/or no query
			$queryIndex = 0;
			foreach( $this->storedqueries as $key => $value ) {
				if ( trim( $key ) == "" || trim( $value ) == "" ) {
					unset( $this->storedqueries[$key] );
				}
				$queryIndex++;
			}
			
			// debugging
			$this->debug( '<h4>Stored query array to save</h4><textarea style="width:100%;height:10em">' .
			    print_r( $this->storedqueries, true ) .
			    '</textarea>' );
			
			// serialize the value so it can be saved
			$value = maybe_serialize( $this->storedqueries );

			$message = '';
			
			// adding a saved query for the first time
			if ( $add ) {
				add_option( "DatabaseBrowserQueries", $value, "", "no" );
				sprintf( __( "This query has been saved as: %s", "databasebrowser" ), $queryname );

			// updating queries
			} else {
				update_option( "DatabaseBrowserQueries", $value );
				sprintf( __( "The query %s has been updated", "databasebrowser" ), $queryname );
			}
			
			// if not notifying the user then return here
			if ( ! $notify ) {
				return;
			}
			
			// display the result to the user
			echo '
			    <div id="message" class="updated">
				    <p>' . $message . '</p>
			    </div>
			    ';
		}

		// =====================================================================================================================
		// Database manipulation

		/**
		 * Loads the database tables visible to the WordPress user
		 * 
		 * @since 1.0
		 */
		function loadTables() {
			global $wpdb;
			$sql = "SHOW TABLES;";
			$results = $wpdb->get_results( $sql, ARRAY_N );
			$tables = array();
			foreach ( $results as $result ) {
				$tables[] = $result[0];
			}
			$this->tables = $tables;
		}

		/**
		 * Loads the columns and rows in the current table
		 * 
		 * @since 1.0
		 */
		function loadTable( $start = 0, $limit = 100 ) {
			if ( $this->table != "" ) {
				unset( $_SESSION["query"] );
				$this->loadTableColumns();
				$this->loadRows( $start, $limit );
			}
		}

		/**
		 * Loads the columns in the current table
		 * 
		 * @since 1.0
		 */
		function loadTableColumns() {
			global $wpdb;
			$sql = "SHOW COLUMNS FROM " . esc_sql( $this->table ) . ";";
			$this->columns = $wpdb->get_results( $sql );
		}

		/**
		 * Loads $limit rows from the current table starting at row number $start
		 * 
		 * @since 1.0
		 */
		function loadRows( $start = 0, $limit = 100 ) {
			global $wpdb;
			$sql = "SELECT SQL_CALC_FOUND_ROWS ";
			foreach ( $this->columns as $column ) {
				$sql .= "`" . $column->Field . "`, ";
			}
			$sql = trim( trim( $sql ), "," );
			$sql .= " FROM " . esc_sql( $this->table );
			
			// using a where
			if ( $this->where != "" ) {
				$sql .= " WHERE " . $this->where;
			}

			// using an order by
			if ( $this->orderby != "" ) {
				$sql .= " ORDER BY `" . $this->orderby . "`";
			}

			// using a limit
			if ( $limit != null ) {
				$sql .= $wpdb->prepare( " LIMIT %d, %d", $start, $limit );
			}
			$sql .= ";";
			$this->query = $sql;
			$this->rows = $wpdb->get_results( $sql, ARRAY_A );
			$this->error = $wpdb->last_error;
			$this->rowcount = $wpdb->get_var( "SELECT FOUND_ROWS();" );
		}

		/**
		 * Runs a SQL query and sets the result in the properties of this class
		 * 
		 * @since 1.0
		 */
		function runQuery( $query ) {
			global $wpdb;
			$query = stripslashes( $query );
			$this->query = $query;
			
			$_SESSION["query"] = $query;
			$this->rows = $wpdb->get_results( $query, ARRAY_A );
			$this->error = $wpdb->last_error;
			$this->rowcount = count( $this->rows );
			if ( count( $this->rows ) > 0 ) {
				foreach ( $this->rows[0] as $key => $value ) {
					$obj = new stdClass();
					$obj->Field = $key;
					$this->columns[] = $obj;
				}
			}
		}

		// =====================================================================================================================
		// Export

		/**
		 * Displays the links to export the current data to different formats
		 * 
		 * @since 1.0
		 */
		function exportLinks() {

			echo '
			<div id="exportlinks">
				<ul>
					<li><a href="' . wp_nonce_url( 'tools.php?page=databasebrowser&amp;table=' . $this->table . '&amp;export=XML', 'export' ) . '" class="button">XML</a></li>
					<li><a href="' . wp_nonce_url( 'tools.php?page=databasebrowser&amp;table=' . $this->table . '&amp;export=HTML', 'export' ) . '" class="button">HTML</a></li>
					<li><a href="' . wp_nonce_url( 'tools.php?page=databasebrowser&amp;table=' . $this->table . '&amp;export=CSV', 'export' ) . '" class="button">CSV</a></li>
					<li><a href="' . wp_nonce_url( 'tools.php?page=databasebrowser&amp;table=' . $this->table . '&amp;export=SQL', 'export' ) . '" class="button">SQL</a></li>
					<li><a href="' . wp_nonce_url( 'tools.php?page=databasebrowser&amp;table=' . $this->table . '&amp;export=JSON', 'export' ) . '" class="button">JSON</a></li>
				</ul>
			</div>
			';
		}

		/**
		 * Handles exporting the current data
		 * 
		 * @since 1.0
		 */
		function export() {

			// if a query has been set
			if ( isset( $_SESSION["query"] ) && trim( $_SESSION["query"] ) != "" ) {
				$this->runQuery( $_SESSION["query"] );
			} else {
				// load the table with all rows
				$this->loadTable( null, null );
			}

			// get the requested export format
			$format = strtolower( $_GET["export"] );

			switch ( $format ) {
			
				case "xml":
					$this->forceDownload( "xml" );
					echo $this->toXML( $this->database_name, $this->table, $this->columns, $this->rows );
					die();
					break;
					
				case "html":
					$this->forceDownload( "html" );
					echo $this->toHTML( $this->database_name, $this->table, $this->columns, $this->rows );
					die();
					break;
					
				case "csv":
					$this->forceDownload( "csv" );
					echo $this->toCSV( $this->database_name, $this->table, $this->columns, $this->rows );
					die();
					break;
					
				case "sql":
					$this->forceDownload( "sql" );
					echo $this->toSQL( $this->database_name, $this->table, $this->columns, $this->rows );
					die();
					break;
					
				case "json":
					$this->forceDownload( "json" );
					echo $this->toJSON( $this->database_name, $this->table, $this->columns, $this->rows );
					die();
					break;
			}
		}

		/**
		 * Adds the headers required to force the browser to download the requested file as the given file format
		 * 
		 * @since 1.0
		 */
		function forceDownload( $format ) {
		    
			// when debugging allow the files to be shown in the browser
			if ( $this->debugging ) {
			    header( "Content-Type: text" );
			    return;
			}
		    
			header( "Cache-Control: public" );
			header( "Content-Description: WordPress Database Browser Table Export" );
			header( "Content-Disposition: attachment; filename=" . DB_NAME . "." . $this->table . "." . $format );
			header( "Content-Type: application/octet-stream" );
		}

		/**
		 * Outputs the given data as a JSON document
		 * 
		 * @since 1.0
		 */
		function toJson( $database_name, $table, $columns, $rows ) {
			$output = '{
	"database": "' . $database_name . '",
	"table": "' . $table . '",
	"columns":
	{';
			foreach ( $columns as $column ) {
				$output .= '
		"column":
		{
			"name": "' . str_replace( '"', '\\"', $column->Field ) . '"';
			// for custom queries we don't know the column types
			if ( property_exists( $column, "Type" ) ) {
				$output .= ',
			"type": "' . str_replace( '"', '\\"', $column->Type ) . '",
			"null": "' . str_replace( '"', '\\"', $column->Null ) . '",
			"key": "' . str_replace( '"', '\\"', $column->Key ) . '",
			"default": "' . str_replace( '"', '\\"', $column->Default ) . '",
			"extra": "' . str_replace( '"', '\\"', $column->Extra ) . '"';
			}
			$output .= '
		},
		';
			}
			$output = trim( trim( $output ), "," );
			$output .= '
	},
	"rows":
	{';
			foreach ( $rows as $row ) {
				$line = '
		"row":
		{
		';
				$col = '';
				foreach ( $columns as $column ) {
					$col .= '
			"' . $column->Field . '": "' . utf8_decode( $row[$column->Field] ) . '",';
				}
				$line .= trim( trim( $col ), "," );
				$line .= '
		},';
				$output .= $line;
			}
			$output = trim( trim( $output ), "," );
			$output .= '
	}
}';
			return $output;
		}

		/**
		 * Outputs the given data as a CSV document
		 * 
		 * @since 1.0
		 */
		function toCSV( $database_name, $table, $columns, $rows ) {
			$output = "";
			foreach ( $columns as $column ) {
				$output .= $column->Field . ",";
			}
			$output = trim( $output, "," );
			$output .= "\r\n";
			foreach ( $rows as $row ) {
				$line = "";
				foreach ( $columns as $column ) {
					$line .= '"' . str_replace( '"', '""', utf8_decode( $row[$column->Field] ) ) . '",';
				}
				$line = trim( $line, "," );
				$line .= "\r\n";
				$output .= $line;
			}
			$output = trim( $output );
			return $output;
		}

		/**
		 * Outputs the given data as SQL INSERT statements
		 * 
		 * @since 1.2
		 */
		function toSQL( $database_name, $table, $columns, $rows ) {
			$output = "";
			foreach ( $rows as $row ) {
				$cols = "INSERT INTO " . $table . " (";
				foreach ( $columns as $column ) {
					// for custom queries we don't know the column types
					if ( property_exists( $column, "Type" ) ) {
						if ( $column->Key != 'PRI' ) {
							$cols .= $column->Field . ",";
						}
					} else {
						$cols .= $column->Field . ",";
					}
				}
				$cols = trim( $cols, "," );
				$cols .= ") VALUES (";
				$data = "";
				foreach ( $columns as $column ) {
					// for custom queries we don't know the column types
					if ( property_exists( $column, "Type" ) ) {
						if ( $column->Key != 'PRI' ) {
							$data .= $this->writeColumnInsertSQL( $column, $row[$column->Field] );
						}
					} else {
						$data .= $this->writeColumnInsertSQL( $column, $row[$column->Field] );
					}
				}
				$data = trim( $data, "," );
				$data .= ");\r\n";
				$cols .= $data;
				$output .= $cols;
			}
			return $output;
		}

		/**
		 * Outputs the value $value of column $column for use in a SQL insert statement
		 * 
		 * @since 1.0
		 */
		private function writeColumnInsertSQL( $column, $value ) {
		    
			// for custom queries we don't know the column types
			if ( ! property_exists( $column, "Type" ) ) {
			    return "'" . str_replace( "'", "''", $value ) . "',";
			}
			
			// text columns
			if ( 	$this->startsWith( $column->Type, "longtext" )
				|| 	$this->startsWith( $column->Type, "text" )
				|| 	$this->startsWith( $column->Type, "mediumtext" )
				|| 	$this->startsWith( $column->Type, "longtext" )
				|| 	$this->startsWith( $column->Type, "char" )
				|| 	$this->startsWith( $column->Type, "varchar" ) ) {
				return "'" . str_replace( "'", "''", $value ) . "',";
			}
			// date columns
			if ( $this->startsWith( $column->Type, "datetime" ) ) {
				return "'" . $value . "',";
			}
			// iinteger columns
			if ( 	$this->startsWith( $column->Type, "bigint" )
				|| 	$this->startsWith( $column->Type, "int" )
				|| 	$this->startsWith( $column->Type, "tinyint" )
				|| 	$this->startsWith( $column->Type, "smallint" )
				|| 	$this->startsWith( $column->Type, "mediumint" ) ) {
				return $value . ",";
			}
		}

		/**
		 * Outputs the current data as an HTML document
		 * 
		 * @since 1.0
		 */
		function toHTML( $database_name, $table, $columns, $rows, $class = "", $truncateAtCharacter = -1 ) {
			$output = '
<table class="' . $class . '">
	<thead>
		<tr>';
			foreach ( $columns as $column ) {
				$output .= "
			<th>" . $column->Field . "</th>";
			}
			$output .= "
		</tr>
	</thead>
	<tbody>";
			foreach ( $rows as $row ) {
				$line = "
		<tr>";
				foreach ( $columns as $column ) {

					// if we are displaying all the content of every field
					if ( $truncateAtCharacter < 0 ) {

						$line .= '
			<td>' . utf8_decode( $row[$column->Field] ) . '</td>';

					// we need to truncate long field values at the given character
					} else {

						$value = $row[$column->Field];
						$stripped_value = strip_tags( $value );
						if ( strlen( $stripped_value ) > $truncateAtCharacter ) {
							$value = substr( $stripped_value, 0, $truncateAtCharacter ) . "...";
						}
						$line .= '
			<td>' . utf8_decode( $value ) . '</td>';

					}
				}
				$line .= "
		</tr>";
				$output .= $line;
			}
			$output .= "
	</tbody>
</table>
";
			return $output;
		}

		/**
		 * Outputs the current data as an XML document
		 * 
		 * @since 1.0
		 */
		function toXML( $database_name, $table, $columns, $rows ) {
			$output = '<?xml version="1.0" encoding="ISO-8859-1" ?> 
<database name="' . $database_name . '">
	<table name="' . $table . '">
		<columns>';
			foreach ( $columns as $column ) {
				// for custom queries we don't know the column types
				if ( property_exists( $column, "Type" ) ) {
					$output .= '
			<column name="' . $column->Field . '" type="' . $column->Type . '" null="' . $column->Null . '" key="' . $column->Key . '" default="' . $column->Default . '" extra="' . $column->Extra . '" />';
				} else {
					$output .= '
			<column name="' . $column->Field . '" />';
				}
			}
			$output .= '
		</columns>
		<rows>';
			foreach ( $rows as $row ) {
				$line = '
			<row>';
				foreach ( $columns as $column ) {
					$line .= '
				<' . $column->Field . '>' . $row[$column->Field] . '</' . $column->Field . '>';
				}
				$line .= '
			</row>';
				$output .= $line;
			}
			$output .= '
		</rows>
	</table>
</database>
';
			return $output;
		}

		// =====================================================================================================================
		// Helper methods

		/**
		 * Returns a boolean value indicating if the given string $haystack starts with the string $needle
		 * 
		 * @link http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
		 * @since 1.2
		 */
		function startsWith( $haystack, $needle ) {
			return $needle === "" || strpos( $haystack, $needle ) === 0;
		}

		/**
		 * Returns a boolean value indicating if the given string $haystack ends with the string $needle
		 * 
		 * @link http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
		 * @since 1.2
		 */
		function endsWith( $haystack, $needle ) {
			return $needle === "" || substr( $haystack, -strlen( $needle ) ) === $needle;
		}
	}
}

/**
 * Creates a new instance of the DatabaseBrowser class and ensures it will be loaded in the admin area
 * 
 * @since 1.0
 */
if ( class_exists( 'DatabaseBrowser' ) && function_exists( 'add_action' ) ) {
	$databasebrowser = new DatabaseBrowser();
	add_action( 'admin_menu', array( &$databasebrowser, 'on_admin_menu' ) );
	add_action( 'admin_init', array( &$databasebrowser, 'on_admin_init' ) );
}