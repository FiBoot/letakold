class DataRow {
  constructor(json) {
    this.id = json.id ? parseInt(json.id) : 0;
    this.account_id = json.account_id ? parseInt(json.account_id) : 0;
    this.type = json.type ? `${json.type}` : ``;
    this.name = json.name ? `${json.name}` : ``;
    this.data =
      this.type === 'dnd_sheet' ? { sheet: json.data.slice(10, json.data.length - 1) } : JSON.parse(json.data);
    this.creation_date = json.creation_date ? new Date(json.creation_date) : new Date();
    this.public = json.public ? json.public : false;
  }
}
