<?php
	//CR Election portal
	require_once("config.php");
	session_start();
	function get_candidates() {
		global $DB;
		$query = mysqli_prepare($DB, "SELECT id, name FROM `candidates`");
		mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $id, $name);
		mysqli_stmt_store_result($query);
		$results = array();
		while(mysqli_stmt_fetch($query)){
			$results[$id] = $name;
		}
		return $results;
	}
	function allowed() {
		global $DB;
		$query = mysqli_prepare($DB, "SELECT meta_value FROM `meta` WHERE meta_name='vote_allowed'");
		mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $allowed);
		mysqli_stmt_store_result($query);
		mysqli_stmt_fetch($query);
		return $allowed == "1" ? true : false ;
	}
	
	$htmlOutput = '';
	
	if ( allowed() ) {
		$htmlOutput .= "<form action='vote.php' method='post'>";
		$candidates = get_candidates();
		if ( count($candidates) ) { 
			foreach ( $candidates as $id => $name ){
				$htmlOutput .= ("<label><input type='radio' name='candidate_id' value='".$id."' required >".$name."</label><br>" );
			}
			$htmlOutput .= "<input type='submit' value='Vote'></form>";
		}
		else {
			$htmlOutput .= "No Candidates in the list.";
		}
	}
	else {
		if ( isset( $_SESSION["done_voting"] ) && $_SESSION["done_voting"] ) {
			$htmlOutput .= "Your response has been recorded.<br><a href=''>Refresh</a>";
			unset($_SESSION["done_voting"]);
		}
		else {
			$htmlOutput .= "Not Allowed to vote. Ask for your right and <a href=''>Refresh</a>";
			$htmlOutput .= "<script>document.body.onload=function(){setTimeout(function(){setInterval(function(){window.location=''}, 500)},1000)}</script>";
		}
	}
	
	include('template.php');
