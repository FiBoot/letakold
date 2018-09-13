angular.module('App').controller('mainCtrl', [
  '$scope',
  '$rootScope',
  'ajaxService',
  function($scope, $rootScope, ajaxService) {
    $rootScope.BASEURL = 'fiboot/-new/#/';
    $rootScope.TIMESPAN = 250;

    $rootScope.apply = function apply() {
      if (!$scope.$root.$$phase) {
        $scope.$apply();
      }
    };

    $scope.Loaded = false;

    $rootScope.User = {
      connected: false,
      id: 0,
      name: ''
    };

    $scope.Login = {
      info: new AjaxInfo(),
      username: '',
      password: ''
    };

    function connection(response) {
      if (response.status) {
        $rootScope.User.id = response.data.id;
        $rootScope.User.name = response.data.name;
        $rootScope.User.connected = true;
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
          $rootScope.User.connected = false;
          $scope.$broadcast('disconnected');
          $rootScope.apply();
        }
      });
    };

    $(document).ready(function() {
      setTimeout(function() {
        $scope.Loaded = true;
        $scope.$broadcast('loaded');
        console.log('broadcasting');
        $rootScope.apply();
      }, $rootScope.TIMESPAN);

      ajaxService.internalAjax('autoconnect', null, $scope.Login.info, connection);
    });

  }
]);
