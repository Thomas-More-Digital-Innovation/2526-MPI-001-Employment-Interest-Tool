@once
<script>
(function(){
  function localizeAll(){
    document.querySelectorAll('[data-iso-datetime]').forEach(function(el){
      try{
        var iso = el.getAttribute('data-iso-datetime');
        if(!iso) return;
        var dt = new Date(iso);
        if (isNaN(dt.getTime())) return;
        // Use user's locale and timezone automatically
        var formatted = new Intl.DateTimeFormat(undefined, {
          day: '2-digit', month: 'short', year: 'numeric',
          hour: '2-digit', minute: '2-digit'
        }).format(dt);
        el.textContent = formatted;
      }catch(e){console.error(e)}
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', localizeAll);
  } else {
    localizeAll();
  }
})();
</script>
@endonce
