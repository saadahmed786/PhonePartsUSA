<?php
class ModelModuleUberCSV extends Model {

	public function getTables() {
		$table_data = array();

		$query = $this->db->query("SHOW TABLES FROM `" . DB_DATABASE . "`");

		foreach ($query->rows as $result) {
			$table_data[] = $result['Tables_in_' . DB_DATABASE];
		}

		return $table_data;
	}

	public function getColumns($table) {
		$columns = array();

		$query = $this->db->query("SHOW COLUMNS FROM `$table`");

		foreach ($query->rows as $result) {
			$columns[] = $result['Field'];
		}

		return $columns;
	}

	public function csvExport($data) {

		if (empty($data['csv_export_table'])) {
			return array('error' => 'error_no_table');
		}

		if (empty($data['csv_export_columns'])) {
			return array('error' => 'error_no_columns');
		}

		$table = $data['csv_export_table'];
		$columns = $data['csv_export_columns'];

		$limit = '';
		if ((int)$data['start_row'] && (!(int)$data['end_row'] || $data['end_row'] <= $data['start_row'])) {
			$rowcount = $this->db->query("SELECT count(*) FROM `" . $table . "`");
			$data['end_row'] = $rowcount->row['count(*)'];
			$limit = "LIMIT " . (int)$data['start_row'] . ", " . (int)$data['end_row'];
		} elseif ((int)$data['end_row'] && !(int)$data['start_row']) {
			$data['start_row'] = 0;
			$limit = "LIMIT " . (int)$data['start_row'] . ", " . (int)$data['end_row'];
		} elseif ((int)$data['start_row'] && (int)$data['end_row']) {
			$limit = "LIMIT " . (int)$data['start_row'] . ", " . (int)$data['end_row'];
		}


		$output 	= '';
	    //$query 		= "SELECT * FROM " . DB_PREFIX . $table;

		// Get Primary Key for table
		$table_index_info = $this->db->query("show index from `$table` where Key_name = 'PRIMARY';");
		$primary_columns = array();
		foreach ($table_index_info->rows as $table_index_rows) {
			$primary_columns[] = $table_index_rows['Column_name'];
		}

		// Compare columns to see if primary key is found. Supports multiple keys. Add to front if not found.
		// If no columns are passed in, then it will just add the primary key and export that.
		if (($diff_result = array_diff($primary_columns, $columns))) {
			rsort($diff_result);
			foreach ($diff_result as $diff) {
				array_unshift($columns, $diff);
			}
		}

		//if ($columns) {
			$query = "SELECT `" . implode("`,`", $columns) . "` FROM `" . $table . "` $limit"; // prefix already part of the table name being passed in
		//} else {
		//	$query = "SELECT * FROM `" . $table . "`"; // prefix already part of the table name being passed in
		//}
	    $result 	= $this->db->query($query);
	    $columns 	= array_keys($result->row);

		$csv_terminated = "\n";
	    $csv_separator = ",";
	    $csv_enclosed = '"';
	    $csv_escaped = "\\"; //linux
		$csv_escaped = '"';

		// Header Row
	 	$output .= '"' . $table . '.' . stripslashes(implode('","' . $table . '.',$columns)) . "\"\n";

	 	// Data Rows
	    foreach ($result->rows as $row) {
			//$output .= '"' . html_entity_decode(implode('","', str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, str_replace(array("\r","\n","\t"), "", $row))), ENT_COMPAT, "utf-8") . "\"\n";

			$schema_insert = '';
			$fields_cnt = count($row);
			foreach ($row as $k => $v) {
		        if ($row[$k] == '0' || $row[$k] != '') {
		            if ($csv_enclosed == '') {
		                $schema_insert .= $row[$k];
		            } else {
		            	$row[$k] = str_replace(array("\r","\n","\t"), "", $row[$k]);
		            	$row[$k] = html_entity_decode($row[$k], ENT_COMPAT, "utf-8");
		                $schema_insert .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$k]) . $csv_enclosed;
		            }
		        } else {
		            $schema_insert .= '';
		        }

		        if ($k < $fields_cnt - 1) {
		            $schema_insert .= $csv_separator;
		        }
		    }

		    $output .= $schema_insert;
		    $output .= $csv_terminated;

	    }

	    return $output;
	}

	public function csvImport($file, $truncate = false) {

		ini_set('max_execution_time', 999999);

	    $handle = fopen($file,'r');
	    if(!$handle) die('Cannot fopen uploaded file.');

		// Get Table name and Columns from header row
		$columns = array();
		$data = fgetcsv($handle, 1000, ",");
		// If the first line is blank, try second line
		if (!$data[0]) {
			$data = fgetcsv($handle, 1000, ",");
		}

		foreach ($data as $d) {
			if (strpos($d, '.') !== false) {
				$tmp = explode('.', $d);
				$table = $tmp[0];
				$columns[] = $tmp[1];
			} else {
				$columns[] = $d;
			}
		}

		if (!isset($table)) {
			exit('Could not retrieve table.');
		}

		// Determine if this is insert or update depending on if the primary key column already exists
		$table_index_info = $this->db->query("show index from $table where Key_name = 'PRIMARY';");

		//check if table exists here. Need custom err handler

		// Loop through columns to see if primary key is found. Supports multiple keys
		$key_count = 0;
		$primary_column_indexes = array();
		$default_update_where_clause = " WHERE ";
		foreach ($columns as $column_idx => $column) {
			foreach ($table_index_info->rows as $table_index_rows) {
				if ($column == $table_index_rows['Column_name']) {
					$key_count++;

					// Add column index to list of column indexes for where clauses on update
					$primary_columns[$column_idx] = $column;
					$default_update_where_clause .= "`" . $column . "` = '{".$column."}' AND";

					// Only break if we found all primary keys
					if ($key_count == $table_index_info->num_rows) {
						break 2;
					}
				}
			}
		}
		$default_update_where_clause = preg_replace('/ AND$/', '', $default_update_where_clause);

		// If all primary keys not found, then don't import
		if ($key_count != $table_index_info->num_rows) {
			return array('error' => 'error_primary_keys');
		}

		// Date Format Check
		$pattern = '/\A\d{1,2}\/\d{1,2}\/\d{4}/';
		$pattern2 = '/\A\d{1,2}\-\d{1,2}\-\d{4}/';

		$sql_update = "";
		$sql_insert = "INSERT INTO `" . $table . "` (". implode(',',$columns) .") VALUES(";

		$update_rows = array();
		$update_row_count = 0;

		$insert_rows = array();
		$insert_row_count = 0;

		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

			// Trim data down to column count so they line up. Any excess columns will be cut off
			if (count($data) > count($columns)) {
				$data = array_slice($data, 0, count($columns));
			}

			// Loop through primary keys and update them in the where clause
			$update_where_clause = $default_update_where_clause;
			foreach ($primary_columns as $j => $pricol) {
				$update_where_clause = str_replace('{'.$pricol.'}', $data[$j], $update_where_clause);
			}

			// Check to see if primary key exists to determine if update or insert, unless truncate is checked
			$bUpdate = false;
			if ($truncate) {
				// Moved down to end
			} else {
				$record_info = $this->db->query("SELECT count(*) FROM `$table` " . $update_where_clause);
				if ((int)$record_info->row['count(*)']) { // Update
					$bUpdate = true;
				}
			}

			if ($bUpdate) { // Update

				// Update Code
				$update_row_count++;
				$update_rows[$update_row_count] = "UPDATE `" . $table . "` SET ";
				$primary_columns_keys = array_keys($primary_columns);
				// Clean and Format data
				foreach($data as $k => $value) {
					// Assumes the main primary key is always the first array index based on Seq_in_index listing
					//if ($k == $primary_columns_keys[0] && in_array($columns[$k], $primary_columns)) { continue; }
					if ($k == $primary_columns_keys[0]) { continue; }

					$date_matches = '';
					$test = preg_match_all($pattern, $value, $date_matches);
					$test2 = preg_match_all($pattern2, $value, $date_matches);
					if ($test || $test2) {
						$value = "DATE('" . $this->db->escape(date("Y-m-d", strtotime($value))) . "')";
					} else {
					    $value = "'" . $this->db->escape($value) . "'";
					}
					$update_rows[$update_row_count] .= "`" . $columns[$k] . "` = $value,";
				}

				// Trim excess commas
				$update_rows[$update_row_count] = trim($update_rows[$update_row_count], ",");

				// Add where clause
				$update_rows[$update_row_count] .= $update_where_clause;
				$update_rows[$update_row_count] .= ";";
			} else { // Insert

				// Insert Code
				$insert_row_count++;

				// Clean and Format data
				foreach($data as $k => $value) {
					$date_matches = '';
					$test = preg_match_all($pattern, $value, $date_matches);
					$test2 = preg_match_all($pattern2, $value, $date_matches);
					if ($test || $test2) {
						$data[$k] = "DATE('" . $this->db->escape(date("Y-m-d", strtotime($value))) . "')";
					} else {
					    $data[$k] = "'" . $this->db->escape($value) . "'";
					}
				}
				$insert_rows[$insert_row_count] = htmlentities(implode(",",$data), ENT_NOQUOTES, 'UTF-8');
			}

		}
		fclose($handle);


		// Combine Update and Insert queries
		if ($update_row_count || $insert_row_count) {
			$sql_combined = '';
			if ($update_row_count) {
				foreach ($update_rows as $row) {
					$sql_combined .= $row;
				}
			}
			if ($insert_row_count) {
				$sql_insert .= implode("),(", $insert_rows);
			    $sql_insert .= ");";
				$sql_combined .= $sql_insert;
			}

			if ($truncate) {
				$this->db->query("TRUNCATE TABLE " . $table);
			}
			//$this->db->query($sql_combined);
			$this->csv_mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, $sql_combined);
			$this->cache->delete('product');
			return array('updated' => $update_row_count, 'inserted' => $insert_row_count);
		} else {
			return array('error' => 'error_no_rows');
		}
	}

	private function validDate($date){
		//replace / with - in the date
		$date = strtr($date,'/','-');
		//explode the date into date,month and year
		$datearr = explode('-', $date);
		//count that there are 3 elements in the array
		if(count($datearr) == 3){
			list($d, $m, $y) = $datearr;
			/*checkdate - check whether the date is valid. strtotime - Parse about any English textual datetime description into a Unix timestamp. Thus, it restricts any input before 1901 and after 2038, i.e., it invalidate outrange dates like 01-01-2500. preg_match - match the pattern*/
			if(checkdate($m, $d, $y) && strtotime("$y-$m-$d") && preg_match('#\b\d{2}[/-]\d{2}[/-]\d{4}\b#', "$d-$m-$y")) { /*echo "valid date";*/
				return TRUE;
			} else {/*echo "invalid date";*/
				return FALSE;
			}
		} else {/*echo "invalid date";*/
			return FALSE;
		}
		/*echo "invalid date";*/
		return FALSE;
	}

	private function IsValidDate($date){
		//replace / with - in the date
		$date = strtr($date,'/','-');
		//explode the date into date,month and year
		$datearr = explode('-', $date);
		//count that there are 3 elements in the array
		if(count($datearr) == 3){
			list($d, $m, $y) = $datearr;
			/*checkdate - check whether the date is valid. strtotime - Parse about any English textual datetime description into a Unix timestamp. Thus, it restricts any input before 1901 and after 2038, i.e., it invalidate outrange dates like 01-01-2500. preg_match - match the pattern*/
			if(checkdate($m, $d, $y) && strtotime("$y-$m-$d") && preg_match('#\b\d{2}[/-]\d{2}[/-]\d{4}\b#', "$d-$m-$y")) { /*echo "valid date";*/
				return TRUE;
			} else {/*echo "invalid date";*/
				return FALSE;
			}
		} else {/*echo "invalid date";*/
			return FALSE;
		}
		/*echo "invalid date";*/
		return FALSE;
	}

	// Since mysql query doesn't support multiple queries in a single call, using my own mysqli multi_query call
	private function csv_mysqli($hostname, $username, $password, $database, $sql) {
		$csvlink = new mysqli($hostname, $username, $password, $database);

		if (mysqli_connect_error()) {
			throw new ErrorException('Error: Could not make a csv database link (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
		}

		$csvlink->set_charset("utf8");
		$csvlink->query("SET SQL_MODE = ''");

		mysqli_multi_query($csvlink, $sql);

		if (!$csvlink->errno){
			return true;
		} else {
			throw new ErrorException('Error CSV DB MultiQuery: ' . $csvlink->error . '<br />Error No: ' . $csvlink->errno . '<br />' . $sql);
			exit();
		}


	}



}
?>