angular.module('App', ['ngRoute']).config(function($routeProvider, $locationProvider) {
  $locationProvider.hashPrefix('');

  $routeProvider
    // index
    .when('/', {
      controller: 'defaultCtrl',
      templateUrl: 'views/default/default.view.html'
    })
    // admin
    .when('/admin', {
      controller: 'adminCtrl',
      templateUrl: 'views/admin/admin.view.html'
    })
    // list
    .when('/list/:type', {
      controller: 'listCtrl',
      templateUrl: 'views/list/list.view.html'
    })

    // story
    .when('/story/new', {
      controller: 'storyCtrl',
      templateUrl: 'views/story/story.view.html'
    })
    .when('/story/:id', {
      controller: 'storyCtrl',
      templateUrl: 'views/story/story.view.html'
    })
    .otherwise({ redirectTo: '/' });
});
