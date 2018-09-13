function ajaxMessageController($scope, $rootScope) {
  var ctrl = this;

  ctrl.click = function click() {
    ctrl.info.message = null;
    $rootScope.apply();
  };
}
angular.module('App').component('ajaxMsg', {
  templateUrl: 'components/ajaxMsg/ajaxMsg.template.html',
  controller: ['$scope', '$rootScope', ajaxMessageController],
  bindings: {
    info: '='
  }
});
