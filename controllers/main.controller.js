angular.module('App').controller('mainCtrl', [
  '$scope',
  '$rootScope',
  'ajaxService',
  function($scope, $rootScope, ajaxService) {
    $rootScope.apply = function apply() {
      if (!$scope.$root.$$phase) {
        $scope.$apply();
      }
    };

    // autoconnect
    $(document).ready(function() {
      ajaxService.internalAjax('connect');
    });
  }
]);
