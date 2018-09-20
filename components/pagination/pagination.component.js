function paginationController($scope, $element, $attrs) {
  let ctrl = this;

  ctrl.click = function click(page) {
    if (page > 0 && page <= ctrl.total && page !== ctrl.current) {
      ctrl.onChange({page: page});
    }
  };
}

angular.module('App').component('pagination', {
  templateUrl: 'components/pagination/pagination.template.html',
  controller: paginationController,
  bindings: {
    current: '<',
    total: '<',
    onChange: '&'
  }
});
