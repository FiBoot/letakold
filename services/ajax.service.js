class AjaxInfo {
  constructor() {
    this.pending = false;
    this.message = '';
    this.status = null;
  }

  update(pending, message, status) {
    this.status = status;
    this.message = message;
    this.pending = pending;
  }
  ok(message) {
    this.update(false, message, true);
  }
  nok(message) {
    this.update(false, message, false);
  }
}

function ajaxService($rootScope) {
  return {
    ajax: function ajax(url, method, data, info, successCallback, errorCallback) {
      const jqxhr = $.ajax({
        url: url,
        method: method,
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        data: JSON.stringify(data)
      });

      jqxhr.done((data, textStatus, jqXHR) => {
        info.update(false, data.message, data.status);
        $rootScope.apply();
        if (successCallback) {
          successCallback(data);
        }
      });
      jqxhr.fail((jqXHR, textStatus, errorThrown) => {
        info.nok(textStatus);
        $rootScope.apply();
        if (errorCallback) {
          errorCallback(textStatus);
        }
      });
      jqxhr.always((jqXHR, textStatus, errorThrown) => {});
    },

    internalAjax: function internalAjax(action, type, data, info = new AjaxInfo(), successCallback, errorCallback) {
      info.update(true, `${action} ${type} ...`);
      this.ajax(
        'api/ajax.php',
        'POST',
        { action: action, type: type, data: data },
        info,
        successCallback,
        errorCallback
      );
      return info;
    }
  };
}

angular.module('App').factory('ajaxService', ['$rootScope', ajaxService]);
