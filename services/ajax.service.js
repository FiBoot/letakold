class AjaxService {
  Ajax(url, method, data, successCallback, errorCallback) {
    var jqxhr = $.ajax({
      url: url,
      method: method,
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      data: JSON.stringify(data)
    });

    if (successCallback) {
      jqxhr.done(successCallback);
    }
    if (errorCallback) {
      jqxhr.fail(errorCallback);
    }
  }
  InternalAjax(action, type, data, successCallback, errorCallback) {
    this.Ajax("api/ajax.php", "POST", { action: action, type: type, data: data }, successCallback, errorCallback);
  }
}
angular.module("App").service("ajaxService", AjaxService);
