angular.module("App").controller("listCtrl", [
  "$scope",
  "$rootScope",
  "$routeParams",
  function($scope, $rootScope, $routeParams) {
    // SCOPE
    $scope.list = {
      type: "",
      data: [],
      selected: null
    };

    $scope.info = {
      pending: false,
      message: null,
      status: true
    };

    // SCOPE FUNC
    $scope.isOwned = function isOwned(o) {
      if (!$rootScope.account.connected) {
        return false;
      }
      return $rootScope.account.info.admin || o.account_id === $rootScope.account.info.id;
    };

    $scope.selectObject = function selectObject(o) {
      $scope.list.selected = o;
    };

    $scope.deleteObject = function deleteObject(o) {
      $scope.info.pending = true;
      $rootScope.InternalAjax(
        `DELETE_${$scope.list.type.toUpperCase()}`,
        {
          object_id: o.id,
          account_id: $rootScope.account.connected ? $rootScope.account.info.id : 0,
          password: $rootScope.account.connected ? $rootScope.account.info.password : null
        },
        function(successData) {
          $scope.info.pending = false;
          $("#deleteModal").modal("hide");
          getList();
        },
        function(errorData) {
          $scope.info.pending = false;
          $rootScope.Apply();
        }
      );
    };

    // FUNC
    function getList() {
      $scope.list.type = $routeParams.type;

      $rootScope.StartLoading($scope.info, "Loading ..");
      $rootScope.InternalAjax(
        `GET_${$scope.list.type.toUpperCase()}_LIST`,
        {
          account_id: $rootScope.account.connected ? $rootScope.account.info.id : 0,
          is_admin: $rootScope.account.connected ? $rootScope.account.info.admin : 0,
          password: $rootScope.account.connected ? $rootScope.account.info.password : null
        },
        function(successData) {
          $rootScope.EndLoading($scope.info, successData.message, successData.status);
          if (successData.status) {
            $scope.list.data = successData.data;
            $rootScope.Apply();
          }
        },
        function(errorData) {
          $rootScope.EndLoading($scope.dndSpells, `Error while loading list ${type}`, false);
        }
      );
    }

    // INIT
    getList();

    // EVENT
    $scope.$on("connection", getList);
    $scope.$on("disconnection", getList);
  }
]);
