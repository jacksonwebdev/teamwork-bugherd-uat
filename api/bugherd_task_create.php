<?php

/**
 * Incoming webhook from Bugherd with bug payload to create UAT teamwork task ( with optional screenshot )
 */

// get json from bugherd webhook
$webhookResponse = file_get_contents('php://input');

// die if accessed without payload
if ( !$webhookResponse || $_GET['bid'] != '8937' ) {
	die();
}


$bug = json_decode( $webhookResponse, true );

$content = $bug['task']['description'];
$content = str_replace('"', "'", $content);
$truncated_content = substr($content,0,75).'...';
$priority = $bug['task']['priority'];

// meat & potatoes
$description  = '**General Info** \\n';
$description .= 'Bug: ' . $content . '\\n';
$description .= 'Site: ' . $bug['task']['site'] . '\\n';
$description .= 'URL: ' . $bug['task']['url'] . '\\n';
$description .= 'Bugherd Link: https://www.bugherd.com/projects/' . $bug['task']['project_id'] . '/tasks/' . $bug['task']['local_task_id'] . '\\n';

$description .= '**Requested Information**' . '\\n';
$description .= 'Requester OS: ' . $bug['task']['requester_os'] . '\\n';
$description .= 'Requester Browser: ' . $bug['task']['requester_browser'] . '\\n';
$description .= 'Requester Browser Size: ' . $bug['task']['requester_browser_size'] . '\\n';
$description .= 'Requester Browser Resolution: ' . $bug['task']['requester_resolution'] . '\\n';


if ( $bug['task']['screenshot_url'] ) {
	$description .= '**Screenshot Info**' . '\\n';
	$description .= 'Screenshot URL: ' . $bug['task']['screenshot_url'] .  '\\n';
	$description .= 'View in Bugherd for Pin Location: https://www.bugherd.com/projects/' . $bug['task']['project_id'] . '/tasks/' . $bug['task']['local_task_id'] . '\\n';
	$description .= '![Image](' . $bug['task']['screenshot_url'] . ')'. '\\n';
}

if( $priority == '2' || $priority == 'normal' ) {
	$priority = 'medium';
}
elseif( $priority == '3' || $priority == 'important' ) {
	$priority = 'high';
}
elseif( $priority == '4' || $priority == 'critical' ) {
	$priority = 'high';
}
else {
	$priority = 'low';
}

$teamwork_task_json = '{
  "todo-item": {
    "content": "' . $truncated_content . '",
    "description": "' . $description . '",
    "priority": "' . $priority . '",
    "tags": "bug,Bugherd,UAT"
  }
}';

// bring in teamwork functions
include('../php/class.uat.php');
$uat = new UAT();
// In our uat interface, we assign the Teamwork Task List ID (for the UAT list) to the Project List Name.
// So we need to retrieve that to post to the correct project's UAT task list.
$uat_task_list_id = $uat->get_TW_task_list_ID_from_BH_bug( $bug['task']['project_id'] );
if ( $uat_task_list_id != '' && is_numeric( $uat_task_list_id ) ){
	// send bug off to TW
	$response = $uat->create_teamwork_task( $uat_task_list_id, $teamwork_task_json );
}
else {
	$response = 'missing project id';
}

// debugging
//set the filename
$filename = 'webhook_log.txt';
$date = date_create();
$response = date_format($date, 'Y-m-d H:i:s') . ' => ' . $response;
file_put_contents('webhook_log.txt', $response.PHP_EOL , FILE_APPEND);
die();
