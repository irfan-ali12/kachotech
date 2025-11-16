(function (){
  'use strict';

  // Carousel logic (same as prototype)
  const slides  = Array.from(document.querySelectorAll('[data-kt-slide]'));
  const tabs    = Array.from(document.querySelectorAll('[data-kt-tab]'));
  const rows    = Array.from(document.querySelectorAll('.kt-products-row'));
  const nextBtn = document.getElementById('kt-next');
  let current   = 0;
  let timer     = null;
  const AUTO_MS = 9000;

  function setActive(index){
    if(index === current) return;
    if(index < 0) index = slides.length - 1;
    if(index >= slides.length) index = 0;

    slides[current]?.classList.remove('kt-slide-active');
    slides[index]?.classList.add('kt-slide-active');

    tabs[current]?.classList.remove('kt-tab-active');
    tabs[index]?.classList.add('kt-tab-active');

    rows[current]?.classList.remove('kt-products-row-active');
    rows[index]?.classList.add('kt-products-row-active');

    current = index;
  }

  function goNext(){ setActive(current + 1); }
  function restartAuto(){ if(timer) clearInterval(timer); timer = setInterval(goNext, AUTO_MS); }

  tabs.forEach((tab, idx) => {
    tab.addEventListener('click', () => { setActive(idx); restartAuto(); });
  });
  nextBtn?.addEventListener('click', () => { goNext(); restartAuto(); });
  rows[0]?.classList.add('kt-products-row-active');
  restartAuto();

  // Simple toast
  function toast(msg){
    const t = document.createElement('div'); t.className = 'kt-hero-toast'; t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(()=> t.classList.add('kt-hero-toast--out'), 1800);
    setTimeout(()=> t.remove(), 2600);
  }

  // AJAX add-to-cart handlers
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.kt-hero-add-to-cart');
    if (!btn) return;
    e.preventDefault();
    const pid = btn.dataset.productId;
    if (!pid) return;
    btn.disabled = true; btn.textContent = 'Adding...';

    fetch( KT_AJAX.ajax_url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: new URLSearchParams({ action: 'kt_hero_add_to_cart', product_id: pid, quantity: 1, nonce: KT_AJAX.hero_nonce })
    }).then(r=>r.json()).then(json => {
      if (json && json.success) {
        btn.textContent = 'Added';
        toast('Added to cart');
        // update cart count badges if present
        const badges = document.querySelectorAll('.kt-cart-count');
        badges.forEach(b => b.textContent = json.data.count || '0');
        document.dispatchEvent(new CustomEvent('kt_cart_updated', { detail: json.data }));
      } else {
        btn.textContent = 'Try again';
        toast( (json && json.data && json.data.message) ? json.data.message : 'Unable to add to cart' );
      }
    }).catch(err=>{
      console.error(err);
      toast('Unable to add to cart');
      btn.textContent = 'Try again';
    }).finally(()=>{
      setTimeout(()=>{ btn.disabled = false; btn.textContent = 'Add to Cart'; }, 1200);
    });
  });

})();
