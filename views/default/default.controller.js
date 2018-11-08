angular.module('App').controller('defaultCtrl', [
  '$scope',
  '$rootScope',
  '$location',
  'ajaxService',
  function ($scope, $rootScope, $location, ajaxService) {
    $scope.D = {
      info: new AjaxInfo(),
      loaded: false,
      list: []
    };

    function loadData() {
      const options = {
        order: 'last_update',
        limit: 12
      };
      ajaxService.internalAjax('list', options, $scope.D.info, response => {
        $scope.D.list = response.data;
        $rootScope.apply();
      });
    }

    $scope.goTo = function goTo(item) {
      const link = `${item.type}/${item.id}`;
      $location.path(link);
    };

    $scope.$on('connected', loadData);
    $scope.$on('disconnected', loadData);
    loadData();
  }
]);
