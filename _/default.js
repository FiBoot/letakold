function msplice(tab, item) {
  const index = tab.indexOf(item);
  if (index >= 0) {
    tab.splice(index, 1);
    return true;
  }
  return false;
}
