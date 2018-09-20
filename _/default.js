function msplice(tab, item) {
  const index = tab.indexOf(item);
  if (index >= 0) {
    tab.splice(index, 1);
    return true;
  }
  return false;
}

function copyToClipboard(str) {
  const el = document.createElement('textarea');
  el.value = str;
  document.body.appendChild(el);
  el.select();
  document.execCommand('copy');
  document.body.removeChild(el);
}
