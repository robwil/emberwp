<script type="text/javascript">
	App = Ember.Application.create({});
	App.ApplicationView = Ember.View.extend({
	  didInsertElement: function() {
		 $("#loader").remove();
	  }
	});
	App.WP = Ember.Object.extend({
		loadedPosts: false,
		page: 1,
		pages: 1,
		
		loadPosts: function() {
		  var wp = this;
		  return Ember.Deferred.promise(function (p) {

			if (wp.get('loadedPosts')) {
			  // We've already loaded the links, let's return them!
			  p.resolve(wp.get('posts'));
			} else {

			  // If we haven't loaded the links, load them via JSON
			  p.resolve($.getJSON("<?php echo site_url(); ?>/?json=1").then(function(response) {
				var posts = response.posts.map(function(post) {
					post.wp = wp;
					post.category = post.categories[0].title;
					post.body = post.content;
					return post;
				});
				wp.setProperties({posts: posts, loadedPosts: true, page: 1, pages: response.pages});
				return posts;
			  }));
			}
		  });
		},

		findPostByName: function(post_name) {
		  return this.loadPosts().then(function (posts) {
			return posts.findProperty('slug', post_name);
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
</script>