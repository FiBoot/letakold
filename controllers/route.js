angular.module('App', ['ngRoute']).config(function($routeProvider) {
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
