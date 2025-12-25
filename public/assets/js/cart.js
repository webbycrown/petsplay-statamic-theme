$(document).ready(function(){

    // CSRF token for Laravel
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ADD TO CART
    $(document).on('click', '.add-to-cart', function(e){
        e.preventDefault();
        let button = $(this);
      
        let isWishlist = button.data('type') === 'wishlist'; 
        let item = {
            product_id: button.data('id'),
            slug: button.data('slug'),
            title: button.data('title'),
            price: button.data('price'),
            quantity: button.data('quantity') || 1,
            image: button.data('image'),
            stock: button.data('stock'),
            size: button.data('size'),
            short_description: button.data('short_description'),
        };


        if (isWishlist) {
            
            $.post('/wishlist/remove', { product_id: item.product_id }, function (removeRes) {
                console.log(removeRes);
                updateWishlistCount(removeRes.totalQuantity);
                 if (removeRes.is_empty) {
                    updateWishlistCount(0);
                }
                
            // After removing from wishlist → add to cart
                addToCart(item);

            }).fail(function () {
                showNotification('Error removing item from wishlist', 'error');
            });

        } else {
        // Normal add to cart
            addToCart(item);
        }
    });

    function addToCart(item) {
        $.post('/cart/add', item, function(response){
            if(response.success) {
                updateCartUI(response);
                showNotification('Item added to cart!', 'success');
                window.location.href = '/cart';
            }
        }).fail(function(){
            showNotification('Error adding item to cart', 'error');
        });
    }

    // REMOVE ITEM
    $(document).on('click', '.remove-item', function(e){
        e.preventDefault();

        let id = $(this).data('id');
        let row = $(this).closest('tr');
        let isWishlist = $(this).data('type') === 'wishlist'; 

        let apiRoute = isWishlist ? '/wishlist/remove': '/cart/remove';

        $.post(apiRoute, {product_id: id}, function(response){
            if(response.success) {

                if (isWishlist && typeof response.totalQuantity !== 'undefined') {
                    console.log(response.totalQuantity);
                    // return false;
                    updateWishlistCount(response.totalQuantity);
                }

                // ✅ Reload if empty
                if (response.is_empty) {
                    updateWishlistCount(0);
                    location.reload();
                }

                row.fadeOut(300, function(){
                    $(this).remove();
                    updateCartUI(response);
                    if(response.is_empty) {
                        location.reload(); 
                    }

                });
                showNotification('Item removed from cart', 'success');
            }
        }).fail(function(){
            showNotification('Error removing item', 'error');
        });
    });

    // UPDATE QUANTITY (on input change)
    $(document).on('change', '.qty', function(){
        let input = $(this);
        let id = input.data('id');
        let quantity = parseInt(input.val()) || 1;

        if(quantity < 1) {
            input.val(1);
            quantity = 1;
        }

        updateCartItem(id, quantity, input);
    });

    // QUANTITY + BUTTON
    $(document).on('click', '.qtyplus', function(e){
        e.preventDefault();
        let input = $(this).siblings('.qty');
        let id = input.data('id');
        let quantity = parseInt(input.val()) || 0;
        let max = parseInt(input.data('max')) || 0;
         
        if (quantity < max) {
            quantity++;
            input.val(quantity);
            updateCartItem(id, quantity, input);
        }
    });

    // QUANTITY - BUTTON
    $(document).on('click', '.qtyminus', function(e){
        e.preventDefault();
        let input = $(this).siblings('.qty');
        let id = input.data('id');
        let quantity = parseInt(input.val()) || 1;
        if(quantity > 1) {
            quantity--;
            input.val(quantity);
            updateCartItem(id, quantity, input);
        }
    });

    // Update cart item via AJAX
    function updateCartItem(id, quantity, inputElem) {
        $.ajax({
            url: '/cart/update',
            method: 'POST',
            data: {
                product_id: id,
                quantity: quantity
            },
            success: function(response) {
                if(response.success) {
                    // Update the price for this row
                    let row = inputElem.closest('tr');
                    let item = response.items.find(i => i.product_id == id);
                    if(item) {
                        let totalPrice = (item.price * item.quantity).toFixed(2);
                        row.find('.item-total').text('$ ' + totalPrice + '/-');
                    }
                    updateCartUI(response);
                    updateCartCount(response.total_quantity);
                }
            },
            error: function(){
                showNotification('Error updating quantity', 'error');
            }
        });
    }

    // Update cart UI with new data
    function updateCartUI(cartData) {
        // Update subtotal
        $('.subtotal-amount').text(parseFloat(cartData.subtotal).toFixed(2));

        // Update discount
        $('.discount-amount').text(parseFloat(cartData.discount).toFixed(2));

        // Update delivery
        $('.delivery-amount').text(parseFloat(cartData.delivery).toFixed(2));

        // Update total
        $('.total-amount').text(parseFloat(cartData.total).toFixed(2));

        // Update cart count badge (if exists)
        $('.cart-count-badge').text(cartData.count);
        $('.cart-total-quantity').text(cartData.total_quantity);

        // Update cart count in header/navigation
        updateCartBadge(cartData.count);
    }

    // Update cart badge in header
    function updateCartBadge(count) {
        $('.cart-icon .badge, .cart-count, [data-cart-count]').text(count);
        if(count > 0) {
            $('.cart-icon .badge, .cart-count, [data-cart-count]').show();
        } else {
            $('.cart-icon .badge, .cart-count, [data-cart-count]').hide();
        }
    }

    // APPLY COUPON
    $(document).on('click', '.apply-promo-code', function (e) {
        e.preventDefault();

        const code = $('.promo-code-input').val().trim();
        if (!code) {
            showNotification('Please enter a promo code.', 'error');
            $('.coupon-message').text('Please enter a promo code.').css('color', 'red');
            return;
        }

        $.post('/cart/coupon/apply', { code: code }, function (response) {
            if (response.success) {
                updateTotalsFromResponse(response);
                showNotification(response.message || 'Coupon applied successfully.', 'success');
                $('.coupon-message').text(response.message || 'Coupon applied successfully.').css('color', 'green');
                $('.qtyminus, .qtyplus').attr('disabled', true);
                
            }
        }).fail(function (xhr) {
            let message = 'Invalid or expired coupon.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            $('.qtyminus, .qtyplus').attr('disabled', false);
            $('.discount-amount').text('0.00');
            $('.total-amount').text($('.subtotal-amount').text());
            showNotification(message, 'error');
            $('.coupon-message').text(message).css('color', 'red');
        });
    });

    // OPTIONAL: REMOVE COUPON LINK (if you add one later)
    $(document).on('click', '.remove-coupon-link', function (e) {
        e.preventDefault();

        $.post('/cart/coupon/remove', {}, function (response) {
            if (response.success) {
                updateTotalsFromResponse(response);
                $('.promo-code-input').val('');
                $('.coupon-message').text(response.message || 'Coupon removed.').css('color', 'green');
                showNotification(response.message || 'Coupon removed.', 'success');
            }
        });
    });

    // Helper to update totals area from any coupon/cart response
    function updateTotalsFromResponse(data) {
        if (typeof data.subtotal !== 'undefined') {
            $('.subtotal-amount').text(parseFloat(data.subtotal).toFixed(2));
        }
        if (typeof data.discount !== 'undefined') {
            $('.discount-amount').text(parseFloat(data.discount).toFixed(2));
        }
        if (typeof data.delivery !== 'undefined') {
            $('.delivery-amount').text(parseFloat(data.delivery).toFixed(2));
        }
        if (typeof data.total !== 'undefined') {
            $('.total-amount').text(parseFloat(data.total).toFixed(2));
        }
        if (typeof data.count !== 'undefined') {
            updateCartBadge(data.count);
        }
    }

    // REFRESH CART FUNCTION - Updated for partial approach
    function refreshCart(){
        location.reload();
    }

    // Show notification
    function showNotification(message, type) {
        // You can implement a toast notification here
        // For now, using alert (replace with your notification system)
        if(type === 'success') {
            // Success notification
            console.log('Success:', message);
        } else {
            // Error notification
            console.error('Error:', message);
        }
    }

    function updateCartCount(count) {
        const cartCount = $('#cart-count');

        if (count > 0) {
            cartCount.text(count).show();
        } else {
            cartCount.text(0).hide();
        }
    }

     // Add item to wishlist
    $(document).on('click', '.add-to-wishlist', function (e) {
        e.preventDefault();
        var $this = $('.add-to-wishlist');
        const data = {
            title: $(this).data('title'),
            image: $(this).data('image'),
            price: $(this).data('price'),
            short_description: $(this).data('short_description'),
            slug: $(this).data('slug'),
            status: $(this).data('status'),
            product_id: $(this).data('id'),
            url: $(this).data('url'),
            stock: $(this).data('stock'),
            size: $(this).data('size'),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: '/wishlist/add',
            type: 'POST',
            data: data,
            success: function (response) {
              let totalQuantity = 0;

              response.wishlist.forEach(item => {
                totalQuantity += parseInt(item.quantity);
                $(`.add-to-wishlist[data-id="${item.product_id}"]`).addClass('in-cart');
            });

              updateWishlistCount(totalQuantity);
            },
            error: function () {
                alert('Failed to add item.');
            }
        });
    });

    function updateWishlistCount(count) {
        const cartCount = $('#wishlist-count');
console.log(cartCount);
        if( cartCount ){
            if (count > 0) {
                cartCount.text(count).show();
            } else {
                cartCount.text(0).hide();
            }

        }
    }
});
