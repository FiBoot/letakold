angular.module('App').controller('defaultCtrl', [
  '$scope',
  '$rootScope',
  'ajaxService',
  function($scope, $rootScope, ajaxService) {
    $scope.items = [];

    function loadHotItems() {
      const options = {
        orderby: 'last_update',
        asc: false,
        limit: 10
      };
      ajaxService.internalAjax('list', options, $scope.list, response => {
        $scope.items = response.data;
        $rootScope.apply();
      });
    }

    $(document).ready(function() {
      loadHotItems();
    });
  }
]);
