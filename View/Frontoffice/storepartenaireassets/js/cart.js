(function () {
  function normalizeTotal(txt) {
    var n = (txt || '').replace(/[^0-9.,]/g, '').replace(',', '.');
    return parseFloat(n) || 0;
  }
  function showToast(msg) {
    var toast = document.getElementById('cartToast');
    if (!toast) return;
    toast.textContent = msg;
    toast.style.display = 'block';
    setTimeout(function () { toast.style.display = 'none'; }, 2000);
  }
  function onRemoveClick(e) {
    var ok = window.confirm('Voulez-vous retirer cet article du panier ?');
    if (!ok) { e.preventDefault(); }
  }
  function onClearClick(e) {
    var ok = window.confirm('Voulez-vous vider tout le panier ?');
    if (!ok) { e.preventDefault(); }
  }
  function disableCheckoutIfNeeded() {
    var row = document.querySelector('.cart-total-row');
    var totalTxt = row ? (row.querySelectorAll('span')[1] ? row.querySelectorAll('span')[1].textContent : '') : '';
    var totalVal = normalizeTotal(totalTxt);
    var hasItems = document.querySelectorAll('.cart-items .game-card').length > 0;
    var checkout = document.getElementById('btnCheckout');
    if (!checkout) return;
    if (!hasItems || totalVal <= 0) {
      checkout.classList.add('disabled');
      checkout.setAttribute('aria-disabled', 'true');
    } else {
      checkout.classList.remove('disabled');
      checkout.removeAttribute('aria-disabled');
    }
  }
  function initVanilla() {
    var openBtn = document.getElementById('btnCheckout');
    var overlay = document.getElementById('overlayCheckout');
    var closeBtn = document.getElementById('closeCheckout');
    var cancelBtn = document.getElementById('cancelCheckout');
    var form = document.getElementById('checkoutForm');

    function show() {
      if (overlay) {
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden'; // Empêche le scroll
      }
    }

    function hide() {
      if (overlay) {
        overlay.classList.remove('show');
        document.body.style.overflow = ''; // Rétablit le scroll
      }
    }

    function setError(name, msg) {
      var el = form ? form.querySelector('.checkout-error[data-for="' + name + '"]') : null;
      if (el) {
        el.textContent = msg;
        // Highlight du champ en erreur
        var input = form.querySelector('[name="' + name + '"]');
        if (input) {
          input.style.borderColor = 'var(--accent)';
        }
      }
    }

    function clearErrors() {
      if (form) {
        form.querySelectorAll('.checkout-error').forEach(function (e) {
          e.textContent = '';
        });
        // Réinitialise les bordures
        form.querySelectorAll('.checkout-input, .checkout-select').forEach(function (input) {
          input.style.borderColor = '';
        });
      }
    }

    function validEmail(v) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
    }

    function validPhone(v) {
      return (/^\d{8,}$/).test((v || '').replace(/\D/g, ''));
    }

    // Événements
    document.addEventListener('click', function (ev) {
      var el = ev.target.closest('.js-remove-line');
      if (el) onRemoveClick(ev);
    });

    var clearBtn = document.getElementById('btnClearCart');
    if (clearBtn) clearBtn.addEventListener('click', onClearClick);

    if (openBtn) openBtn.addEventListener('click', function (e) {
      e.preventDefault();
      show();
    });

    if (closeBtn) closeBtn.addEventListener('click', function () { hide(); });
    if (cancelBtn) cancelBtn.addEventListener('click', function () { hide(); });

    if (overlay) overlay.addEventListener('click', function (e) {
      if (e.target === overlay) hide();
    });

    if (form) form.addEventListener('submit', function (e) {
      e.preventDefault(); // Important : empêche la soumission immédiate
      clearErrors();

      var data = new FormData(form);
      var ok = true;

      // Validation
      var name = (data.get('name') || '').trim();
      if (name.length < 3) {
        setError('name', 'Le nom doit contenir au moins 3 caractères');
        ok = false;
      }

      var email = (data.get('email') || '').trim();
      if (!validEmail(email)) {
        setError('email', 'Veuillez entrer un email valide');
        ok = false;
      }

      var phone = (data.get('phone') || '');
      if (!validPhone(phone)) {
        setError('phone', 'Le numéro doit contenir au moins 8 chiffres');
        ok = false;
      }

      var address = (data.get('address') || '').trim();
      if (address.length < 5) {
        setError('address', 'L\'adresse doit contenir au moins 5 caractères');
        ok = false;
      }

      var city = (data.get('city') || '').trim();
      if (city.length < 2) {
        setError('city', 'La ville doit contenir au moins 2 caractères');
        ok = false;
      }

      var shipping = (data.get('shipping') || '');
      if (!shipping) {
        setError('shipping', 'Veuillez choisir un mode de livraison');
        ok = false;
      }

      if (ok) {
        // Si validation OK, soumettre le formulaire
        this.submit();
      }
    });

    // Gestion des toasts
    var params = new URLSearchParams(window.location.search);
    if (params.get('updated') === '1') showToast('Panier mis à jour');
    if (params.get('removed') === '1') showToast('Article retiré');
    if (params.get('cleared') === '1') showToast('Panier vidé');
    if (params.get('order') === 'success') showToast('Commande confirmée !');
    if (params.get('order') === 'invalid') {
      var errorMsg = params.get('error') || 'Erreur dans le formulaire de commande';
      showToast(decodeURIComponent(errorMsg));
    }

    // Clear URL parameters after showing the toast to prevent showing error on refresh
    if (params.has('updated') || params.has('removed') || params.has('cleared') || params.has('order')) {
      var cleanUrl = window.location.pathname + window.location.search.split('&updated=')[0].split('&removed=')[0].split('&cleared=')[0].split('&order=')[0].split('?updated=')[0].split('?removed=')[0].split('?cleared=')[0].split('?order=')[0];
      if (cleanUrl.endsWith('?')) cleanUrl = cleanUrl.slice(0, -1);
      if (cleanUrl.endsWith('&')) cleanUrl = cleanUrl.slice(0, -1);
      history.replaceState(null, '', cleanUrl || window.location.pathname);
    }

    disableCheckoutIfNeeded();
  }
  function initJQuery($) {
    $(document).on('click', '.js-remove-line', onRemoveClick);
    $('#btnClearCart').on('click', onClearClick);
    var overlay = $('#overlayCheckout');
    $('#btnCheckout').on('click', function (e) { e.preventDefault(); overlay.addClass('show'); });
    $('#closeCheckout, #cancelCheckout').on('click', function () { overlay.removeClass('show'); });
    overlay.on('click', function (e) { if (e.target === overlay[0]) overlay.removeClass('show'); });
    $('#checkoutForm').on('submit', function (e) {
      var form = this;
      function setError(name, msg) { var el = form.querySelector('.checkout-error[data-for="' + name + '"]'); if (el) el.textContent = msg; }
      function clearErrors() { form.querySelectorAll('.checkout-error').forEach(function (e) { e.textContent = ''; }); }
      function validEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }
      function validPhone(v) { return (/^\d{8,}$/).test((v || '').replace(/\D/g, '')); }
      clearErrors();
      var data = $(form).serializeArray().reduce(function (acc, cur) { acc[cur.name] = cur.value; return acc; }, {});
      var ok = true;
      if ((data.name || '').trim().length < 3) { setError('name', 'Nom invalide'); ok = false; }
      if (!validEmail((data.email || '').trim())) { setError('email', 'Email invalide'); ok = false; }
      if (!validPhone((data.phone || ''))) { setError('phone', 'Téléphone invalide'); ok = false; }
      if ((data.address || '').trim().length < 5) { setError('address', 'Adresse invalide'); ok = false; }
      if ((data.city || '').trim().length < 2) { setError('city', 'Ville invalide'); ok = false; }
      if (!(data.shipping || '')) { setError('shipping', 'Choisir la livraison'); ok = false; }
      if (!ok) { e.preventDefault(); }
    });
    var params = new URLSearchParams(window.location.search);
    var toast = $('#cartToast');
    function showToastJQ(msg) { toast.text(msg).fadeIn(150); setTimeout(function () { toast.fadeOut(300); }, 2000); }
    if (params.get('updated') === '1') showToastJQ('Panier mis à jour');
    if (params.get('removed') === '1') showToastJQ('Article retiré');
    if (params.get('cleared') === '1') showToastJQ('Panier vidé');
    if (params.get('order') === 'success') showToastJQ('Commande confirmée');
    if (params.get('order') === 'invalid') {
      var errorMsg = params.get('error') || 'Commande invalide';
      showToastJQ(decodeURIComponent(errorMsg));
    }

    // Clear URL parameters to prevent showing error on refresh
    if (params.has('updated') || params.has('removed') || params.has('cleared') || params.has('order')) {
      var cleanUrl = window.location.pathname + window.location.search.split('&updated=')[0].split('&removed=')[0].split('&cleared=')[0].split('&order=')[0].split('?updated=')[0].split('?removed=')[0].split('?cleared=')[0].split('?order=')[0];
      if (cleanUrl.endsWith('?')) cleanUrl = cleanUrl.slice(0, -1);
      if (cleanUrl.endsWith('&')) cleanUrl = cleanUrl.slice(0, -1);
      history.replaceState(null, '', cleanUrl || window.location.pathname);
    }
    var totalTxt = $('.cart-total-row span').last().text();
    var totalVal = normalizeTotal(totalTxt);
    var hasItems = $('.cart-items .game-card').length > 0;
    if (!hasItems || totalVal <= 0) {
      $('#btnCheckout').addClass('disabled').attr('aria-disabled', 'true');
    }
  }
  if (window.jQuery) {
    jQuery(function ($) { initJQuery($); });
  } else {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initVanilla);
    } else {
      initVanilla();
    }
  }
})();
