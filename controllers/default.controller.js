angular.module("App").controller("defaultCtrl", [
  "$scope",
  "$rootScope",
  "ajaxService",
  function($scope, $rootScope, ajaxService) {
    console.log(2);
    ajaxService.InternalAjax(
      "LIST",
      "app",
      null,
      data => {
        console.log(data);
      },
      err => {
        console.log(err);
      }
    );
  }
]);
