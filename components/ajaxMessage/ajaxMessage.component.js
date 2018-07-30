function ajaxMessageController($scope, $element, $attrs) {
  var ctrl = this;
  ctrl.display = true;

  this.$onInit = function onInit() {};
  this.$onChanges = function onChanges(changesObj) {
    ctrl.display = true;
  };

  ctrl.click = function click() {
    ctrl.display = false;
  };
}

angular.module('App').component('ajaxMessage', {
  templateUrl: 'components/ajaxMessage/ajaxMessage.template.html',
  controller: ajaxMessageController,
  bindings: {
    info: '<'
  }
});
