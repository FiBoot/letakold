angular.module("App", ["ngRoute"]).config(function($routeProvider) {
  $routeProvider
    // index
    .when("/", {
      controller: "defaultCtrl",
      templateUrl: "views/default.view.html"
    })
    // list
    .when("/list/:type", {
      controller: "listCtrl",
      templateUrl: "views/list.view.html"
    })
    .otherwise({ redirectTo: "/" });
});
