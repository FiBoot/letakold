angular.module('App', ['ngRoute']).config(function($routeProvider, $locationProvider) {
  $locationProvider.hashPrefix('');

  $routeProvider
    // index
    .when('/', {
      controller: 'defaultCtrl',
      templateUrl: 'views/default/default.view.html'
    })
    // list
    .when('/list/:type', {
      controller: 'listCtrl',
      templateUrl: 'views/list/list.view.html'
    })
    .otherwise({ redirectTo: '/' });
});
