class DataRow {
  constructor(json) {
    if (!json) {
      json = {};
    }
    this.id = json.id ? parseInt(json.id) : 0;
    this.account_id = json.account_id ? parseInt(json.account_id) : 0;
    this.type = json.type ? `${json.type}` : ``;
    this.name = json.name ? `${json.name}` : ``;
    this.data = json.data ? `${json.data}` : ``;
    this.creation_date = json.creation_date ? new Date(json.creation_date) : new Date();
    this.last_update = json.last_update ? new Date(json.last_update) : new Date();
    this.public = json.public ? json.public : false;
  }

  getDate(creation) {
    return creation
      ? this.creation_date.toLocaleString()
      : this.last_update.toLocaleString();
  }

  parseData() {
    try {
      this.data =
        this.type === 'dnd_sheet'
          ? { sheet: this.data.slice(10, this.data.length - 1) }
          : JSON.parse(this.data);
    } catch (e) {
      console.warn(`DataRow parseData JSON.parse`, e, this);
      return false;
    }
    return true;
  }
}
