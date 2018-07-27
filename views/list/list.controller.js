angular.module('App').controller('listCtrl', [
  '$scope',
  '$rootScope',
  '$routeParams',
  'ajaxService',
  function($scope, $rootScope, $routeParams, ajaxService) {
    function init() {
      $routeParams.type;
    }

    init();
  }
]);
