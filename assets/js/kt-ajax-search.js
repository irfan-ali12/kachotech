(function ($) {
  $(function () {
    var $form = $('.kt-search-form');
    if (!$form.length || typeof ktAjaxSearch === 'undefined') return;

    var $input       = $form.find('.kt-search-input');
    var $suggestions = $form.find('.kt-search-suggestions');
    var $wrap        = $form.find('.kt-search-form-wrap');
    var $catHidden   = $form.find('input[name="product_cat"]');
    var typingTimer  = null;
    var lastTerm     = '';
    var minChars     = parseInt(ktAjaxSearch.minChars || 2, 10);

    function clearSuggestions() {
      $suggestions.attr('hidden', 'hidden').empty();
    }

    function renderLoading(term) {
      $suggestions
        .html(
          '<div class="kt-search-loading">' +
            '<span class="kt-search-spinner"></span>' +
            '<span class="kt-search-loading-text">Searching for “' +
              $('<div>').text(term).html() +
            '”...</span>' +
          '</div>'
        )
        .removeAttr('hidden');
    }

    function renderResults(items) {
      if (!items || !items.length) {
        $suggestions
          .html('<div class="kt-search-empty">No products found.</div>')
          .removeAttr('hidden');
        return;
      }

      var html = '<ul class="kt-search-suggestions-list">';
      items.forEach(function (item) {
        html += '<li class="kt-search-suggestion-item">' +
          '<a href="' + item.url + '">' +
            (item.thumb
              ? '<span class="kt-search-suggestion-thumb"><img src="' + item.thumb + '" alt=""></span>'
              : ''
            ) +
            '<span class="kt-search-suggestion-main">' +
              '<span class="kt-search-suggestion-title">' + item.title + '</span>' +
              (item.price_html
                ? '<span class="kt-search-suggestion-price">' + item.price_html + '</span>'
                : ''
              ) +
            '</span>' +
          '</a>' +
        '</li>';
      });
      html += '</ul>';

      $suggestions.html(html).removeAttr('hidden');
    }

    function performSearch(term) {
      var productCat = $catHidden.val() || '';

      $.ajax({
        url: ktAjaxSearch.ajaxUrl,
        method: 'GET',
        dataType: 'json',
        data: {
          action: 'kt_product_search',
          nonce: ktAjaxSearch.nonce,
          term: term,
          product_cat: productCat
        },
        success: function (resp) {
          if (resp && resp.success) {
            renderResults(resp.data);
          } else {
            clearSuggestions();
          }
        },
        error: function () {
          clearSuggestions();
        }
      });
    }

    // Typing in search
    $input.on('keyup', function (e) {
      // Ignore navigation keys
      if (['ArrowUp', 'ArrowDown', 'Enter', 'Escape'].indexOf(e.key) !== -1) return;

      clearTimeout(typingTimer);

      var term = $input.val().trim();
      if (term.length < minChars) {
        clearSuggestions();
        lastTerm = '';
        return;
      }

      lastTerm = term;
      renderLoading(term);

      typingTimer = setTimeout(function () {
        performSearch(term);
      }, 260);
    });

    // Show suggestions when refocusing if we already have some
    $input.on('focus', function () {
      if ($suggestions.children().length) {
        $suggestions.removeAttr('hidden');
      }
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function (e) {
      if (!$(e.target).closest('.kt-search-form-wrap').length) {
        $suggestions.attr('hidden', 'hidden');
      }
    });
  });
})(jQuery);
