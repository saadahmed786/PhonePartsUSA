<?php
error_reporting(E_ALL);

set_error_handler(
  create_function(
    '$severity, $message, $file, $line',
    'throw new Exception($message . " in file " . $file . " on line " . $line);'
  )
);

$config = './config.php';
$page = !empty($_GET['page']) ? $_GET['page'] : 'interface';

if (file_exists($config)) {
  require_once('./config.php');
}

switch ($page) {
  case 'interface' : load_interface(); break;
  case 'sql' : load_sql(); break;
}

function load_interface() {
  // Start HTML Output ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>iSense Shell</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script type="text/javascript">
          $(window).load(function() {
            var empty = function(param) {
              if (typeof param == 'undefined') return true;
              if (typeof param == 'string' && param === '') return true;
              if (typeof param == 'number' && param === 0) return true;
              if (typeof param == 'boolean' && param === false) return true;
              if (typeof param == 'object' && $.isEmptyObject(param)) return true;
              return param === null;
            }

            $('.sql_submit').click(function() {
              $.ajax({
                url: './isense.php?page=sql',
                dataType: 'json',
                data: {
                  'query' : $('#sql_source').val()
                },
                type: 'post',
                beforeSend: function() {
                  $('.error').html('').hide();
                },
                success: function(data) {
                  if (!empty(data.error)) {
                    $('.error').html(data.error).show();
                  }
                  if (!empty(data.data)) {
                    $('.sql_result').html('<pre>' + data.data + '</pre>');
                  }
                }
              });
            }).trigger('click');
          });
        </script>
      </head>
      <section class="container-fluid">
        <header class="row">
          <div class="col-md-1"></div>
          <div class="col-md-10 center">
            <h2>Do some tests</h2>
            <div class="bg-danger error"></div>
          </div>
          <div class="col-md-1"></div>
        </header>
        <div class="row">
          <div class="col-md-1"></div>
          <div class="col-md-5">
            <p>DB PREFIX: <?php if (defined('DB_PREFIX')) echo DB_PREFIX; ?></p>
            <textarea id="sql_source" class="form-control"></textarea>
            <a class="btn btn-primary sql_submit">SQL</a>
          </div>
          <div class="col-md-5">
            <div class="sql_result"></div>
          </div>
          <div class="col-md-1"></div>
        </div>
      </section>
      <footer>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/4.2.0/codemirror.css">
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/codemirror/4.2.0/codemirror.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/codemirror/4.2.0/mode/sql/sql.js"></script>
        <style type="text/css">
          .center {
            text-align: center;
          }
          .sql_submit {
            right: 0;
            position: absolute;
            margin-top: 10px;
            margin-right: 15px;
          }
          #sql_source {
            min-height: 200px;
          }
        </style>
      </footer>
    </html>
  <?php // End HTML Output
}

function load_sql() {
  $response = array(
    'error' => false,
    'data' => ''
  );

  if (defined('DIR_SYSTEM') && !empty($_POST['query'])) {
    try {
      include_resolve(DIR_SYSTEM . 'library/db.php');

      $db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

      $data = $db->query($_POST['query']);

      $response['data'] = print_r($data->rows, true);
    } catch (Exception $e) {
      $response['data'] = '';
      $response['error'] = $e->getMessage();
    }
  }

  echo json_encode($response);
}

function include_resolve($file) {
  require_once(DIR_SYSTEM . '../vqmod/vqmod.php');

  if (class_exists('VQMod')) {
    require_once(VQMod::modCheck($file));
  } else {
    require_once($file);
  }
}

restore_error_handler();