

function ajaxService($rootScope) {
  
  function buildDataRow(response) {
    if (response.data) {
      if (response.data instanceof Array) {
        const arr = [];
        response.data.forEach(row => {
          arr.push(new DataRow(row));
        });
        response.data = arr;
      } else {
        response.data = new DataRow(response.data);
      }
    }
    return response;
  }

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
          const dataRow = buildDataRow(response);
          successCallback(dataRow);
        }
      });
      jqxhr.fail((jqXHR, textStatus, errorThrown) => {
        info.nok(textStatus);
        $rootScope.apply();
        if (errorCallback) {
          errorCallback(textStatus);
        }
      });
      jqxhr.always((jqXHR, textStatus, errorThrown) => { });
    },

    internalAjax: function internalAjax(action, data, info, successCallback, errorCallback) {
      const type = data && data.hasOwnProperty('type') ? `/${data.type}` : '';
      info = info ? info : new AjaxInfo();

      info.update(true, `Requête ${action}${type} en cours...`, true);
      $rootScope.apply();
      this.ajax(
        'api/ajax.php',
        'POST',
        { action: action, data: data },
        info,
        successCallback,
        errorCallback
      );
      return info;
    }
  };
}

angular.module('App').factory('ajaxService', ['$rootScope', ajaxService]);
