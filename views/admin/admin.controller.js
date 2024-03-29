angular.module('App').controller('adminCtrl', [
  '$scope',
  '$rootScope',
  'ajaxService',
  function($scope, $rootScope, ajaxService) {
    $scope.info = new AjaxInfo();
    $scope.resultCounts = [1, 10, 20, 50, 100, 200];
    $scope.allType = '*';

    $scope.N = new DataRow();
    $scope.E = new DataRow();

    $scope.F = {
      page: 1,
      count: $scope.resultCounts[1],
      type: $scope.allType
    };

    $scope.D = {
      loaded: false,
      list: [],
      displayList: [],
      pages: [0],
      types: [$scope.allType]
    };

    function loadData() {
      ajaxService.internalAjax('list', { order: 'id' }, null, response => {
        const types = [$scope.allType];
        $scope.D.list = response.data.map(row => {
          if (!types.includes(row.type)) {
            types.push(row.type);
          }
          row.edit = false;
          return row;
        });
        $scope.D.types = types;
        $scope.calcPagination();
        $scope.D.loaded = true;
        $rootScope.apply();
      });
    }

    function actionItem(action, item) {
      ajaxService.internalAjax(action, { item: item }, $scope.info, loadData);
    }

    $scope.calcPagination = function calcPagination() {
      let pageCount =
        Math.floor($scope.D.list.length / $scope.F.count) +
        ($scope.D.list.length % $scope.F.count ? 1 : 0);
      let pageArr = new Array(pageCount);
      for (let i = 0; i < pageArr.length; i++) {
        pageArr[i] = i + 1;
      }
      $scope.F.page = $scope.F.page > pageCount ? pageCount : $scope.F.page;
      $scope.D.pages = pageArr;
      $scope.calcDisplayList();
    };

    $scope.calcDisplayList = function calcDisplayList() {
      const start = ($scope.F.page - 1) * $scope.F.count;
      const end =
        $scope.F.count > $scope.D.list.length ? $scope.D.list.length - 1 : start + $scope.F.count;
      $scope.D.displayList = $scope.D.list.slice(start, end);
      console.log(start, end);
    };

    $scope.add = function addItem(item) {
      if (item.parseData()) {
        actionItem('new', item);
        $scope.N = new DataRow();
      } else {
        $scope.info.nok(`Error while parsing data for ${item.name}`);
      }
    };

    $scope.save = function saveItem(item) {
      if (item.parseData()) {
        actionItem('save', item);
      } else {
        $scope.info.nok(`Error while parsing data for ${item.name}`);
      }
    };

    $scope.delete = function deleteItem(item) {
      actionItem('delete', item);
    };

    $scope.edit = function editItem(item) {
      $scope.E = new DataRow(item);
      item.edit = true;
    };

    $scope.$on('connected', loadData);
    $scope.$on('disconnected', loadData);
    loadData();
  }
]);
