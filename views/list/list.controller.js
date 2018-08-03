angular.module('App').controller('listCtrl', [
  '$scope',
  '$rootScope',
  '$routeParams',
  'ajaxService',
  function($scope, $rootScope, $routeParams, ajaxService) {
    $scope.D = {
      info: new AjaxInfo(),
      type: null,
      list: []
    };

    function loadList(type) {
      $scope.type = type;
      const options = {
        type: type,
        order: 'last_update'
      };
      ajaxService.internalAjax('list', options, $scope.D.info, response => {
        $scope.list = response.data;
      });
    }

    $(document).ready(function() {
      loadList($routeParams.type);
    });
  }
]);
