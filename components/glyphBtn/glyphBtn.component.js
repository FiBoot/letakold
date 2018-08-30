function glyphBtnController($scope, $element, $attrs) {
  let ctrl = this;

  this.$onInit = function onInit() {};
  this.$onChanges = function onChanges(changesObj) {};

  ctrl.click = function click() {
    ctrl.onClick({});
  };
}

angular.module('App').component('glyphBtn', {
  templateUrl: 'components/glyphBtn/glyphBtn.template.html',
  controller: glyphBtnController,
  bindings: {
    type: '@',
    glyph: '@',
    size: '@',
    value: '@',
    onClick: '&'
  }
});
