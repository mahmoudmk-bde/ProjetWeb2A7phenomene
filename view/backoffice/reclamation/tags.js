(function(){
  function applyTagFilter(tag){
    const list = document.querySelectorAll('[data-reclamation-item]');
    list.forEach(el=>{
      const tags = (el.getAttribute('data-tags')||'').split(',').map(t=>t.trim());
      el.style.display = (tags.includes(tag) ? '' : 'none');
    });
  }
  document.addEventListener('click', function(e){
    const pill = e.target.closest('.tag-pill[data-tag]');
    if(!pill) return;
    const tag = pill.getAttribute('data-tag');
    applyTagFilter(tag);
  });
})();
