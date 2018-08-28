angular.module('App').controller('mainCtrl', [
  '$scope',
  '$rootScope',
  'ajaxService',
  function($scope, $rootScope, ajaxService) {
    $rootScope.BASEURL = 'fiboot/-new/#/';
    $rootScope.TIMESPAN = 150;

    $rootScope.apply = function apply() {
      if (!$scope.$root.$$phase) {
        $scope.$apply();
      }
    };

    $scope.Loaded = false;

    $(document).ready(function() {
      $scope.$broadcast('loaded');
      setTimeout(function() {
        $scope.Loaded = true;
        $rootScope.apply();
      }, $rootScope.TIMESPAN);
    });

    $scope.User = {
      connected: false,
      name: ''
    };

    $scope.Login = {
      info: new AjaxInfo(),
      username: '',
      password: ''
    };

    function connection(response) {
      if (response.status) {
        $scope.User.name = response.data.name;
        $scope.User.connected = true;
        $scope.$broadcast('connected');
        $rootScope.apply();
      }
    }

    $scope.connect = function connect() {
      ajaxService.internalAjax('connect', $scope.Login, $scope.Login.info, connection);
    };

    $scope.disconnect = function disconnect() {
      ajaxService.internalAjax('disconnect', null, $scope.Login.info, response => {
        if (response.status) {
          $scope.User.connected = false;
          $scope.$broadcast('disconnected');
          $rootScope.apply();
        }
      });
    };

    $scope.$on('loaded', (event, data) => {
      ajaxService.internalAjax('autoconnect', null, $scope.Login.info, connection);
    });
  }
]);
