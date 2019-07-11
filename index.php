<?php 

include_once('php/class.uat.php'); 
$helper = new UAT();
$teamwork_projects = json_decode( $helper->get_teamwork_projects(), true );
$teamwork_projects = $teamwork_projects['projects'];
$bugherd_projects = json_decode( $helper->get_bugherd_projects(), true );
$bugherd_projects = $bugherd_projects['projects'];
$bugherd_webhooks = json_decode( $helper->get_bugherd_webhooks(), true );

?>


<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>CBW - UAT - ᕕ( ᐛ )ᕗ</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="manifest" href="site.webmanifest">
  <link rel="apple-touch-icon" href="https://coolblueweb.com/wp-content/themes/coolblueweb/assets/images/favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" href="https://coolblueweb.com/wp-content/themes/coolblueweb/assets/images/favicons/favicon-32x32.png" sizes="32x32">

  <link rel="stylesheet" href="css/normalize.css">

  <!-- milligram -->
  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
  <link rel="stylesheet" href="//cdn.rawgit.com/necolas/normalize.css/master/normalize.css">
  <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
  <!-- milligram -->

  <!-- custom -->
  <link rel="stylesheet" href="css/main.css">
  <!-- custom -->
  

</head>

<body>
  <!--[if lte IE 9]>
    <p class="browserupgrade">Lol You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->


  <div class="container">
    <div class="row full">
      <div class="column"><h3>UAT Bugherd to Teamwork Bridge</h3></div>
      <div class="column">
        <p>
          Hello, this page provides the tools needed to allow for auto submitting bugs from <a href="https://www.bugherd.com/organizations/46284" target="_blank" title="Bugherd">Bugherd</a> to <a href="https://coolsupport.teamwork.com/" target="_blank" title="Teamwork">Teamwork</a>.
          <br>
          Follow the steps below, afterwhich include the <a href="https://support.bugherd.com/hc/en-us/articles/204171450-Installing-the-Script" target="_blank" title="Bugherd">Bugherd Javascript Sidebar</a> so clients can submit bugs.
        </p>
      </div>
    </div>

    <hr />
    <ol>
      <li class="list-item ease">
        <h5>Create a Teamwork UAT Tasklist</h5>
        <div class="row">
          <div class="column">
            <div class="loading ease">
              <p>ᕕ( ᐛ )ᕗ<br>Loading...</p>
            </div>
            <p>Select a Project to create a UAT Task List for.</p>
            <form id="create-teamwork-uat-tasklist">
              <fieldset>
                <label for="teamworkProjects">Teamwork Projects</label>
                <select id="teamworkProjects">
                  <option value=""></option>
                  <?php foreach( $teamwork_projects as $project ) { ?>

                    <option value="<?php echo $project['id']; ?>"><?php echo $project['name']; ?></option>

                  <?php } ?>
                </select>
                <input class="button-primary" type="submit" value="Create List">
              </fieldset>
              <div class="button alert ease success">Good Job</div>
              <div class="button alert ease error">Error :(</div>
            </form>
          </div>
          <hr />
        </div>
      </li>

      <li class="list-item disabled ease">
        <h5>Create a Bugherd Project List</h5>
        <div class="row">
          <div class="column">
            <div class="loading ease">
              <p>ᕕ( ᐛ )ᕗ<br>Loading...</p>
            </div>
            <p>Enter a Client Name for your Bugherd Project.</p>
            <form id="create-bugherd-uat-project">
              <fieldset>
                <label for="newBugherdProjects">New Project Name:</label>
                <input id="newBugherdProjects" name="newBugherdProjects" type="text" placeholder="New Project Name:" />
                <input id="TW_task_list_ID" type="hidden" value="" />
                <input class="button-primary" type="submit" value="Create List">
              </fieldset>
              <div class="button alert ease success">Good Job</div>
              <div class="button alert ease error">Error :(</div>
            </form>
          </div>
          <hr />
        </div>
      </li>

      <li class="list-item disabled ease">
        <h5>Create a Bugherd Webhook For A Specific List</h5>
        <div class="row">
          <div class="column">
            <div class="loading ease">
              <p>ᕕ( ᐛ )ᕗ<br>Loading...</p>
            </div>
            <p>
              This webhook will be registered per project list, to tell Bugherd to send any new tasks to this URL so we can ship it off to Teamwork.
              <br>
              If you created a new Bugherd List above, click refresh.
            </p>
            <a href="#" id="refresh-bugherd-projects" class="button blue refresh">Refresh</a>
            <form id="create-bugherd-uat-create-task-webhook">
              <fieldset>
                <label for="bugherdProjects">Bugherd Projects</label>
                <select id="bugherdProjects">
                  <option value=""></option>
                  <?php foreach( $bugherd_projects as $project ) { ?>

                    <option value="<?php echo $project['id']; ?>"><?php echo $project['name']; ?></option>

                  <?php } ?>
                </select>
                <input class="button-primary" type="submit" value="Create Webhook">
              </fieldset>
              <div class="button alert ease success">Good Job</div>
              <div class="button alert ease error">Error :(</div>
            </form>
          </div>
        </div>
      </li>

      <li class="list-item disabled ease">
        <h5>Afterward:</h5>
        <div class="row">
          <div class="column">
            <p>
              ᕕ( ᐛ )ᕗ <br> <br>
              Now you can copy / paste this code block into your project's header.php file so clients can start submitting Bugs.
            </p>
            <pre id="bugherd_inject"></pre>
                
            <p>
              <b>**</b>Be sure to tell your client they should install the Bugherd Screenshot Extension. <br>
              The client UAT document can also be found => <a class="button" href="/doc/bugherd-2018.pdf" target="_blank">here</a>
            </p>
          </div>
        </div>
      </li>

  </ol>

  </div>



  <!-- Add your site or application content here -->
  <script src="js/vendor/modernizr-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-3.3.1.min.js"><\/script>')</script>
  <script src="js/plugins.js"></script>
  <script src="js/main.js"></script>
  
</body>

</html>
