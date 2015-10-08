<?php
	include( '../../../wp-load.php' );
	$fdObj = new FreshDeskAPI();
	//echo '<xmp>'; print_r($fdObj); echo '</xmp>';
	//echo '<xmp>'; print_r($_POST); echo '</xmp>';
	//die;
	$postArray = $_POST;
	$action = $_POST['action'];
	$returnArray = array();
	switch( $action ){
		case 'filter':
			$filteredTickets = $fdObj->filter_tickets( $postArray['tickets'], $postArray['key'] );
			$returnArray = $fdObj->get_html( $filteredTickets );
			break;
		case 'search':
			$filteredTickets = $fdObj->search_tickets( $postArray['tickets'], $postArray['key'] );
			$returnArray = $fdObj->get_html( $filteredTickets );
			break;
		default:
			$returnArray = '<p>Error!</p>';
			break;
	}
	echo json_encode( $returnArray ); die;
	
?>