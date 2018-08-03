angular.module('App').controller('defaultCtrl', [
  '$scope',
  '$rootScope',
  'ajaxService',
  function($scope, $rootScope, ajaxService) {
    $scope.info = new AjaxInfo();
    $scope.items = [];
    let data;

    function loadHotItems() {
      const options = {
        orderby: 'last_update',
        asc: false,
        limit: 10
      };
      ajaxService.internalAjax('list', options, null, response => {
        $scope.items = response.data;
        data = response.data[0];
        data.parseData();
        $rootScope.apply();
      });
    }

    $scope.test = function test() {
      const options = { id: 1178, item: data };
      ajaxService.internalAjax('new', options, $scope.info);
    };

    $scope.connect = function() {
      options = { username: 'fiboot', password: 'qweqwe', debug: true };
      ajaxService.internalAjax('connect', options, $scope.info);
    };
    $scope.disconnect = function() {
      ajaxService.internalAjax('disconnect', { debug: true }, $scope.info);
    };

    $(document).ready(function() {
      loadHotItems();
    });
  }
]);
