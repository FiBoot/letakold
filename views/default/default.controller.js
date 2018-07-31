angular.module('App').controller('defaultCtrl', [
  '$scope',
  '$rootScope',
  'ajaxService',
  function($scope, $rootScope, ajaxService) {
    $scope.actions = ['get', 'list', 'new', 'save', 'update', 'delete', 'connect', 'disconnect'];
    $scope.D = {
      action: $scope.actions[0],
      type: '',
      field: '',
      value: '',
      force: false
    };
    $scope.data = null;
    $scope.info = new AjaxInfo();

    $scope.submit = function submit() {
      const D = $scope.D;
      ajaxService.internalAjax(D.action, D.type, D, $scope.info, response => {
        console.log(response.data);
        $scope.data = response.data;
        $rootScope.apply();
      });
    };
  }
]);
