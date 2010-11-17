<html>
  <!-- $Id: _rawError.tpl 7539 2010-10-04 04:41:38Z john $ -->
  <head>
    <base href="http://<?php echo $_SERVER['HTTP_HOST'] ?>/<?php echo $base ?>" />
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
    <meta http-equiv="Content-Language" content="en-US" >

    <link href="<?php echo $base ?>/externals/styles/compat.css" media="screen" rel="stylesheet" type="text/css" >
    <link href="<?php echo $base ?>/externals/styles/styles.css" media="screen" rel="stylesheet" type="text/css" >
    <link href="<?php echo $base ?>/externals/styles/sdk.css" media="screen" rel="stylesheet" type="text/css" >
  </head>
  <body>
    <div class='topbar_wrapper'>
      <div class="topbar">
        <div class='logo'>
          <img src="<?php echo $base ?>/externals/images/socialengine_logo_admin.png" alt="" />
        </div>
      </div>
    </div>
    <div class='content'>
      <pre><?php
          if( APPLICATION_ENV == 'development' ) {
            echo $error;
          } else {
            echo $error->getMessage();
          }
        ?></pre>
      <p>
        For "cache_dir is not writable", set full permissions on temporary/cache
      </p>
    </div>
  </body>
</html>