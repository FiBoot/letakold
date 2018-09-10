angular.module('App').controller('timelineCtrl', [
  '$scope',
  '$rootScope',
  '$routeParams',
  'ajaxService',
  function($scope, $rootScope, $routeParams, ajaxService) {

    class Item {
      constructor(row) {
        this.id = row.id;
        // this.group = row.data.group;
        this.content = row.name;
        this.start = row.data.start_date;
        this.end = row.data.end_date;
        this.type = row.data.type;
        this.public = row.public;
        this.editable = $rootScope.User.connected && row.account_id === $rootScope.User.id;
        this.saved = true;
      }
    }

    $scope.D = {
      info: new AjaxInfo,
      loaded: false,
      list: []
    }

    function buildTimeline(data) {
      let items = array();
      data.forEach(item => {
        item.parseData();
      });
    }

    function loadData() {
      ajaxService.internalAjax('get', {id: 1201}, null, response => {
        console.log(response);
      });
      // $scope.D.loaded = false;
      // ajaxService.internalAjax('list', {type: 'event'}, $scope.D.info, response => {
      //   console.log(response);
      //   if (response.status) {
      //     buildTimeline(response.data);
      //     $scope.D.loaded = true;
      //   }
      // });
    }

    $scope.$on('connected', loadData);
    $scope.$on('disconnected', loadData);
    loadData();
  }
]);
