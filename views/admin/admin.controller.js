angular.module('App').controller('adminCtrl', [
  '$scope',
  '$rootScope',
  'ajaxService',
  function($scope, $rootScope, ajaxService) {
    $scope.info = new AjaxInfo();

    $scope.searchString = new String();
    $scope.N = new DataRow();
    $scope.E = new DataRow();

    $scope.D = {
      loaded: false,
      list: []
    };

    function loadList() {
      ajaxService.internalAjax('list', null, null, response => {
        $scope.D.list = response.data.map(row => {
          row.edit = false;
          return row;
        });
        $scope.D.loaded = true;
        $rootScope.apply();
      });
    }

    function actionItem(action, item) {
      ajaxService.internalAjax(action, { item: item }, $scope.info, loadList);
    }

    $scope.search = function() {
      console.log($scope.S.input);
    };

    $scope.add = function(item) {
      if (item.parseData()) {
        actionItem('new', item);
        $scope.N = new DataRow();
      } else {
        $scope.info.nok(`Error while parsing data for ${item.name}`);
      }
    };

    $scope.save = function(item) {
      if (item.parseData()) {
        actionItem('save', item);
      } else {
        $scope.info.nok(`Error while parsing data for ${item.name}`);
      }
    };

    $scope.delete = function(item) {
      actionItem('delete', item);
    };

    $scope.edit = function(item) {
      $scope.E = new DataRow(item);
      item.edit = true;
    };

    $(document).ready(function() {
      loadList();
    });
  }
]);
