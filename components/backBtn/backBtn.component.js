function backBtnController($scope, $location) {
  let ctrl = this;

  ctrl.click = function click() {
    const link = `/${(ctrl.noList ? '' : 'list/')}${ctrl.type}`;
    $location.path(link);
  };
}

angular.module('App').component('backBtn', {
  templateUrl: 'components/backBtn/backBtn.template.html',
  controller: ['$scope', '$location', backBtnController],
  bindings: {
    type: '@',
    noList: '@'
  }
});
