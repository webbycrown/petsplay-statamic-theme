$(document).ready(function () {

    // Add item to wishlist
    $(document).on('click', '.add-to-wishlist', function (e) {
        e.preventDefault();

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
                
            },
            error: function () {
                alert('Failed to add item.');
            }
        });
    });

});