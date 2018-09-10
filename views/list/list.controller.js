angular.module('App').controller('listCtrl', [
  '$scope',
  '$rootScope',
  '$routeParams',
  'ajaxService',
  function($scope, $rootScope, $routeParams, ajaxService) {
    $scope.D = {
      info: new AjaxInfo(),
      type: null,
      loaded: false,
      list: []
    };

    function loadData() {
      $scope.D.type = $routeParams.type;
      const options = {
        type: $scope.D.type,
        order: 'last_update'
      };
      $scope.D.loaded = false;
      ajaxService.internalAjax('list', options, $scope.D.info, response => {
        $scope.D.list = response.data;
        $scope.D.loaded = true;
        $rootScope.apply();
      });
    }

    $scope.canEdit = function canEdit(item) {
      return $rootScope.User.connected && item.account_id === $rootScope.User.id;
    };

    $scope.$on('connected', loadData);
    $scope.$on('disconnected', loadData);
    loadData();
  }
]);
