(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle floating compare button click
        $(document).on('click', '.custom-floating-compare-btn', function(e) {
            e.preventDefault();
            var $popup = $('.custom-compare-popup');
            if ($popup.hasClass('active')) {
                $popup.removeClass('active');
            } else {
                $popup.addClass('active');
            }
        });

        // Close popup
        $(document).on('click', '.custom-compare-popup-close', function(e) {
            e.preventDefault();
            $('.custom-compare-popup').removeClass('active');
        });

        // Update badge count
        function updateCompareCount() {
            var $bar = $('.custom-compare-popup');
            if ($bar.length > 0) {
                var count = $bar.find('.compare-slot-item.filled-slot').length;
                $('.custom-floating-compare-btn .compare-count').text(count);
                if (count > 0) {
                    $('.custom-floating-compare-btn').show();
                } else {
                    $('.custom-floating-compare-btn').hide();
                }
            }
        }

        // Handle live search
        $(document).on('keyup', '.custom-compare-search-input', function() {
            var $input = $(this);
            var keyword = $input.val();
            var $resultsContainer = $input.siblings('.custom-compare-search-results');

            if (keyword.length < 2) {
                $resultsContainer.empty().hide();
                return;
            }

            $resultsContainer.show().html('<div class="cc-loading">Searching...</div>');

            $.ajax({
                url: custom_compare_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'custom_compare_search',
                    keyword: keyword
                },
                success: function(response) {
                    $resultsContainer.empty();
                    if (response.success && response.data.length > 0) {
                        var html = '<ul>';
                        $.each(response.data, function(index, product) {
                            html += '<li class="cc-search-item" data-id="' + product.id + '">' + product.title + '</li>';
                        });
                        html += '</ul>';
                        $resultsContainer.html(html);
                    } else {
                        $resultsContainer.html('<div class="cc-no-results">No products found.</div>');
                    }
                },
                error: function() {
                    $resultsContainer.empty().hide();
                }
            });
        });

        // Hide results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.custom-compare-search-wrapper').length) {
                $('.custom-compare-search-results').hide();
            }
        });

        // Show results when clicking input if it has value
        $(document).on('focus', '.custom-compare-search-input', function() {
            if ($(this).val().length >= 2) {
                $(this).siblings('.custom-compare-search-results').show();
            }
        });

        // Handle product selection from search results
        $(document).on('click', '.cc-search-item', function() {
            var productId = $(this).data('id');
            var $input = $(this).closest('.custom-compare-search-wrapper').find('.custom-compare-search-input');
            var productTitle = $(this).text();

            $input.val(productTitle);
            $(this).closest('.custom-compare-search-results').empty().hide();

            var $dummyBtn = $('<a href="#" class="ct-compare-button" data-product_id="' + productId + '"></a>');
            $('body').append($dummyBtn);
            $dummyBtn.trigger('click');
            $dummyBtn.remove();
        });

        // Initial setup
        setTimeout(updateCompareCount, 500);

        // Update on blocksy event
        $(document).on('blocksy:frontend:woocommerce:compare:update', function() {
            setTimeout(updateCompareCount, 100);
        });
    });
})(jQuery);
