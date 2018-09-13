angular.module('App').controller('listCtrl', [
  '$scope',
  '$rootScope',
  '$routeParams',
  '$location',
  'ajaxService',
  function($scope, $rootScope, $routeParams, $location, ajaxService) {
    $scope.D = {
      info: new AjaxInfo(),
      type: null,
      loaded: false,
      list: []
    };

    $scope.isOwner = function isOwner(item) {
      return $rootScope.User.connected && item.account_id === $rootScope.User.id;
    };

    $scope.goTo = function goTo(item, edit) {
      edit = edit ? 'edit/' : '';
      $location.path(`${$scope.D.type}/${edit}${item.id}`)
    }

    $scope.create = function create() {
      $location.path(`/${$scope.D.type}`);

    }

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

    $scope.$on('connected', loadData);
    $scope.$on('disconnected', loadData);
    loadData();
  }
]);
