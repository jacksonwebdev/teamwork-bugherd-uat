<?php

/**
 * UAT functions for Bugherd to Teamwork Zapier Bridge
 *
 * @since 1.0.0
 */
if( ! class_exists( 'UAT' ) ) :

	// https://github.com/php-mod/curl
	require('Curl.php'); 

	/**
	 * UAT Utility Class for API Requests
	 *
	 * @since 1.0.0
	 */
	class UAT {

		private $curl;

		private $bugherd_api_url = 'https://www.bugherd.com';
		private $bugherd_api_key = 'XXXXXXXXXXXXXXXXX';
		private $bugherd_site_webhook_base = 'XXXXXXXXXXXXXXXXXX';
		private $bugherd_requester_email = 'support@somewebsite.com';
		private $bugherd_assigned_to_email = 'support@somewebsite.com';
		

		private $teamwork_api_url = 'XXXXXXXXXXXXXXXXX';
		private $teamwork_api_token = 'XXXXXXXXXXXXXXXXX';
		private $teamwork_bugherd_list_id_append_format = ' ##';

		private $teamwork_uat_task_list_name = 'UAT - Bugherd';
		private $teamwork_uat_task_list_description = 'Bugherd Bugs auto sent to TW.';
		 
		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->curl = new Curl\Curl();
		}

		/**
		 * Post request to API of choice with basic Auth
		 *
		 * @since 1.0.0
		 */
		public function post_raw_json_curl( $url, $data_json, $headers, $auth_token ) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_USERPWD, base64_encode( $auth_token . ':xxx' ));
			$response  = curl_exec($ch);
			curl_close($ch);
			return $response;
		}

		/**
		 * Get Teamwork Projects via API
		 *
		 * @since 1.0.0
		 */
		public function get_teamwork_projects() {
			$request = '/projects.json';
			$url = $this->teamwork_api_url . $request;
			$this->curl->setBasicAuthentication( $this->teamwork_api_token, 'xxx');
			$response = $this->curl->get( $url, array(
			    'status' => 'ACTIVE',
			));
			return $response->response;
		}

		/**
		 * Get Single Bugherd Project via API
		 *
		 * @since 1.0.0
		 */
		public function get_bugherd_project( $project_id ) {
			$request = '/api_v2/projects/' . $project_id . '.json';
			$url = $this->bugherd_api_url . $request;
			$this->curl->setBasicAuthentication( $this->bugherd_api_key, 'xxx');
			$response = $this->curl->get( $url );
			return $response->response;
		}

		/**
		 * Get All Bugherd Projects via API
		 *
		 * @since 1.0.0
		 */
		public function get_bugherd_projects() {
			$request = '/api_v2/projects.json';
			$url = $this->bugherd_api_url . $request;
			$this->curl->setBasicAuthentication( $this->bugherd_api_key, 'xxx');
			$response = $this->curl->get( $url );
			return $response->response;
		}

		/**
		 * Get All Bugherd Webhooks
		 *
		 * @since 1.0.0
		 */
		public function get_bugherd_webhooks() {
			$request = '/api_v2/webhooks.json';
			$url = $this->bugherd_api_url . $request;
			$this->curl->setBasicAuthentication( $this->bugherd_api_key, 'xxx');
			$response = $this->curl->get( $url );
			return $response->response;
		}

		/**
		 * Post Request to Create Teakwork Task List
		 * 
		 * @var string The teamwork project list ID
		 *
		 * @since 1.0.0
		 */
		public function create_teamwork_project_task_list( $project_list_id ) {
			$project_list = '{
			  "todo-list": {
			    "name": "' . $this->teamwork_uat_task_list_name . '",
			    "private": false,
			    "pinned": true,
			    "milestone-id": "",
			    "description": "' . $this->teamwork_uat_task_list_description . '",
			    "todo-list-template-id": ""
			  }
			}';
			$request = '/projects/' . $project_list_id . '/tasklists.json';
			$url = $this->teamwork_api_url . $request;
			$headers = array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($project_list),
				'Authorization: Basic ' . base64_encode($this->teamwork_api_token . ':xxx') 
			);
			$response = $this->post_raw_json_curl( $url, $project_list, $headers, $this->teamwork_api_token );
			return $response;
		}

		/**
		 * Post Request to Create Teamwork Task
		 * 
		 * @var string The teamwork task list ID
		 * @var string The JSON string to insert into task description
		 *
		 * @since 1.0.0
		 */
		public function create_teamwork_task( $task_list_id, $json_payload ) {
			$request = '/tasklists/' . $task_list_id . '/tasks.json';
			$url = $this->teamwork_api_url . $request;
			$headers = array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($json_payload),
				'Authorization: Basic ' . base64_encode($this->teamwork_api_token . ':xxx') 
			);
			$response = $this->post_raw_json_curl( $url, $json_payload, $headers, $this->teamwork_api_token );
		
			return $response;
		}

		/**
		 * Post Request to Create Bugherd Project
		 * 
		 * @var string The input iname for a Bugherd Project
		 * @var string The Teamwork Task List ID
		 *
		 * @since 1.0.0
		 */
		public function create_bugherd_project( $name, $TW_tasklist_id ) {
			$append = $this->teamwork_bugherd_list_id_append_format . $TW_tasklist_id;
			$project = '{"project":{
						  "name":"' . $name . $append . '",
						  "devurl":"https://uat.coolblueweb.com"
						}}';
			$request = '/api_v2/projects.json';
			$url = $this->bugherd_api_url . $request;
			$headers = array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($project),
				'Authorization: Basic ' . base64_encode($this->bugherd_api_key . ':xxx') 
			);
			$response = $this->post_raw_json_curl( $url, $project, $headers, $this->bugherd_api_key );
			return $response;
		}

		/**
		 * Post Request to create Bugherd Webhook
		 * 
		 * @var string The Bugherd Project ID
		 *
		 * @since 1.0.0
		 */
		public function create_bugherd_uat_create_task_webhook( $bugherd_project_id ) {
			$webhook = '{
			  "project_id":' . (int)$bugherd_project_id . ',
			  "target_url": "' . $this->bugherd_site_webhook_base . '/api/bugherd_task_create.php?bid=8937",
			  "event":"task_create"
			}';

			$request = '/api_v2/webhooks.json';
			$url = $this->bugherd_api_url . $request;
			$headers = array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($webhook),
				'Authorization: Basic ' . base64_encode($this->bugherd_api_key . ':xxx') 
			);
			$response = $this->post_raw_json_curl( $url, $webhook, $headers, $this->bugherd_api_key );
			return $response;
		}

		/**
		 * Get Teamwork Task List ID Referenced in Bugherd List
		 * 
		 * When creating the Bugherd Project List, we append it with -TWTL{{_number_}} so we can reference the task list ID.
		 * So we need to grab that to post a bugherd bug payload over to a specific task list in TW.
		 *
		 * @var string The Bugherd Project ID
		 * 
		 * @since 1.0.0
		 */
		
		public function get_TW_task_list_ID_from_BH_bug( $bugherd_project_id ){
			// {
			// 	"project": {
			// 		"id": 140486,
			// 		"name": "Test Payload Example",
			// 		"devurl": "https://some_url.biz",
			// 		"api_key": "XXXXXXXXXXXXXXX",
			// 		"is_active": true,
			// 		"is_public": null,
			// 		"members": [],
			// 		"guests": []
			// 	}
			// }
			$project = $this->get_bugherd_project( $bugherd_project_id );
			$project = json_decode( $project, true );
			if( empty( $project ) ) {
				return '';
			}
			else {
				$project_name = $project['project']['name'];
				$index = strpos($project_name, $this->teamwork_bugherd_list_id_append_format) + strlen($this->teamwork_bugherd_list_id_append_format);
				$task_list_id = substr($project_name, $index);
				return $task_list_id;
				if ( is_numeric( $task_list_id ) ) {
					return $task_list_id;
				}
				else {
					return '';
				}
			}
		}

	}
endif;