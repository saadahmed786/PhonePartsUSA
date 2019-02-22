<?php
define('PREVNEXT_TITLE_PREVIOUS_PAGE','Click To Go Prev Position');
define('PREVNEXT_BUTTON_PREV','Prev');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE','No Prev Page');
define('PREVNEXT_TITLE_PAGE_NO','Page No : %s');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE','No Next Page');
define('PREVNEXT_TITLE_NEXT_PAGE','Click To Go Next Page');
define('PREVNEXT_BUTTON_NEXT','Next');

class splitPageResults {

	var $sql_query,$number_of_rows,$current_page_number,$number_of_pages,$number_of_rows_per_page,$page_url;

	function splitPageResults($db , $query, $max_rows,$page_url,$page ) {
		$this->sql_query = ($query);  // force the quesry to all lower case
		if (empty($page) || !is_numeric($page)) $page = 1;

		$this->current_page_number = $page;
		if($max_rows <= 0){
			$max_rows = '1';
		}

		$this->page_url = $page_url;

		$this->number_of_rows_per_page = $max_rows;

		$result_query = mysqli_query($db->conn, $this->sql_query);
		$this->number_of_rows = mysqli_num_rows($result_query);
		// echo $this->number_of_rows;
		$this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);

		if($this->current_page_number > $this->number_of_pages) {
			$this->current_page_number = $this->number_of_pages;
		}

		$offset = ($this->number_of_rows_per_page * ($this->current_page_number - 1));
		if ($offset < '0'){
			$offset = '1';
		}
		$this->sql_query .= " limit " . $offset . ", " . $this->number_of_rows_per_page;
	}


	// display split-page-number-links
	function display_links($max_page_links, $parameters = '') {
		global $PHP_SELF, $request_type;

		$display_links_string = '';

		if($max_page_links <= 0){
			$max_page_links = '1';
		}

		//$class = 'class="pageResults"';
		if($this->number_of_pages > 0) {

			if(!is_null($parameters) && (substr($parameters, -1) != '&')) $parameters .= '&';

			if ($this->current_page_number > 1)
			$display_links_string .= '<a href="'. $this->page_url . '?' . $parameters . 'page=' . ($this->current_page_number - 1) . '" class="pageResults" title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>' . PREVNEXT_BUTTON_PREV . '</u></a>&nbsp;&nbsp;';

			$cur_window_num = intval($this->current_page_number / $max_page_links);
			if($this->current_page_number % $max_page_links) $cur_window_num++;

			$max_window_num = intval($this->number_of_pages / $max_page_links);
			if($this->number_of_pages % $max_page_links) $max_window_num++;

			if($cur_window_num > 1)
			$display_links_string .= '<a href="' . $this->page_url . '?' . $parameters . 'page=' . (($cur_window_num - 1) * $max_page_links) . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>';


			for($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++) {
				if($jump_to_page == $this->current_page_number) {
					$display_links_string .= '&nbsp;<b>' . $jump_to_page . '</b>&nbsp;';
				}else{
					$display_links_string .= '&nbsp;<a href="' . $this->page_url . '?' . $parameters . 'page=' . $jump_to_page  . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' "><u>' . $jump_to_page . '</u></a>&nbsp;';
				}
			}

			// next window of pages
			if($cur_window_num < $max_window_num) $display_links_string .= '<a href="' . $this->page_url . '?' . $parameters . 'page=' . (($cur_window_num) * $max_page_links + 1) . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>&nbsp;';

			// next button
			if(($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1))
			$display_links_string .= '&nbsp;<a href="' . $this->page_url . '?' . $parameters . 'page=' . ($this->current_page_number + 1) . '" class="pageResults" title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' "><u>' . PREVNEXT_BUTTON_NEXT . '</u></a>&nbsp;';
		}
		else  {  // if zero rows, then simply say that
			$display_links_string .= '&nbsp;<b>0</b>&nbsp;';
		}

		return $display_links_string;
	}

	function display_count($text_output) {
		$to_num = ($this->number_of_rows_per_page * $this->current_page_number);

		if($to_num > $this->number_of_rows) $to_num = $this->number_of_rows;

		$from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

		if($to_num == 0) {
			$from_num = 0;
		}
		else{
			$from_num++;
		}

		return sprintf($text_output, $from_num, $to_num, $this->number_of_rows);
	}

	function display_i_count() {
		$to_num = ($this->number_of_rows_per_page * $this->current_page_number);

		if($to_num > $this->number_of_rows) $to_num = $this->number_of_rows;

		$from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

		if($to_num == 0) {
			$from_num = 0;
		}
		else{
			$from_num++;
		}

		return $from_num;
	}
}
?>