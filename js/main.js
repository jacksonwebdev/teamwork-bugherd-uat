// personally namespaced just in case, wrapped in an IIFE.
(function(cbwUAT, $, undefined ) {

	var cbw = cbwUAT;
	var ajax_url = 'php/ajax.php';
	var show_bugherd_code = false;

	// Document is now ready
	$(function() {
		console.log('hello');
		cbw.formEvents();
	});

	// handle ajax requests
	// params:
	//	obj: data to pass
	//	instance: current DOM item requested for UI
	cbw.ajaxRequest = function( obj, instance ){
		var item = instance.closest('.list-item');
		var loading = item.find('.loading');
		loading.toggleClass('show');

		$.ajax({ 
		    type: obj.type, 
		    url: ajax_url, 
		    data: obj.data,
		    dataType: 'json',
		    success: function (data) { 
		        
		        var success = true;
				var response = JSON.parse( data );
				// 200 request but API returns error
		        if ( response.error ) {
		        	window.alert( response.error );
		        	success = false;
				}
				// 200 request but API fails
		        else if ( response.hasOwnProperty( 'CONTENT' ) ) {
		        	window.alert( response.CONTENT.MESSAGE );
		        	success = false;
		        }
		        else {
			        if ( success == true ) {
				        if ( obj.data.action == 'create_teamwork_project_task_list' ) {
				        	$('#TW_task_list_ID').val( response.TASKLISTID );
				        }
				        else if ( obj.data.action == 'create_bugherd_project' ) {
				        	if ( response.hasOwnProperty( 'project' ) ) {
				        		var api_key = response.project.api_key;
				        		if ( api_key ) {
				        			show_bugherd_code = true;
					        		cbw.renderBugherdScriptCodeBlock( api_key );
					        	}
				        	}
				        }
				        else if( obj.data.action == 'get_bugherd_projects' ) {
				        	var bugherdProjects = $('#bugherdProjects');
				        	var options = '<option value=""></option>';
				        	bugherdProjects.html('');
				        	$.each(response.projects, function(i,item){
								var option = response.projects[i];
								var html = '<option value="' + option['id'] + '">' + option['name'] + '</option>';
								options += html;
							});
				        	bugherdProjects.html(options);
				        }
				    }
				}
				// UI processes to send user to next step in creating bugherd webhook
			    if ( success == true ) {
				    if ( !obj.ignore_success || obj.ignore_success == false ) {
				    	item.removeClass('disabled');
				    	item.toggleClass('finished');
				    }
				}
				
				// toggle loading class
				loading.toggleClass('show');
		    },
		    error: function(xhr, desc, err) {
				console.log(xhr);
				console.log("Details: " + desc + "\nError:" + err);
				loading.toggleClass('show');
			}
		});
	}

	// form events for submitting data
	cbw.formEvents = function() {

		// Creating a UAT task list
		$('#create-teamwork-uat-tasklist').submit(function(e) {
			e.preventDefault();
			var teamwork_project_id = $(this).find('#teamworkProjects').val();
			var args = {};
			args.type = 'POST';
			args.data = {
				'action': 'create_teamwork_project_task_list',
				'id': teamwork_project_id,
			};
			cbw.ajaxRequest( args, $(this) );
		});

		// Creating a UAT project in Bugherd
		$('#create-bugherd-uat-project').submit(function(e) {
			e.preventDefault();
			var bugherd_name = $(this).find('#newBugherdProjects').val();
			var TW_task_list_ID = $(this).find('#TW_task_list_ID').val();
			var args = {};
			args.type = 'POST';
			args.data = {
				'action': 'create_bugherd_project',
				'name': bugherd_name,
				'TW_task_list_ID': TW_task_list_ID
			};
			cbw.ajaxRequest( args, $(this) );
		});

		// Registering a webhook via the interface
		$('#create-bugherd-uat-create-task-webhook').submit(function(e) {
			e.preventDefault();
			var bugherd_project_id = $(this).find('#bugherdProjects').val();
			var args = {};
			args.type = 'POST';
			args.data = {
				'action': 'create_bugherd_uat_create_task_webhook',
				'id': bugherd_project_id
			};
			cbw.ajaxRequest( args, $(this) );
		});

		// Refreshing the list of Bugherd projects if new Bugherd project is created via interface
		$('#refresh-bugherd-projects').on('click', function(e ){
			e.preventDefault();
			var args = {};
			args.type = 'GET';
			args.data = {
				'action': 'get_bugherd_projects',
			};
			args.ignore_success = true;
			cbw.ajaxRequest( args, $(this) );
		});

	}

	// Render a Bugherd script block to be copy/pasted in head of staging site
	cbw.renderBugherdScriptCodeBlock = function( per_project_api_key ){
  		if ( show_bugherd_code ) {
	  		var pre_block = $('#bugherd_inject');
	  		var newline = "\n";
	  		var code = newline;
	  		code += "&lt;script type='text/javascript'&gt;" + newline;
	  		code 	+= "(function (d, t) {" + newline;
	  		code 	+= "var bh = d.createElement(t), s = d.getElementsByTagName(t)[0];" + newline;
	  		code 	+= "bh.type = 'text/javascript';" + newline;
	  		code 	+= "bh.src = 'https://www.bugherd.com/sidebarv2.js?apikey=" + per_project_api_key + "';" + newline;
	  		code 	+= "s.parentNode.insertBefore(bh, s);" + newline;
	  		code 	+= "})(document, 'script');" + newline;
	  		code += "&lt;/script&gt;" + newline + newline;

	  		pre_block.html( code );
	  	}
	}


}( window.cbwUAT = window.cbwUAT || {}, jQuery ) );

