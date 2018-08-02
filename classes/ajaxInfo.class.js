class AjaxInfo {
  constructor() {
    this.pending = false;
    this.message = '';
    this.status = true;
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
