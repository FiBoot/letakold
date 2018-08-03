function ajaxBtnController($scope, $element, $attrs) {
  var ctrl = this;
  this.$onInit = function onInit() {};
  this.$onChanges = function onChanges(changesObj) {};

  ctrl.click = function click() {
    ctrl.onClick({});
  };
}

angular.module('App').component('ajaxBtn', {
  templateUrl: 'components/ajaxBtn/ajaxBtn.template.html',
  controller: ajaxBtnController,
  bindings: {
    info: '<',
    value: '@',
    class: '@',
    onClick: '&'
  }
});
