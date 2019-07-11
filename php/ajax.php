<?php

/**
 * Ajax functions for UAT form
 */

 
require_once 'class.uat.php';
$uat = new UAT();
$result = 'lol';

if ( $_GET['action'] == 'get_bugherd_projects'){
	$result = $uat->get_bugherd_projects();
}
else {

	if( $_POST['action'] == 'create_teamwork_project_task_list' ) {
	     $id = $_POST['id'];
	     $result = $uat->create_teamwork_project_task_list( $id );
	}
	elseif( $_POST['action'] == 'create_bugherd_project' ) {
	     $name = $_POST['name'];
	     $TW_task_id = $_POST['TW_task_list_ID'];
	     if ( !isset($TW_task_id) || $TW_task_id == '' ) {
	     	$err = array(
	     		'error' => 'Missing TeamWork Task List ID. We cannot continue, find Daniel.'
	     	);
	     	echo json_encode( $err );
	     	die();
	     }
	     $result = $uat->create_bugherd_project( $name, $TW_task_id );
	}
	elseif( $_POST['action'] == 'create_bugherd_uat_create_task_webhook' ) {
	     $id = $_POST['id'];
	     $result = $uat->create_bugherd_uat_create_task_webhook( $id );
	}

}

echo json_encode( $result );
die();