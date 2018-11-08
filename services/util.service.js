
function utilService($rootScope) {
  return {
    random(max) { return Math.floor(Math.random() * max); },
    cube(n) { return Math.pow(n, 2); },
    sign(n) { return n > 0 ? 1 : n < 0 ? -1 : 0; }
  }
}

angular.module('App').factory('utilService', ['$rootScope', utilService]);