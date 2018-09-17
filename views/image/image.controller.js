angular.module('App').controller('imageCtrl', [
  '$scope',
  '$rootScope',
  '$routeParams',
  '$location',
  'ajaxService',
  function($scope, $rootScope, $routeParams, $location, ajaxService) {
    $scope.D = {
      info: new AjaxInfo(),
      loaded: false,
      list: [],
      image: null
    };

    function loadImage(id) {
      ajaxService.internalAjax('get', { type: 'image', id: id }, $scope.D.info, response => {
        if (response.status) {
          response.data.parseData();
          $scope.D.image = response.data;
          $scope.D.loaded = true;
          $rootScope.apply();
        }
      });
    }

    function loadList() {
      ajaxService.internalAjax('list', { type: 'image' }, $scope.D.info, response => {
        if (response.status) {
          response.data.forEach(image => image.parseData());
          $scope.D.list = response.data;
          $scope.D.loaded = true;
          $rootScope.apply();
        }
      });
    }

    $scope.goTo = function goTo(image) {
      $location.path(`/image/${image.id}`);
    };

    function init() {
      if ($routeParams.hasOwnProperty('id')) {
        loadImage($routeParams.id);
      } else {
        loadList();
      }
    }
    init();
  }
]);
