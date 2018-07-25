angular.module('App').controller('mainCtrl', [
  '$scope',
  '$rootScope',
  function($scope, $rootScope) {
    $rootScope.apply = function apply() {
      if (!$scope.$root.$$phase) {
        $scope.$apply();
      }
    };
  }
]);
