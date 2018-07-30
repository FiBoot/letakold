class Data {
  constructor(data) {
    this.id = data.id ? parseInt(data.id) : 0;
    this.account_id = data.accountId ? parseInt(data.accountId) : 0;
    this.name = data.name ? data.name : '';
    // this.data = data.data ? JSON.parse(data.data) : null;
    this.data = data.data;
    this.type = data.type ? data.type : '';
    this.creation_date = data.creation_date ? new Date(data.creation_date) : new Date();
    this.public = data.public ? data.public : false;
  }
}
