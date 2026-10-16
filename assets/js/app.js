// küçük yardımcılar
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('input[name=plaka]').forEach(i => {
    i.addEventListener('input', () => { i.value = i.value.toLocaleUpperCase('tr-TR'); });
  });
});
