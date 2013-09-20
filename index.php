<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
  <link rel="stylesheet" href="<?php bloginfo('template_directory') ?>/css/normalize.css">
  <style type="text/css" media="screen">
    @import url( <?php bloginfo('stylesheet_url'); ?> );
  </style>
  <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
</head>
<body>

  <script type="text/x-handlebars">
    <div class="navbar">
      <div class="navbar-inner">
        <a class="brand" href="#">Personal Blog of Rob Williams</a>
        <ul class="nav">
          <li>{{#link-to 'posts'}}Something{{/link-to}}</li>
        </ul>
      </div>
    </div>

    {{outlet}}
  </script>

  <script type="text/x-handlebars" id="posts">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span3">
          <table class='table'>
            <thead>
              <tr><th>Recent Posts</th></tr>
            </thead>
            {{#each model}}
            <tr><td>
                {{#link-to 'post' this}}{{post_title}} <small class='muted'>{{terms.[0].name}}</small>{{/link-to}}
            </td></tr>
            {{/each}}
          </table>
        </div>
        <div class="span9">
          {{outlet}}
        </div>
      </div>
    </div>
  </script>

  <script type="text/x-handlebars" id="posts/index">
    <p class="text-warning">Please select a post</p>
  </script>

  <script type="text/x-handlebars" id="post">
    <h1>{{post_title}}</h1>
    <h2>by {{author.name}} <small class='muted'>({{format-date date}})</small></h2>

    <hr>

    <div class='post-content'>
      {{{post_content}}}
    </div>
  </script>

  <script src="<?php bloginfo('template_directory') ?>/libs/jquery-1.9.1.js"></script>
  <script src="<?php bloginfo('template_directory') ?>/libs/handlebars-1.0.0.js"></script>
  <script src="<?php bloginfo('template_directory') ?>/libs/ember-1.0.0-rc.8.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.1.0/moment.min.js"></script>
  <?php require 'app.php' ?>
  

</body>
</html>
