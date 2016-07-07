<?
	//------------------------------------------------------------------------------------------
	function process_func() {
	//------------------------------------------------------------------------------------------
		global $APP;
		
		if(!isset($_GET['target']))
			return proc_error('Invalid function call');
		
		switch($_GET['target']) {
			case LINKED_ITEM_HTML:
				process_linked_item_html();
				return;
				
			case GET_SHAREABLE_QUERY_LINK:
				get_sharable_query_link();
				return;
		}
		
		if(!isset($APP['additional_callable_plugin_functions']) 
			|| !in_array($_GET['target'], $APP['additional_callable_plugin_functions']))
		{
			return proc_error('Invalid function call');
		}
		
		// call func.
		$_GET['target']();
	}
	
	//------------------------------------------------------------------------------------------
	function process_linked_item_html() {
	//------------------------------------------------------------------------------------------
		global $TABLES;
		if(!isset($_GET['table']) || !isset($TABLES[$_GET['table']]) 
			|| !isset($_GET['field']) || !isset($TABLES[$_GET['table']]['fields'][$_GET['field']])
			|| !isset($_GET['self_id'])
			|| !isset($_GET['other_id'])
			|| !isset($_GET['label'])
			|| !isset($_GET['parent_form']))
		{
			return proc_error('Parameter(s) missing or invalid');
		}
		$table = $TABLES[$_GET['table']];				
		
		$can_edit = count($table['primary_key']['columns']) == 1 // currently not possible to do inline edit in table with composite key
			&& has_additional_editable_fields($table['fields'][$_GET['field']]['linkage']);				
		
		echo get_linked_item_html($_GET['parent_form'], $table, $_GET['table'], $_GET['field'], $can_edit, $_GET['other_id'], $_GET['label'], $_GET['self_id']);
	}
	
	//------------------------------------------------------------------------------------------
	function get_sharable_query_link() {
	//------------------------------------------------------------------------------------------
		header('Content-Type: text/plain');
		define('QUERYPAGE_NO_INCLUDES', 1);
		require_once 'query.php';
		
		$stored_query = QueryPage::store_query($error_msg);
		if($stored_query === false) {
			echo 'Error: ' . $error_msg;
			return;
		}
		
		echo '?' . http_build_query(array(
			'mode' => MODE_QUERY,
			'id' => $stored_query
		));
	}
?>