(function($) {
  'use strict';

  $(document).ready(function() {

    // ===== GLOBAL VARIABLES =====
    var currentFilters = {
      categories: '',
      availability: '',
      brands: '',
      min_rating: 0,
      max_price: 0,
      search: '',
      orderby: 'date',
      paged: 1
    };

    var minPrice = typeof ktShopAjax !== 'undefined' ? ktShopAjax.min_price : 0;
    var maxPrice = typeof ktShopAjax !== 'undefined' ? ktShopAjax.max_price : 25000;
    var priceRange = maxPrice - minPrice;
    var postsPerPage = typeof ktShopAjax !== 'undefined' ? ktShopAjax.posts_per_page : 12;

    console.log('Price Range:', minPrice, 'to', maxPrice, 'Range:', priceRange);
    console.log('Posts per page:', postsPerPage);

    // ===== INITIALIZE FROM URL PARAMETERS =====
    // Check if there's a search parameter in the URL (from header search)
    var urlParams = new URLSearchParams(window.location.search);
    var urlSearchParam = urlParams.get('s');
    
    if (urlSearchParam) {
      console.log('Search parameter found in URL:', urlSearchParam);
      
      // Populate search field with the parameter value
      $('#kt-product-search').val(urlSearchParam);
      $('#kt-product-search-mobile').val(urlSearchParam);
      
      // Update currentFilters with search term
      currentFilters.search = urlSearchParam;
      
      // Trigger the filter application with the search parameter
      // Use setTimeout to ensure DOM is fully ready
      setTimeout(function() {
        applyFiltersAjax(urlSearchParam, 1);
      }, 100);
    }

    // ===== MOBILE FILTER DRAWER TOGGLE =====
    var $filterDrawer = $('#kt-filter-drawer');
    var $openFilterBtn = $('#kt-open-filters');
    var $closeFilterBtn = $('#kt-close-filters');

    $openFilterBtn.on('click', function() {
      $filterDrawer.addClass('kt-visible');
      $('body').addClass('kt-no-scroll');
    });

    $closeFilterBtn.on('click', function() {
      $filterDrawer.removeClass('kt-visible');
      $('body').removeClass('kt-no-scroll');
    });

    $filterDrawer.on('click', function(e) {
      if (e.target === this || $(e.target).hasClass('kt-filter-overlay')) {
        $filterDrawer.removeClass('kt-visible');
        $('body').removeClass('kt-no-scroll');
      }
    });

    // ===== BRAND PILL TOGGLE =====
    $(document).on('click', '.kt-pill:not(.kt-pill-mobile)', function(e) {
      e.preventDefault();
      $(this).toggleClass('kt-pill-active');
      applyFiltersAjax();
    });

    // Mobile brand pills
    $(document).on('click', '.kt-pill-mobile', function(e) {
      e.preventDefault();
      $(this).toggleClass('kt-pill-active');
    });

    // ===== RATING FILTER =====
    $(document).on('change', 'input[name="rating"]', function() {
      applyFiltersAjax();
    });

    // Mobile rating filter
    $(document).on('change', 'input[name="rating-mobile"]', function() {
      // Don't apply immediately for mobile - wait for apply button
    });

    // ===== CATEGORY FILTER - LIVE =====
    $(document).on('change', '.kt-category-filter:not(.kt-category-filter-mobile)', function() {
      applyFiltersAjax();
    });

    // ===== AVAILABILITY FILTER - LIVE =====
    $(document).on('change', '.kt-availability-filter:not(.kt-availability-filter-mobile)', function() {
      applyFiltersAjax();
    });

    // ===== PRICE RANGE FILTER WITH TOOLTIP =====
    var priceTimeout;
    var $priceRange = $('#kt-price-range');
    var $priceRangeMobile = $('#kt-price-range-mobile');
    var $tooltip = $('#kt-price-tooltip');
    var $tooltipMobile = $('#kt-price-tooltip-mobile');

    // Initialize tooltips to show max price on page load
    $('#kt-price-tooltip-text').text('Rs ' + maxPrice.toLocaleString());
    $('#kt-price-tooltip-text-mobile').text('Rs ' + maxPrice.toLocaleString());

    // Update tooltip position and text - SIMPLIFIED since slider now uses actual price values
function updatePriceTooltip(value, $slider, $tooltipElement, isMobile) {
  // The slider value is now the actual price, no calculation needed
  var displayPrice = parseInt(value);
  
  var tooltipTextId = isMobile ? 'kt-price-tooltip-text-mobile' : 'kt-price-tooltip-text';
  $('#' + tooltipTextId).text('Rs ' + displayPrice.toLocaleString());
  
  // Position tooltip based on the price value's position in the range
  var sliderMin = parseInt($slider.attr('min'));
  var sliderMax = parseInt($slider.attr('max'));
  var range = sliderMax - sliderMin;
  var position = ((displayPrice - sliderMin) / range) * 100;
  
  $tooltipElement.css('left', position + '%');
  $slider.css('--value', position);
  
  console.log('Price slider - Min:', sliderMin, 'Max:', sliderMax, 'Value:', displayPrice, 'Position:', position + '%');
}

    // Desktop price range events
    setupPriceRange($priceRange, $tooltip, false);
    
    // Mobile price range events
    setupPriceRange($priceRangeMobile, $tooltipMobile, true);

    function setupPriceRange($slider, $tooltipElement, isMobile) {
      // Show tooltip on input
      $slider.on('input', function(e) {
        $tooltipElement.addClass('kt-price-tooltip-visible');
        var value = $(this).val();
        updatePriceTooltip(value, $slider, $tooltipElement, isMobile);
      });

      // Hide tooltip and apply filter on change (desktop only)
      if (!isMobile) {
        $slider.on('change', function() {
          clearTimeout(priceTimeout);
          var value = $(this).val();
          updatePriceTooltip(value, $slider, $tooltipElement, isMobile);
          
          priceTimeout = setTimeout(function() {
            $tooltipElement.removeClass('kt-price-tooltip-visible');
            applyFiltersAjax();
          }, 500);
        });
      }

      $slider.on('mouseleave', function() {
        if (!isMobile) {
          setTimeout(function() {
            $tooltipElement.removeClass('kt-price-tooltip-visible');
          }, 300);
        }
      });
    }

    // ===== SEARCH FUNCTIONALITY =====
    var searchTimeout;
    
    // Desktop search
    $(document).on('keyup', '#kt-product-search', function(e) {
      if (e.which === 13) {
        e.preventDefault();
        clearTimeout(searchTimeout);
        var search = $(this).val();
        applyFiltersAjax(search);
        return false;
      }
      
      clearTimeout(searchTimeout);
      var search = $(this).val();
      
      searchTimeout = setTimeout(function() {
        applyFiltersAjax(search);
      }, 400);
    });

    $(document).on('click', '.kt-search-btn', function(e) {
      e.preventDefault();
      var $searchInput = $(this).closest('.kt-search-bar').find('.kt-search-input');
      var search = $searchInput.val();
      applyFiltersAjax(search);
      return false;
    });

    // Mobile search
    $(document).on('keyup', '#kt-product-search-mobile', function(e) {
      if (e.which === 13) {
        e.preventDefault();
        clearTimeout(searchTimeout);
        var search = $(this).val();
        applyFiltersAjax(search);
        return false;
      }
      
      clearTimeout(searchTimeout);
      var search = $(this).val();
      
      searchTimeout = setTimeout(function() {
        applyFiltersAjax(search);
      }, 400);
    });

    // ===== APPLY FILTERS BUTTONS =====
    $(document).on('click', '#kt-apply-filters-desktop', function() {
      applyFiltersAjax();
    });

    $(document).on('click', '#kt-apply-filters-mobile', function() {
      // Sync mobile filters to currentFilters before applying
      syncMobileFilters();
      applyFiltersAjax();
      $filterDrawer.removeClass('kt-visible');
      $('body').removeClass('kt-no-scroll');
    });

    // ===== CLEAR FILTERS BUTTONS =====
    $(document).on('click', '#kt-clear-filters-desktop', function() {
      clearAllFilters();
      applyFiltersAjax('', 1);
    });

    $(document).on('click', '#kt-clear-filters-mobile', function() {
      clearAllFilters();
      applyFiltersAjax('', 1);
      $filterDrawer.removeClass('kt-visible');
      $('body').removeClass('kt-no-scroll');
    });

    // Clear filters from AJAX no results
    $(document).on('click', '#kt-clear-filters-ajax', function() {
      clearAllFilters();
      applyFiltersAjax('', 1);
    });

    function clearAllFilters() {
      // Clear all checkboxes
      $('.kt-category-filter').prop('checked', false);
      $('.kt-category-filter-mobile').prop('checked', false);
      $('.kt-availability-filter').prop('checked', false);
      $('.kt-availability-filter-mobile').prop('checked', false);
      $('input[name="rating"]').prop('checked', false);
      $('input[name="rating-mobile"]').prop('checked', false);
      
      // Reset price range to max value (actual price, not percentage)
      $priceRange.val(maxPrice);
      $priceRangeMobile.val(maxPrice);
      updatePriceTooltip(maxPrice, $priceRange, $tooltip, false);
      updatePriceTooltip(maxPrice, $priceRangeMobile, $tooltipMobile, true);
      
      // Clear ALL brand pills
      $('.kt-pill').removeClass('kt-pill-active');
      $('.kt-pill-mobile').removeClass('kt-pill-active');

      // Clear search inputs
      $('#kt-product-search').val('');
      $('#kt-product-search-mobile').val('');

      // Reset currentFilters
      currentFilters = {
        categories: '',
        availability: '',
        brands: '',
        min_rating: 0,
        max_price: maxPrice,
        search: '',
        orderby: $('#kt-sort-select').val() || 'date',
        paged: 1
      };

      console.log('All filters cleared');
    }

    // ===== SORT FUNCTIONALITY =====
    $(document).on('change', '#kt-sort-select', function() {
      var orderby = $(this).val();
      currentFilters.orderby = orderby;
      applyFiltersAjax(currentFilters.search || '', 1, orderby);
    });

    // ===== SYNC MOBILE FILTERS =====
    function syncMobileFilters() {
      // Sync categories
      var mobileCategories = [];
      $('.kt-category-filter-mobile:checked').each(function() {
        mobileCategories.push($(this).val());
      });
      currentFilters.categories = mobileCategories.join(',');

      // Sync availability
      var mobileAvailability = [];
      $('.kt-availability-filter-mobile:checked').each(function() {
        mobileAvailability.push($(this).val());
      });
      currentFilters.availability = mobileAvailability.join(',');

      // Sync brands
      var mobileBrands = [];
      $('.kt-pill-mobile.kt-pill-active').each(function() {
        mobileBrands.push($(this).data('brand'));
      });
      currentFilters.brands = mobileBrands.join(',');

      // Sync rating
      var mobileRating = $('input[name="rating-mobile"]:checked').val() || '';
      currentFilters.min_rating = mobileRating;

      // Sync price - FIXED PRICE CALCULATION
      var mobileSliderValue = $priceRangeMobile.val();
      var mobilePriceMax = minPrice + (mobileSliderValue / 100) * (maxPrice - minPrice);
      currentFilters.max_price = Math.round(mobilePriceMax);

      // Sync search
      var mobileSearch = $('#kt-product-search-mobile').val();
      if (mobileSearch) {
        currentFilters.search = mobileSearch;
      }

      console.log('Mobile filters synced:', currentFilters);
    }

    // ===== MAIN FILTER FUNCTION =====
    function applyFiltersAjax(search, page, orderby) {
      search = search || currentFilters.search || '';
      page = page || currentFilters.paged || 1;
      orderby = orderby || currentFilters.orderby || $('#kt-sort-select').val() || 'date';
      
      // Get selected filters from desktop
      var categories = [];
      $('.kt-category-filter:checked').each(function() {
        categories.push($(this).val());
      });

      var availability = [];
      $('.kt-availability-filter:checked').each(function() {
        availability.push($(this).val());
      });

      var brands = [];
      $('.kt-pill.kt-pill-active:not(.kt-pill-mobile)').each(function() {
        brands.push($(this).data('brand'));
      });

      var rating = $('input[name="rating"]:checked').val() || '';
      
      // FIXED PRICE CALCULATION - Slider value is now the actual price
var sliderValue = parseInt($priceRange.val());
var priceMax = sliderValue;

      console.log('Slider value:', sliderValue, 'Max price to filter:', priceMax, 'Min price:', minPrice, 'Max available price:', maxPrice);

      // Update currentFilters
      currentFilters = {
        categories: categories.length > 0 ? categories.join(',') : '',
        availability: availability.length > 0 ? availability.join(',') : '',
        brands: brands.length > 0 ? brands.join(',') : '',
        min_rating: rating,
        max_price: priceMax,
        search: search,
        orderby: orderby,
        paged: page
      };

      // Build filter data
      var filterData = {
        action: 'kt_filter_products',
        categories: currentFilters.categories,
        availability: currentFilters.availability,
        brands: currentFilters.brands,
        min_rating: currentFilters.min_rating,
        max_price: currentFilters.max_price,
        search: currentFilters.search,
        orderby: currentFilters.orderby,
        paged: currentFilters.paged,
        nonce: ktShopAjax.nonce,
        category_id: typeof ktShopAjax.category_id !== 'undefined' ? ktShopAjax.category_id : 0,
        is_category_page: typeof ktShopAjax.is_category_page !== 'undefined' ? ktShopAjax.is_category_page : false
      };

      console.log('Sending Filter Data:', filterData);

      // Show loading state
      showLoadingState();

      // Make AJAX request
      $.ajax({
        url: ktShopAjax.ajaxurl,
        type: 'POST',
        data: filterData,
        success: function(response) {
          console.log('AJAX Response Received:', response);
          handleAjaxSuccess(response);
        },
        error: function(xhr, status, error) {
          console.log('AJAX Error:', error);
          handleAjaxError();
        },
        complete: function() {
          hideLoadingState();
        }
      });
    }

    function showLoadingState() {
      $('#kt-products-container').css('opacity', '0.6').css('pointer-events', 'none');
      $('#kt-products-container').prepend(
        '<div class="kt-loading-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);display:flex;align-items:center;justify-content:center;z-index:10;">' +
        '<div class="kt-spinner" style="width:40px;height:40px;border:4px solid rgba(255,36,70,0.1);border-top:4px solid #ff2446;border-radius:50%;animation:spin 1s linear infinite;"></div>' +
        '</div>'
      );
    }

    function hideLoadingState() {
      $('#kt-products-container').css('opacity', '1').css('pointer-events', 'auto');
      $('.kt-loading-overlay').remove();
    }

    function handleAjaxSuccess(response) {
      if (response.success && response.data) {
        // Update products
        $('#kt-products-container').html(response.data.html);
        
        // Update pagination
        updatePagination(response.data.pagination, response.data.total_pages, response.data.current_page);
        
        // Update results count
        var resultsCount = response.data.total_products || 0;
        $('.kt-results-count').text(resultsCount + ' products found');
        
        // Update URL without reloading page
        updateUrlState();
        
        // Update current page
        currentFilters.paged = response.data.current_page || 1;
        
        console.log('Products updated successfully. Total:', resultsCount, 'Pages:', response.data.total_pages, 'Current Page:', response.data.current_page);
        
        // Ensure proper grid layout after AJAX update
        setTimeout(function() {
          ensureGridLayout();
        }, 100);
      } else {
        console.log('AJAX success but no data:', response);
        $('#kt-products-container').html(
          '<div class="kt-no-products-found" style="grid-column:1/-1;text-align:center;padding:40px;">' +
          '<p>No products found matching your criteria.</p>' +
          '<p><button class="kt-btn-link" id="kt-clear-filters-ajax">Clear all filters</button></p>' +
          '</div>'
        );
        $('.kt-pagination').empty();
        $('.kt-results-count').text('0 products found');
      }
    }

    function handleAjaxError() {
      $('#kt-products-container').html(
        '<div class="kt-ajax-error" style="grid-column:1/-1;text-align:center;padding:40px;">' +
        '<p>Error loading products. Please try again.</p>' +
        '</div>'
      );
      $('.kt-pagination').empty();
      $('.kt-results-count').text('0 products found');
    }

    function updatePagination(paginationHtml, totalPages, currentPage) {
      var $paginationContainer = $('.kt-pagination');
      
      if (paginationHtml && paginationHtml.length > 0 && totalPages > 1) {
        if ($paginationContainer.length === 0) {
          $paginationContainer = $('<div class="kt-pagination"></div>').insertAfter('#kt-products-container');
        }
        $paginationContainer.html(paginationHtml);
        console.log('Pagination updated. Total pages:', totalPages, 'Current page:', currentPage);
      } else {
        $paginationContainer.empty();
        console.log('Pagination hidden - single page or no pagination HTML');
      }
    }

    function updateUrlState() {
      var url = new URL(window.location);
      
      // Update URL parameters
      if (currentFilters.search) {
        url.searchParams.set('s', currentFilters.search);
      } else {
        url.searchParams.delete('s');
      }
      
      if (currentFilters.paged > 1) {
        url.searchParams.set('paged', currentFilters.paged);
      } else {
        url.searchParams.delete('paged');
      }
      
      if (currentFilters.orderby && currentFilters.orderby !== 'date') {
        url.searchParams.set('orderby', currentFilters.orderby);
      } else {
        url.searchParams.delete('orderby');
      }
      
      // Update URL without page reload
      window.history.replaceState({}, '', url);
    }

    // ===== PAGINATION LINK HANDLER =====
    $(document).on('click', '.kt-page-link', function(e) {
      e.preventDefault();
      
      var $link = $(this);
      var href = $link.attr('href');
      if (!href) return;
      
      // Extract page number from href
      var pageMatch = href.match(/paged=(\d+)/);
      var page = pageMatch ? parseInt(pageMatch[1]) : 1;
      
      console.log('Pagination clicked - Page:', page);
      
      // Scroll to products
      $('html, body').animate({
        scrollTop: $('#kt-products-container').offset().top - 100
      }, 300);
      
      // Apply filters with new page
      currentFilters.paged = page;
      applyFiltersAjax(currentFilters.search || '', page);
    });

    // ===== AJAX ADD TO CART =====
    $(document).on('click', '.ajax_add_to_cart', function(e) {
      e.preventDefault();
      var $button = $(this);
      var $card = $button.closest('.kt-product-card');
      
      // Prevent multiple clicks
      if ($button.data('loading')) return;
      
      $button.data('loading', true);
      var originalText = $button.text();
      $button.text('Adding...').css('opacity', '0.6');

      var productId = $button.data('product_id');
      var quantity = 1;

      $.ajax({
        url: ktShopAjax.ajaxurl,
        type: 'POST',
        data: {
          action: 'woocommerce_ajax_add_to_cart',
          product_id: productId,
          quantity: quantity,
          nonce: wc_add_to_cart_params.nonce
        },
        success: function(response) {
          if (response.error && response.product_url) {
            window.location.href = response.product_url;
            return;
          }
          
          // Show success feedback
          $card.css('background-color', '#c6f6d5').animate({
            backgroundColor: '#ffffff'
          }, 800);
          
          // Update cart fragments
          $(document.body).trigger('wc_fragment_refresh');
          
          // Show success message
          showAddToCartSuccess();
        },
        error: function() {
          alert('Error adding product to cart. Please try again.');
        },
        complete: function() {
          $button.data('loading', false);
          $button.text(originalText).css('opacity', '1');
        }
      });
    });

    function showAddToCartSuccess() {
      var $message = $('<div class="kt-cart-success" style="position:fixed;top:20px;right:20px;background:#10b981;color:white;padding:12px 20px;border-radius:6px;z-index:1000;box-shadow:0 4px 6px rgba(0,0,0,0.1);">Product added to cart!</div>');
      $('body').append($message);
      
      setTimeout(function() {
        $message.fadeOut(300, function() {
          $(this).remove();
        });
      }, 3000);
    }

    // ===== ENSURE GRID LAYOUT =====
    function ensureGridLayout() {
      var $productsContainer = $('#kt-products-container');
      var $productCards = $productsContainer.find('.kt-product-card');
      
      // Remove any empty grid columns that might break the layout
      $productsContainer.find('.kt-product-card:empty').remove();
      
      console.log('Grid layout ensured. Product cards:', $productCards.length);
    }

    // ===== INITIALIZE PAGE =====
    function initializePage() {
      console.log('Shop page initialized');
      console.log('Price range:', minPrice, 'to', maxPrice, 'Range:', priceRange);
      console.log('Posts per page:', postsPerPage);
      
      // Set initial filter state
      currentFilters.orderby = $('#kt-sort-select').val() || 'date';
      
      // Ensure initial grid layout
      setTimeout(function() {
        ensureGridLayout();
      }, 500);
    }

    // Initialize the page
    initializePage();

    // Add CSS for spinner animation and body scroll lock only
if (!$('#kt-shop-styles').length) {
  $('head').append(
    '<style id="kt-shop-styles">' +
    '@keyframes spin {0% {transform: rotate(0deg);}100% {transform: rotate(360deg);}}' +
    'body.kt-no-scroll {overflow: hidden;}' +
    '</style>'
  );
}

  });

})(jQuery);