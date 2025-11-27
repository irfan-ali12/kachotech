(function(){
  // ----- Quantity -----
  window.ktSpChangeQty = function(delta){
    var input = document.getElementById('kt-sp-qty');
    if (!input) return;
    var val = parseInt(input.value || '1', 10);
    if (isNaN(val)) val = 1;
    val = val + delta;
    if (val < 1) val = 1;
    input.value = val;
  };

  // ----- Gallery -----
  var mainImg = document.getElementById('kt-sp-main-image');
  var thumbBtns  = document.querySelectorAll('.kt-sp-thumb-btn');
  var current = 0;

  function setActive(i){
    if (!thumbBtns.length || !mainImg) return;
    current = i;
    thumbBtns.forEach(function(btn, idx){
      if (idx === i) {
        btn.classList.remove('border','border-slate-200');
        btn.classList.add('border-2','border-kt-primary');
      } else {
        btn.classList.remove('border-2','border-kt-primary');
        btn.classList.add('border','border-slate-200');
      }
    });
    var src = thumbBtns[i].getAttribute('data-src');
    if (src) mainImg.src = src;
  }

  thumbBtns.forEach(function(btn, idx){
    btn.addEventListener('click', function(){
      setActive(idx);
    });
  });

  var prev = document.getElementById('kt-sp-thumb-prev');
  var next = document.getElementById('kt-sp-thumb-next');

  if (prev) prev.addEventListener('click', function(){
    if (!thumbBtns.length) return;
    setActive((current - 1 + thumbBtns.length) % thumbBtns.length);
  });

  if (next) next.addEventListener('click', function(){
    if (!thumbBtns.length) return;
    setActive((current + 1) % thumbBtns.length);
  });

  // ----- Tabs -----
  var tabBtns = document.querySelectorAll('.kt-sp-tab-btn');
  var panels = {
    description: document.getElementById('kt-sp-tab-description'),
    specification: document.getElementById('kt-sp-tab-specification'),
    reviews: document.getElementById('kt-sp-tab-reviews')
  };

  tabBtns.forEach(function(btn){
    btn.addEventListener('click', function(){
      var target = btn.getAttribute('data-tab');
      tabBtns.forEach(function(b){
        if (b === btn) {
          b.classList.add('border-kt-primary','text-kt-primary','font-semibold');
          b.classList.remove('text-slate-500','border-transparent','font-medium');
        } else {
          b.classList.remove('border-kt-primary','text-kt-primary','font-semibold');
          b.classList.add('border-transparent','text-slate-500','font-medium');
        }
      });
      Object.keys(panels).forEach(function(key){
        if (!panels[key]) return;
        if (key === target) {
          panels[key].classList.remove('hidden');
        } else {
          panels[key].classList.add('hidden');
        }
      });
    });
  });
})();
