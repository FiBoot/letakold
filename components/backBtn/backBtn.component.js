function backBtnController($scope, $location) {
  let ctrl = this;

  this.$onInit = function onInit() {};
  this.$onChanges = function onChanges(changesObj) {};

  ctrl.click = function click() {
    $location.path(`/list/${ctrl.type}`);
  };
}

angular.module('App').component('backBtn', {
  templateUrl: 'components/backBtn/backBtn.template.html',
  controller: ['$scope', '$location', backBtnController],
  bindings: {
    type: '@'
  }
});
