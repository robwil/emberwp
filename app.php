<script type="text/javascript">
	App = Ember.Application.create({
  LOG_TRANSITIONS: true
});
	App.ApplicationView = Ember.View.extend({
	  didInsertElement: function() {
		 $("#loader").remove();
	  }
	});
	App.WP = Ember.Object.extend({
		loadedPosts: false,
		page: 1,
		pages: 1,
		lastPost: undefined,
		
		loadPosts: function(page) {
		  var wp = this;
		  return Ember.Deferred.promise(function (p) {
			if (wp.get('loadedPosts') && wp.get('page')==page) {
			  // We've already loaded the posts, let's return them!
			  p.resolve(wp.get('posts'));
			} else {

			  // If we haven't loaded the posts, load them via JSON
			  var url = "<?php echo site_url(); ?>/?json=1&page="+page;
			  p.resolve($.getJSON(url).then(function(response) {
				var posts = response.posts.map(function(post) {
					post.category = post.categories[0].title;
					post.body = post.content;
					return post;
				});
				wp.setProperties({'posts': posts, 'loadedPosts': true, 'page': page, 'pages': response.pages});
				return posts;
			  }).fail(function(jqxhr, textStatus, error) {
				var err = textStatus + ", " + error;
				console.log( "Request Failed: " + err );
			  }));
			}
		  });
		},

		findPostByName: function(post_name) {
		  var wp = this;
		  if (post_name === undefined) {
			return wp.get('lastPost');
		  }
		  var curpage = wp.get('page');
		  if (curpage === undefined) { curpage = 1; }
		  return this.loadPosts(curpage).then(function (posts) {
			var post = posts.findProperty('slug', post_name);
			if (post === undefined) {
				return Ember.Deferred.promise(function (p) {
					p.resolve($.getJSON("<?php echo site_url(); ?>/" + post_name + "?json=1").then(function(response) {
						post = response.post;
						post.category = post.categories[0].title;
						post.body = post.content;
						wp.setProperties({'lastPost':post});
						return post;
					}));
				});
			} else {
				wp.setProperties({'lastPost':post});
				return post;
			}
		  });
		}
	});
	var WP = App.WP.create();
	
	App.Router.map(function() {
	  this.resource('posts', { path: '/' }, function() {;
		this.resource('post', { path: '/:year/:month/:post_name' });
	  });
	  this.route('page', { path: '/:page' });
	});

	App.PostsRoute = Ember.Route.extend({
	  model: function() {
		var curpage = WP.get('page');
		if (curpage === undefined) { curpage = 1; }
		return WP.loadPosts(curpage).then(function(posts) {
			return posts;
		});
	  },
	  renderTemplate: function() {
		this.render({outlet: 'sidebar'});
	  }
	});
	
	App.PageRoute = Ember.Route.extend({
	  model: function(params) {
		return WP.loadPosts(params.page).then(function(posts) {
			return posts;
		});
	  },
	  renderTemplate: function() {
		this.render({outlet: 'sidebar'});
		this.render('post', {controller: this.controllerFor('post')});
	  }
	});

	App.PostRoute = Ember.Route.extend({
	  model: function(params) {
		return WP.findPostByName(params.post_name);
	  },
	  serialize: function(model) {
		return {year: model.date.substring(0,4), month: model.date.substring(5,7), post_name: model.slug};
	  }
	});

	Ember.Handlebars.helper('format-date', function(date) {
	  return moment(date, 'YYYY-MM-DD hh:mm:ss').format('MMMM D, YYYY');
	});
	
	Ember.Handlebars.helper('format-category', function(category) {
	  // Ugly hacky way to get different colors for category labels.
	  var color = "color-A";
	  if (category == "Self-Referential") { color = "color-B" }
	  else if (category == "Guides") { color = "color-C" }
	  return new Handlebars.SafeString("<span class='label " + color + "'>" + category + "</span>");
	});
	
	Ember.Handlebars.helper('page-links', function() {
	  var prev = Number(WP.page)-1;
	  var next = Number(WP.page)+1;
	  var output = "";
	  if (prev >= 1) {
		output += "<a href='/#/" + prev + "' title='previous'>&laquo; previous</a>";
	  }
	  if (next <= WP.pages) {
	    output += " <a href='/#/" + next + "' title='next'>next &raquo;</a>";
	  }
	  return new Handlebars.SafeString(output);
	});
</script>