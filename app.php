<script type="text/javascript">
	App = Ember.Application.create({});
	App.ApplicationView = Ember.View.extend({
	  didInsertElement: function() {
		 $("#loader").remove();
	  }
	});
	App.WP = Ember.Object.extend({
		loadedPosts: false,

		loadPosts: function() {
		  var wp = this;
		  return Ember.Deferred.promise(function (p) {

			if (wp.get('loadedPosts')) {
			  // We've already loaded the links, let's return them!
			  p.resolve(wp.get('posts'));
			} else {

			  // If we haven't loaded the links, load them via JSON
			  p.resolve($.getJSON("<?php bloginfo('template_directory') ?>/bridge.php?action=getPosts").then(function(response) {
				var posts = response.map(function(post) {
					post.id = post.post_id;
					post.wp = wp;
					return post;
				});
				wp.setProperties({posts: posts, loadedPosts: true});
				return posts;
			  }));
			}
		  });
		},

		findPostByName: function(post_name) {
		  return this.loadPosts().then(function (posts) {
			return posts.findProperty('post_name', post_name);
		  });
		}
	});
	
	App.WP.reopenClass({
		instance: function() {
			return App.WP.create();
		}
	});
	
	App.Router.map(function() {
	  this.resource('posts', { path: '/' }, function() {;
		this.resource('post', { path: '/:year/:month/:post_name' });
	  });
	});

	App.PostsRoute = Ember.Route.extend({
	  model: function() {
		return App.WP.instance().loadPosts().then(function(posts) {
			return posts;
		});
	  }
	});

	App.PostRoute = Ember.Route.extend({
	  model: function(params) {
		return this.modelFor("posts")[0].wp.findPostByName(params.post_name);
	  },
	  serialize: function(model) {
		return {year: model.post_date.year, month: model.post_date.month, post_name: model.post_name};
	  }
	});

	Ember.Handlebars.helper('format-date', function(date) {
	  var dateString = date.year + date.month + date.day;// + date.hour + date.minute + date.second;
	  //return dateString;
	  return moment(dateString, 'YYYYMMDD').format('MMMM D, YYYY');
	});
	
	Ember.Handlebars.helper('format-category', function(category) {
	  // Ugly hacky way to get different colors for category labels.
	  var color = "color-A";
	  if (category == "Self-Referential") { color = "color-B" }
	  else if (category == "Guides") { color = "color-C" }
	  return new Handlebars.SafeString("<span class='label " + color + "'>" + category + "</span>");
	});
</script>