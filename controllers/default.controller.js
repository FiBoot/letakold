angular.module('App').controller('defaultCtrl', [
  '$scope',
  '$rootScope',
  'ajaxService',
  function($scope, $rootScope, ajaxService) {
    $scope.actions = ['get', 'list', 'add', 'save', 'update', 'delete'];
    $scope.D = {
      action: $scope.actions[0],
      type: '',
      field: '',
      value: ''
    };
    $scope.data = null;
    $scope.info = new AjaxInfo();

    $scope.submit = function submit() {
      const D = $scope.D;
      let data = {};
      data[D.field] = D.value;
      ajaxService.internalAjax(D.action, D.type, D.field ? data : null, $scope.info, response => {
        $scope.data = response.data;
        $rootScope.apply();
      });
    };
  }
]);
