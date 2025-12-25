$(document).ready(function () {
    $(document).on('change','.payment_method',function(){
        var pay_type = $(this).val();
        $('#payment_type').val(pay_type);
    });
    
    $('#placeOrderBtn').on('click', function () {

        const form = $('.shopping-address');

        form.find(".error").removeClass("error");
        form.find("[data-error-for]").html("");
        $("#success-message").html("");

         // Payment method check
        const payment_type = (form.find('#payment_type').val() || '').trim();
        if (!payment_type) {
            $("#success-message").html(`<div class="alert alert-danger">Please select a payment method.</div>`);
            return;
        }

        // Products JSON
        let products = $('.products').val();
        try {
            products = JSON.parse(products);
        } catch (e) {
            return $("#success-message").html(` <div class="alert alert-danger">Invalid product data.</div>`);
        }


        // Prepare payload
        var data = {
            name: (form.find('#name').val() || '').trim(),
            address: (form.find('#address').val() || '').trim(),
            city: (form.find('#city').val() || '').trim(),
            state: (form.find('#state').val() || '').trim(),
            zip: (form.find('#zip').val() || '').trim(),
            notes: (form.find('#notes').val() || '').trim(),
            author_id: form.find('#author_id').val() || '',
            amount: $('.amount').val() || '',
            discount: form.find('#discount').val() || '',
            s_charge: $('.s_charge').val(),
            products: products,
            payment_method: payment_type
        };
  
        $.ajax({
            url: '/checkout/place-order',
            type: 'POST',
            data: data,
            success: function (res) {
                // Stripe redirect
                if (res.checkout_url) {
                    window.location.href = res.checkout_url;
                    return;
                }

                if( payment_type == 'razorpay' ){
                    const options = {
                        key: res.key_id,
                    amount: $('.amount').val() * 100, // in paise
                    currency: 'INR',
                    name: 'Your Store',
                    order_id: res.order_id, // Order created from backend
                    handler: function (response) {
                        // Send the payment info to your backend to verify
                        $.ajax({
                            url: '/order/razorpay/verify',
                            type: 'POST',
                            data: {
                                payment_id: response.razorpay_payment_id,
                                order_id: response.razorpay_order_id,
                                signature: response.razorpay_signature,
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    alert('Payment Verified Successfully!');
                                    window.location.href = '/';
                                } else {
                                    alert('Payment verification failed: ' + res.message);
                                }
                            },
                            error: function() {
                                alert('Something went wrong while verifying payment.');
                            }
                        });
                    },
                    prefill: {
                        name: 'customerName',
                        email: 'customerEmail',
                        contact: 'customerPhone'
                    },
                    theme: {
                        color: "#3399cc"
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();
                }
                // SUCCESS
                if (res.status === true) {
                    $("#success-message").html( `<div class="alert alert-success">${res.message}</div>` );
                    setTimeout(function () {
                        window.location.href = res.redirect; 
                    }, 3000); 
                    return;
                }

                // VALIDATION FAIL
                if (res.status === false && res.type === "validation") {
                    $.each(res.errors, function (field, messages) {

                        const $input = form.find(`[name="${field}"]`);
                        const errorBox = form.find(`[data-error-for="${field}"]`);
                        $input.addClass("error");
                        const msg = Array.isArray(messages) ? messages.join("<br>") : messages;
                        errorBox.html(msg);
                    });
                    return;
                }

                // NOT LOGGED IN
                if (res.status === false && res.type === "unauthorized") {
                    $("#success-message").html(`<div class="alert alert-danger">${res.message}</div> `);

                    setTimeout(function () {
                        window.location.href = res.redirect; 
                    }, 3000); 
                    return;
                }
            },

            error: function () {
                $("#success-message").html(` <div class="alert alert-danger">Something went wrong.</div> `);
            }
        });

    });


});