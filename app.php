<script type="text/javascript">
	App = Ember.Application.create({});
	
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
	  return moment(date).fromNow();
	});
</script>