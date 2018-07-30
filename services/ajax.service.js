function buildData(response) {
  if (response.data) {
    if (response.data instanceof Array) {
      const arr = [];
      response.data.forEach(row => {
        arr.push(new Data(row));
      });
      response.data = arr;
    } else {
      response.data = new Data(response.data);
    }
  }
  return response;
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

      jqxhr.done((response, textStatus, jqXHR) => {
        info.update(false, response.message, response.status);
        $rootScope.apply();
        if (successCallback) {
          let data = buildData(response);
          console.warn(response, data);
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
