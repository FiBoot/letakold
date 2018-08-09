angular.module('App').controller('defaultCtrl', [
  '$scope',
  '$rootScope',
  'ajaxService',
  function($scope, $rootScope, ajaxService) {
    $scope.info = new AjaxInfo();

    $scope.F = {
      action: '',
      data: '',
    };


    $scope.submit = function submit() {
      try {
        let data = $scope.F.data ? JSON.parse($scope.F.data) : null;
        const options = ajaxService.internalAjax($scope.F.action, data, $scope.info, response => {
          console.log(response);
        });
      } catch (e) {
        console.warn(e);
      }
    }
  }
]);
