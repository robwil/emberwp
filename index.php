<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
  <link rel="stylesheet" href="<?php bloginfo('template_directory') ?>/css/normalize.css">
  <link rel="stylesheet" href="<?php bloginfo('template_directory') ?>/css/genericons.css">
  <link href='http://fonts.googleapis.com/css?family=Libre+Baskerville|Jacques+Francois' rel='stylesheet' type='text/css'>
  <style type="text/css" media="screen">
    @import url( <?php bloginfo('stylesheet_url'); ?> );
  </style>
  <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
</head>
<body>

  <div id="loader">
	<img src="<?php bloginfo('template_directory') ?>/images/ajax-loader.gif"/>
  </div>

  <script type="text/x-handlebars">
    <div class="container-fluid">
	  <div class="row-fluid">
		<div class="span3 banner">
		  {{outlet sidebar}}
		</div>
		<div class="span9">
		  {{outlet main}}
		</div>
	  </div> <!-- end .row-fluid -->
	</div> <!-- end .container-fluid -->
  </script>

  <script type="text/x-handlebars" id="posts">
	{{partial "main"}}
  </script>
  
  <script type="text/x-handlebars" id="page">
	{{partial "main"}}
  </script>
  
  <script type="text/x-handlebars" id="_main">
	<h1><a href="/"><span class="home">Rob Williams</span></a></h1>
	<table class='table'>
		<thead>
		  <tr><th>Recent Posts</th></tr>
		</thead>
		{{#each model}}
		<tr><td>
			{{#link-to 'post' this}}{{{title}}}{{/link-to}} {{ format-category category }}
		</td></tr>
		{{/each}}
	</table>
	{{#bind model}}{{page-links}}{{/bind}}
  </script>

  <script type="text/x-handlebars" id="posts/index">
    <article class="post">
		<h1 class="post-title">Welcome</h1>
		<p>
			This is the blog where I post any projects or insights that I've come up with.
			Navigate on the left side to find something that interests you.
		</p>
	</article>
  </script>
 
  <script type="text/x-handlebars" id="post">
	<article class="post">
		<h1 class="post-title">{{{title}}}</h1>
		<h2> <small class='muted'>posted by {{author.nickname}} <span class="date">{{format-date date}}</span></small></h2>

		<hr>

		<div class='post-content'>
		  {{{body}}}
		</div>
	</article>
  </script>

  <script src="<?php bloginfo('template_directory') ?>/libs/jquery-1.9.1.js"></script>
  <script src="<?php bloginfo('template_directory') ?>/libs/handlebars-1.0.0.js"></script>
  <script src="<?php bloginfo('template_directory') ?>/libs/ember-1.0.0-rc.8.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.1.0/moment.min.js"></script>
  <?php require 'app.php' ?>
  

</body>
</html>
