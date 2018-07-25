function ajaxMessageController($scope, $element, $attrs) {
  var ctrl = this;
  ctrl.display = true;

  this.$onInit = function onInit() {};
  this.$onChanges = function onChanges(changesObj) {
    ctrl.display = true;
    console.warn(ctrl.display);
  };

  ctrl.click = function click() {
    ctrl.display = false;
    console.warn(ctrl.display);
  };
}

angular.module('App').component('ajaxMessage', {
  templateUrl: 'components/ajaxMessage/ajaxMessage.template.html',
  controller: ajaxMessageController,
  bindings: {
    info: '<'
  }
});
