document.querySelectorAll(".toggle-password").forEach(function(toggle) {
    toggle.addEventListener("click", function () {

        const input = this.closest(".form-field").querySelector("input");
        if (!input) return;

        // Toggle password visibility
        if (input.type === "password") {
            input.type = "text";
        } else {
            input.type = "password";
        }

        // Toggle icon
        const icon = this.querySelector("svg, i");
        if (icon) {
            icon.classList.toggle("fa-eye");
            icon.classList.toggle("fa-eye-slash");
        }
    });
});


$(document).ready(function(){

   var swiper = new Swiper(".choose-section .mySwiper", {
      slidesPerView: 1,
      spaceBetween: 10,
      navigation: {
         nextEl: ".choose-section .swiper-button-next",
         prevEl: ".choose-section .swiper-button-prev",
      },
      breakpoints: {
         640: {
            slidesPerView: 2,
            spaceBetween: 20,
         },
         768: {
            slidesPerView: 3,
            spaceBetween: 30,
         },
         1024: {
            slidesPerView: 4,
            spaceBetween: 30,
         },
      },
   });

   var swiper = new Swiper(".product-slider-section .mySwiper", {
      slidesPerView: 1,
      spaceBetween: 10,
      navigation: {
         nextEl: ".product-slider-section .swiper-button-next",
         prevEl: ".product-slider-section .swiper-button-prev",
      },
      breakpoints: {
         640: {
            slidesPerView: 2,
            spaceBetween: 20,
         },
         768: {
            slidesPerView: 3,
            spaceBetween: 30,
         },
         1024: {
            slidesPerView: 4,
            spaceBetween: 30,
         },
      },
   });

   var swiper = new Swiper(".blog-slider-section .mySwiper", {
      slidesPerView: 1,
      spaceBetween: 10,
      navigation: {
         nextEl: ".blog-slider-section .swiper-button-next",
         prevEl: ".blog-slider-section .swiper-button-prev",
      },
      breakpoints: {
         640: {
            slidesPerView: 2,
            spaceBetween: 20,
         },
         768: {
            slidesPerView: 3,
            spaceBetween: 30,
         },
         1024: {
            slidesPerView: 4,
            spaceBetween: 30,
         },
      },
   });



   var swiper = new Swiper(".testimonials-section .mySwiper", {
      slidesPerView: 1,
      spaceBetween: 10,
      navigation: {
         nextEl: ".testimonials-section .swiper-button-next",
         prevEl: ".testimonials-section .swiper-button-prev",
      },
      breakpoints: {
         768: {
            slidesPerView: 2,
            spaceBetween: 20,
         },
         1024: {
            slidesPerView: 3,
            spaceBetween: 30,
         },
      },
   });


   jQuery('.wc-main-body').css( 'margin-bottom', jQuery('footer').outerHeight() + 'px' );


   $(window).scroll(function() {
      if ($(this).scrollTop() > 0){  
         $('header.sub-header').addClass("sticky");
      }
      else{
         $('header.sub-header').removeClass("sticky");
      }
   });



   var $loader = document.querySelector('.loader-section')

   if( $loader ){
      window.onload = function() {
         // window.setTimeout(function () {
            $loader.classList.remove('loader--active')
         // }, 2500);
      };

      window.onload ('load', function () {
         $loader.classList.add('loader--active')

         window.setTimeout(function () {
            $loader.classList.remove('loader--active')
         }, 2500);
      });

   }

   $('.mobile-menu-icon a.open-menu').click(function() {
      $('.side-bar-content').addClass('active');
   });
   $('.side-bar-close').click(function() {
      $('.side-bar-content').removeClass('active');
   });

   $('.counter').counterUp();

   $( ".click-down.one" ).click(function() {
      $('.click-down.one .dropdown-content').slideToggle("slow");
   });

   $( ".click-down.two" ).click(function() {
      $('.click-down.two .dropdown-content').slideToggle("slow");
   });

   $( ".click-down.three" ).click(function() {
      $('.click-down.three .dropdown-content').slideToggle("slow");
   });
   
   $('.grid-and-list .list').click(function() {
      $('.grid-and-list .list').addClass('active');
      $('.all-product-box').addClass('list');
      $('.grid-and-list .grid').removeClass('active');
   });
   $('.grid-and-list .grid').click(function() {
      $('.grid-and-list .grid').addClass('active');
      $('.grid-and-list .list').removeClass('active');
      $('.all-product-box').removeClass('list');
   });

   $(".ColorSwatch__ColorWrapper").click(function () {

      if ($(this).hasClass("active-color")) {
         $(".ColorSwatch__ColorWrapper").removeClass("active-color");
      }

      else {
         $(".ColorSwatch__ColorWrapper").removeClass("active-color");
         $(this).addClass("active-color");
      }
   });


   var swiper = new Swiper(".single-product-images .mySwiper", {
      loop: false,
      spaceBetween: 30,
      slidesPerView: 3,
      freeMode: false,
      watchSlidesProgress: false,
      breakpoints: {
         576: {
            slidesPerView: 5,
            spaceBetween: 30,
         },
      },
   });
   var swiper2 = new Swiper(".single-product-images .mySwiper2", {
      loop: false,
      freeMode: false,
      zoom: true,
      watchSlidesProgress: false,
      thumbs: {
         swiper: swiper,
      },
   });


   $(document).ready(function(){

      $('ul.tabs li').click(function(){
         var tab_id = $(this).attr('data-tab');

         $('ul.tabs li').removeClass('current');
         $('.tab-content').removeClass('current');

         $(this).addClass('current');
         $("#"+tab_id).addClass('current');
      })

   });

   $(".loader-btn-box .btn-button a").on("click",function(){
      $("span.loader-icon").addClass("active");
      setTimeout(function(){
         $("span.loader-icon").removeClass("active");
      },4000);
   });


   $(document).ready(function() {
      $('.popup-gallery').magnificPopup({
         delegate: 'a',
         type: 'image',
         tLoading: 'Loading image #%curr%...',
         mainClass: 'mfp-img-mobile',
         gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1]
         },
      });
   });


   $(document).ready(function() {

    //     $(document).on('click', '.js-sign-up', function (e) {
    //     e.preventDefault(); // 🚫 stop page redirect

    //     $('.sign-in-wrapper, .forgot-password-wrapper').removeClass('active');
    //     $('.sign-up-wrapper').addClass('active');

    //     $('#sectionTitle').text('Sign Up');
    // });

      $(document).on('click', '.js-sign-up', function (e) {

        if (window.location.pathname === '/sign-in') {
            e.preventDefault();

            if ($(this).hasClass('js-sign-up')) activate('signup');
        }
    });

    // On page load (after redirect)
    if (window.location.pathname === '/sign-in') {
        const tab = new URLSearchParams(window.location.search).get('tab') || 'signin';
        activate(tab);
    }

    function activate(tab) {
        $('.sign-in-wrapper, .sign-up-wrapper, .forgot-password-wrapper')
            .removeClass('active');

        if (tab === 'signup') {
            $('.sign-up-wrapper').addClass('active');
            $('#sectionTitle').text('Sign Up');
        }

        if (tab === 'signin') {
            $('.sign-in-wrapper').addClass('active');
            $('#sectionTitle').text('Sign In');
        }

        if (tab === 'forgot') {
            $('.forgot-password-wrapper').addClass('active');
            $('#sectionTitle').text('Forgot Password');
        }
    }

      $('.form-footer-text .sign-up a').click(function() {
         $('.sign-in-wrapper').removeClass('active');
         $('.forgot-password-wrapper').removeClass('active');
         $('.sign-up-wrapper').addClass('active');
         $('#sectionTitle').text('Sign Up');
      });
      $('.form-footer-text .sign-in a').click(function() {
         $('.sign-up-wrapper').removeClass('active');
         $('.forgot-password-wrapper').removeClass('active');
         $('.sign-in-wrapper').addClass('active');
          $('#sectionTitle').text('Sign In');
      });
      $('.sign-in-wrapper .forgot-password a').click(function() {
         $('.sign-up-wrapper').removeClass('active');
         $('.sign-in-wrapper').removeClass('active');
         $('.forgot-password-wrapper').addClass('active');
         $('#sectionTitle').text('Forgot Password');
      });

   });



   if( $(window).width() <= 767 ) {
      $( ".information-footer h4.footer-title" ).click(function() {
         $('.wc-footer-content .information-footer ul').slideToggle("slow");
      });
      $( ".helpful-footer h4.footer-title" ).click(function() {
         $('.wc-footer-content .helpful-footer ul').slideToggle("slow");
      });
   };

   AOS.init({
      once: true,
   });

   $(document).on("change", ".size-option", function () {
      const selectedSize = $(this).val();
      $(".add-to-cart, .add-to-wishlist").attr("data-size", selectedSize).removeData("size");
      
   });

   // Load More (Grid) – AJAX Pagination
   $(document).on('click', '.ajax-load-more-grid', function (e) {
      // Stop default link click
      e.preventDefault();

      var $button = $(this);
      var nextPageUrl = $button.data('url'); // URL of next page
      var currentPage = parseInt($('.currentPage').val());
      var lastPage = parseInt($('.lastPage').val());

      // Extract page number from URL
      var pageNum = 1;
      if (nextPageUrl) {
         var urlParams = new URLSearchParams(nextPageUrl.split('?')[1]);
         pageNum = parseInt(urlParams.get('page')) || 1;
      }

      // Force HTTPS on production
      const appEnv = document.querySelector('meta[name="app-env"]')?.getAttribute('content');
      if (appEnv === 'production') {
         if (nextPageUrl && nextPageUrl.startsWith("http:")) {
            nextPageUrl = nextPageUrl.replace(/^http:/, "https:");
         }
      }

      // Show loading icon
      $button.find('img').removeClass('hidden');
      $button.find('img').removeClass('d-none');

      // If last page reached → remove button
      if (!nextPageUrl || currentPage >= lastPage) {
         $button.find('img').addClass('hidden');
         $button.find('img').addClass('d-none');
         $button.remove();
         return;
      }

      // Disable button while loading
      $button.prop('disabled', true).addClass('loading');
      $.ajax({
         url: nextPageUrl,
         type: 'GET',
         dataType: 'html',
         headers: { 'X-Requested-With': 'XMLHttpRequest' },
         success: function (response) {
            var $response = $('<div>').html(response);

            // Get new items from loaded page
            var $newItems = $response.find('.grid-item-append');

            // Load More button wrapper from response
            var $newLoadMoreWrap = $response.find('.load-more-wrap');

            // Hide loader icon
            $button.find('img').addClass('hidden');
            $button.find('img').addClass('d-none');

            // Append new items to current grid
            if ($newItems.length) {
               $('.grid_append').append($newItems);

            }

            // Replace Load More button or remove it
            if ($newLoadMoreWrap.length) {
               $('.load-more-wrap').html($newLoadMoreWrap.html());
            } else {
               $('.load-more-wrap').remove();
            }

            // If last page, remove Load More entirely
            if (pageNum == lastPage) {
               $('.load-more-wrap').remove(); 
            } 
            AOS.init({
               once: true
            });

         },
         error: function (xhr, status, error) {
            $button.find('img').addClass('hidden');
            $button.find('img').addClass('d-none');
            console.error('Load more error:', error);
         },
         complete: function () {
            // Re-enable button after request
            $button.prop('disabled', false).removeClass('loading');
         }
      });
   });

   $(document).on('click', '#searchBtn', function () {
      let area = $('#area_name').val();
      let team = $('#team_select').val();
      let service = $('#service_select').val();

      let url = '?area=' + encodeURIComponent(area) + '&team=' + team + '&service=' + service;

      window.location.href = url;
   });

   // Handle AJAX contact form submission
   $(document).on('submit','#contactForm', function(e) {
      e.preventDefault(); 

      const $form = $(this);

      var $message = $form.find('.form-message');

      // Clear previous errors
      $message.html('');
      $form.find('.field-error').html('');
      $form.find('.form-control').removeClass('error');

      // FormData allows file uploads + normal fields
      const formData = new FormData(this);

      $.ajax({
         url: $form.attr('action'),   // Statamic auto-generates action URL
         method: 'POST',
         data: formData,
         processData: false,  // Required for FormData
         contentType: false,  // Required for FormData
         success: function(response) {

            // Success message
            if(response.success) {
               $message.text("Thank you! We'll be in touch shortly.").css('color', '#0e5e6f').fadeIn();
               $form[0].reset(); // Reset form fields
            } 
         },
         error: function(response) {

            // Show validation errors
            if (response.responseJSON.error) {
               $.each(response.responseJSON.error, function(field, message) {
                  const $input = $form.find('[name="' + field + '"]');
                  const $errorContainer = $form.find('[data-error-for="' + field + '"]');

                  $input.addClass('error');

                  // If array: join with <br>
                  $errorContainer.html(Array.isArray(message) ? message.join('<br>') : message);
               });
            }

         }
      });
   });

   const NEWSLETTER_COOKIE = 'NewsletterPopup'; // single cookie
   const overlay = $('<div id="overlay"></div>');

        // =========================
        // COOKIE HELPERS
        // =========================
   function setCookie(name, value, days) {
      const d = new Date();
      d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
      document.cookie = `${name}=${encodeURIComponent(value)}; expires=${d.toUTCString()}; path=/`;
   }

   function getCookie(name) {
      const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
      return match ? decodeURIComponent(match[1]) : null;
   }

   function daysSinceCookie(cookieName) {
      const value = getCookie(cookieName);
      if (!value) return Infinity;

      const setDate = new Date(value);
      if (isNaN(setDate.getTime())) return Infinity;

      return Math.floor((new Date() - setDate) / 86400000);
   }

   // =========================
   // POPUP SHOW LOGIC
   // subscribed → 365 days hide
   // closed → 7 days hide
   // =========================
   function shouldShowNewsletter() {
      const val = getCookie(NEWSLETTER_COOKIE);

      if (!val) return true; // no cookie = show

      // user subscribed → hide for 365 days
      if (val.includes('subscribed')) {
         return daysSinceCookie(NEWSLETTER_COOKIE) >= 365;
      }

      // user closed popup (not subscribed)
      return daysSinceCookie(NEWSLETTER_COOKIE) >= 7;
   }

   // Optional LocalStorage override (your logic kept)
   if (localStorage.getItem('NewsletterPopup')) {
      $('.automatically_open_popup_window').hide();
      overlay.remove();
      return;
   }

   // =========================
   // SHOW POPUP (after 2s)
   // =========================
   setTimeout(function () {
      if (shouldShowNewsletter()) {
         $('body.home').append(overlay);
         overlay.show();
         $('.automatically_open_popup_window').fadeIn();
      }
   }, 100);

   // =========================
   // CLOSE POPUP
   // =========================
   $('.close, .x').on('click', function () {

      const val = getCookie(NEWSLETTER_COOKIE);

      // set close-cookie only if user did NOT subscribe
      if (!val || !val.includes('subscribed')) {
         setCookie(NEWSLETTER_COOKIE, new Date().toISOString(), 7);
      }

      $('.automatically_open_popup_window').fadeOut();
      overlay.remove();
      return false;
   });

   // AJAX Newsletter Subscription
   $(document).on('submit','#newsletterForm', function(e) {
      e.preventDefault(); 

      const $form = $(this);

      // Reset old messages & errors
      var $message = $form.find('.form-message');
      $message.html('');
      $form.find('.field-error').html('');
      $form.find('.form-control').removeClass('error');
      const email = $form.find('#email').val();

      const formData = new FormData(this);

      $.ajax({
         url:newsLetterUrl,   // API endpoint
         method: 'get',
         data: { email:email },
         dataType: 'json',
         success: function(response) {

            if (response.status === true) {
               // Success message
               $message.text(response.message).css('color', '#0e5e6f').fadeIn();

               // Store cookie for "Don't show popup again"
               setCookie(NEWSLETTER_COOKIE, new Date().toISOString(), 365);

               $form[0].reset();

               // Close popup automatically
               setTimeout(() => {
                  $('.automatically_open_popup_window').fadeOut();
                  overlay.remove();
               }, 1500);

               return;

            }else {
               // Error message from server
               $message.text(response.message).css('color', '#df5243').fadeIn();           
            } 
         },
         error: function(response) {
            // You can add validation error handling here if needed               
         }
      });
   });


   // Handle pagination links
   $(document).on('click', '.pagination a', function(e) {
      e.preventDefault();

      // let url = $(this).attr('href'); 
      let url = $(this).data('url'); 
      if (!url) return;
      const appEnv = document.querySelector('meta[name="app-env"]')?.getAttribute('content');

      // Convert to https in production
      if (appEnv === 'production') {          
         if (url && url.startsWith("http:")) {
            url = url.replace(/^http:/, "https:"); 
         }
      }
      // Load grid items without reloading page
      $('.grid_append').load(url + ' .grid_append > *');

      // Reload pagination componen
      $('.pagination-part').load(url + ' .pagination-part > *');
      $('.pagination-part').load(url + ' .pagination-part > *', function() {

         // Read updated active page numbe
         let newPage = $('.pagination-part .active a').data('current_page');
         
         // Display new page number wherever needed
         $('.current_page_number').text(newPage);
      });

   });

   AOS.init({
      once: true
   });

   var productHtml = $('.all-product');

   // Fetch & filter products
   function filterProducts(page = 1) {

      // Collect all active category filters
      let selectedCategories = [];

      // Get all active categories
      $('.category-link.active').each(function() {
         selectedCategories.push($(this).data('category'));
      });

      // Search input
      let searchTerm = $('#search-input').val();
      
      // Collect selected colors
      let selectedColors = [];
      $('input[name="color[]"]:checked').each(function() {
         selectedColors.push($(this).data('color'));
      });

      // Send AJAX request to backend for filtering
      $.ajax({
         url: productFilterUrl,
         type: 'GET',
         data: {
            category: selectedCategories,
            colors: selectedColors,
            search: searchTerm,
            page: page
         },
         success: function(html) {
            // Render product list
            productHtml.html(html).show();

            // Update pagination text
            paginateCurrent();

            // Handle pagination click
            $(document).off('click', '.page-link').on('click', '.page-link', function(e) {
               e.preventDefault();
               let page = $(this).data('page');
               if (page) filterProducts(page);
            });

            // Reset and refresh AOS animations
            productHtml.find('[data-aos]').each(function() {
               $(this).removeClass('aos-animate'); // reset animations
            });
            AOS.refreshHard(); // refresh animations
         },
         error: function(xhr, status, error) {
            console.error(error);
         }
      });
   }

   // Run initial load
   filterProducts(1);
   
   // Update pagination display
   function paginateCurrent(){
      let current = parseInt($('#paginate-current').text());
      let total = parseInt($('#paginate-total').text());
      let perPage = parseInt($('#paginate-per-page').text());

      updatePaginationText(current, perPage, total);
   }

   // Show "Showing X–Y of Z results"
   function updatePaginationText(current, perPage, total) {

      current = Number(current);
      perPage = Number(perPage);
      total   = Number(total);


      if (total === 0 || !isFinite(current) || !isFinite(perPage)) {
         $('.show-product-content p').text("Showing 0–0 of 0 results");
         return;
      }

      let from = ((current - 1) * perPage) + 1;
      let to = current * perPage;
      if (to > total) to = total;
      $('.show-product-content p').text(`Showing ${from}–${to} of ${total} results`);
   }

   // Accordion menu toggle
   $(document).on('click', '.accordion-item > .accordion-tabs > .category-links > span', function(e) {
      e.preventDefault();

      let $parentItem = $(this).closest('.accordion-item');

      // Toggle open/close accordion
      $parentItem.toggleClass('active');

      // Toggle inner content
      $parentItem.find('.accordion-content').slideToggle(200);

      // Optional active effect on link
      $(this).toggleClass('active');
   });

   // Category selection
   $(document).on('click', '.category-link', function(e) {
      e.preventDefault();
      $(this).toggleClass('active');

      updateSelectedTags();
      filterProducts(1);
   });

   // Checkbox (colors) change
   $(document).on('change', '.form-check-input', function() {
      updateSelectedTags();
      filterProducts(1);
   });

   // Search button click
   $(document).on('click','#search-btn' ,function(e) {
      e.preventDefault();
      filterProducts(1);
   });

   // Search via Enter key
   $(document).on('keypress', '#search-input', function(e) {
      if (e.which == 13) { // enter key
         e.preventDefault();
         filterProducts(1);
      }
   });

   // Pagination click
   $(document).on('click', '.pagination .page-link', function(e) {
      e.preventDefault();
      let page = $(this).data('current_page'); 
      if (page) {
         filterProducts(page);
      }
   });

   // Clear All filters
   $(document).on('click','#clear-all',function(){
      $('.form-check-input').prop("checked", false);
      $('.category-link.active').removeClass('active');
      $(this).closest('div.d-flex.align-items-center').remove();
      $('#search-input').val('');
      updateSelectedTags();
      filterProducts(1);
   }); 

   // Remove individual selected tag
   $(document).on("click", ".remove-tag", function () {
      let value = $(this).data("value");

      // Uncheck matching checkbox
      $('.form-check-input[data-color="'+value+'"]').prop("checked", false);
      $('.category-link.active[data-category="'+value+'"]').removeClass('active');
      $(this).closest('div.d-flex.align-items-center').remove();

      updateSelectedTags();
      filterProducts(1);
   });

   // Clear All (duplicate handler)
   $(document).on("click", "#clear-all", function (e) {
      e.preventDefault();

      $(".form-check-input").prop("checked", false);

      updateSelectedTags();
      filterProducts(1);
   });

   // Build selected filter tag list
   function updateSelectedTags() {
      let tagsContainer = $('.selected-tags');
      tagsContainer.html(""); // clear old tags

      let selected = [];

      // Add checked colors
      $('.form-check-input:checked').each(function () {
         let label = $(this).data("label");
         let value = $(this).data("color"); 

         selected.push({ label, value });
      });

      // Add active categories
     

     $('.category-link.active').each(function () {
          let label = $(this).find('.category-span').text().trim(); // ✅ TEXT
          let value = $(this).data('category');                     // ✅ SLUG

          selected.push({ label, value });
       });

      if (selected.length === 0) return;

      // Build HTML
      let html = "";

      selected.forEach(tag => {
         html += `
               <div class="d-flex align-items-center bg-primary bg-opacity-25 border border-primary border-opacity-25 p-1 rounded">
               <p class="mb-0 px-2 text-dark">${tag.label}</p>

               <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" class="px-2 cursor-pointer remove-tag" data-value="${tag.value}" fill="none" viewBox="0 0 9 9">
               <path d="M8.80065 0.206172C8.7375 0.142889 8.66249 0.0926821 8.5799 0.0584261C8.49732 0.02417 8.40879 0.00653721 8.31939 0.00653721C8.22999 0.00653721 8.14146 0.02417 8.05888 0.0584261C7.97629 0.0926821 7.90128 0.142889 7.83813 0.206172L4.5 3.53747L1.16187 0.199346C1.09867 0.136145 1.02364 0.086012 0.941068 0.0518081C0.858492 0.0176043 0.769989 0 0.68061 0C0.591231 0 0.502727 0.0176043 0.420151 0.0518081C0.337576 0.086012 0.262546 0.136145 0.199346 0.199346C0.136145 0.262546 0.086012 0.337576 0.0518081 0.420151C0.0176043 0.502727 0 0.591231 0 0.68061C0 0.769989 0.0176043 0.858492 0.0518081 0.941068C0.086012 1.02364 0.136145 1.09867 0.199346 1.16187L3.53747 4.5L0.199346 7.83813C0.136145 7.90133 0.086012 7.97636 0.0518081 8.05893C0.0176043 8.14151 0 8.23001 0 8.31939C0 8.40877 0.0176043 8.49727 0.0518081 8.57985C0.086012 8.66242 0.136145 8.73745 0.199346 8.80065C0.262546 8.86385 0.337576 8.91399 0.420151 8.94819C0.502727 8.9824 0.591231 9 0.68061 9C0.769989 9 0.858492 8.9824 0.941068 8.94819C1.02364 8.91399 1.09867 8.86385 1.16187 8.80065L4.5 5.46253L7.83813 8.80065C7.90133 8.86385 7.97636 8.91399 8.05893 8.94819C8.14151 8.9824 8.23001 9 8.31939 9C8.40877 9 8.49727 8.9824 8.57985 8.94819C8.66242 8.91399 8.73745 8.86385 8.80065 8.80065C8.86385 8.73745 8.91399 8.66242 8.94819 8.57985C8.9824 8.49727 9 8.40877 9 8.31939C9 8.23001 8.9824 8.14151 8.94819 8.05893C8.91399 7.97636 8.86385 7.90133 8.80065 7.83813L5.46253 4.5L8.80065 1.16187C9.06006 0.902469 9.06006 0.465577 8.80065 0.206172Z" fill="#0156D5"></path>
               </svg>
               </div>
         `;
      });

         // Add Clear All
      html += `<a id="clear-all" href="#" class="d-inline-flex align-items-center text-decoration-none border-bottom gap-1 small">Clear All</a>`;

      tagsContainer.removeClass('d-none').html(html);
   }

   // -------------------- SIGNUP --------------------

   $("#signup-form").on("submit", function (e) {
      e.preventDefault();

      let form = $(this);

      // Reset errors
      form.find(".error").removeClass("error");
      form.find("[data-error-for]").html("");

      $.ajax({
         url: registrationUrl,
         method: "POST",
         data: form.serialize(),
         success: function (res) {

            // Success message
            if (res.status == true) {
               $("#signup-message").html( `<div class="alert alert-success">${res.message}</div>`);
               form.trigger("reset");
               // Auto-open login tab
               $('.form-footer-text .sign-in a').click();
               return;
            } else {

               // Validation errors
               if( res.type == 'validation' ){

                  if (res.errors) {
                     $.each(res.errors, function (field, messages) {
                        const input = form.find(`[name="${field}"]`);
                        const errorBox = form.find(`[data-error-for="${field}"]`);
                        input.addClass("error");
                        errorBox.html(messages.join("<br>"));
                     });
                  }

               }else{
                  // General error
                  $("#signup-message").html( `<div class="alert alert-danger">${res.message}</div>` );
               }
            }
         },
         error: function (xhr) {
            $("#signup-message").html( `<div class="alert alert-danger">Something went wrong.</div>` );
         }
      });
   });

   // -------------------- LOGIN --------------------
   $("#login-form").on("submit", function (e) {
      e.preventDefault();

      let form = $(this);
      $("#login-message").html("");

      // Reset errors
      form.find(".error").removeClass("error");
      form.find("[data-error-for]").html("");

      $.ajax({
         url: loginUrl,   
         method: "POST",
         data: form.serialize(),
         success: function (res) {

            // Login success
            if (res.status === true) {
               $("#login-message").html( `<div class="alert alert-success">${res.message}</div>` );
            
               if (res.redirect) {
                  window.location.href = res.redirect;
               }
               return;
            }

            // Validation errors
            if (res.type === "validation") {
               $("#login-message").html("");
               $.each(res.errors, function (field, messages) {

                  const $input = form.find(`[name="${field}"]`);
                  const $errorBox = form.find(`[data-error-for="${field}"]`);

                  $input.addClass("error");

                  const msg = Array.isArray(messages) ? messages.join("<br>") : messages;

                  $errorBox.html(msg);
               });
               return;
            }

            // General error
            $("#login-message").html( `<div class="alert alert-danger">${res.message}</div>`);
         },
         error: function () {
            $("#login-message").html( `<div class="alert alert-danger">Something went wrong.</div>` );
         }
      });
   });

// -------------------- LOGIN popup --------------------
   $("#login-popup-form").on("submit", function (e) {
      e.preventDefault();

      let form = $(this);
      $("#login-message").html("");

      // Reset errors
      form.find(".error").removeClass("error");
      form.find("[data-error-for]").html("");

      $.ajax({
         url: loginUrl,   
         method: "POST",
         data: form.serialize(),
         success: function (res) {

            // Login success
            if (res.status === true) {
               $("#login-message").html( `<div class="alert alert-success">${res.message}</div>` );
            
               if (res.redirect) {
                  window.location.href = '/checkout';
               }
               return;
            }

            // Validation errors
            if (res.type === "validation") {
               $("#login-message").html("");
               $.each(res.errors, function (field, messages) {

                  const $input = form.find(`[name="${field}"]`);
                  const $errorBox = form.find(`[data-error-for="${field}"]`);

                  $input.addClass("error");

                  const msg = Array.isArray(messages) ? messages.join("<br>") : messages;

                  $errorBox.html(msg);
               });
               return;
            }

            // General error
            $("#login-message").html( `<div class="alert alert-danger">${res.message}</div>`);
         },
         error: function () {
            $("#login-message").html( `<div class="alert alert-danger">Something went wrong.</div>` );
         }
      });
   });

   $(document).on('submit','#forgot-password-form',function(e){
      e.preventDefault();
      let form = $(this);
      $("#forgot-password-message").html("");

      // Reset errors
      form.find(".error").removeClass("error");
      form.find("[data-error-for]").html("");

      $.ajax({
         url: sendResetLinkUrl,   
         method: "POST",
         data: form.serialize(),
         success: function (res) {

            // Login success
            if (res.status === true) {
               $("#forgot-password-message").html( `<div class="alert alert-success">${res.message}</div>` );
               return;
            }

            // Validation errors
            if (res.type === "validation") {
               $("#forgot-password-message").html("");
               $.each(res.errors, function (field, messages) {

                  const $input = form.find(`[name="${field}"]`);
                  const $errorBox = form.find(`[data-error-for="${field}"]`);

                  $input.addClass("error");

                  const msg = Array.isArray(messages) ? messages.join("<br>") : messages;

                  $errorBox.html(msg);
               });
               return;
            }

            // General error
            $("#forgot-password-message").html( `<div class="alert alert-danger">${res.message}</div>`);
         },
         error: function () {
            $("#forgot-password-message").html( `<div class="alert alert-danger">Something went wrong.</div>` );
         }
      });
   });


   $(document).on('submit','.reset-password-form',function(e) {
       e.preventDefault();
      let form = $(this);
      $("#reset-password-message").html("");

      // Reset errors
      form.find(".error").removeClass("error");
      form.find("[data-error-for]").html("");

      $.ajax({
         url: resetPasswordUrl,   
         method: "POST",
         data: form.serialize(),
         success: function (res) {

            // Login success
            if (res.status === true) {
               $("#reset-password-message").html( `<div class="alert alert-success">${res.message}</div>` );
                window.location.href = '/sign-in';
               return;
            }

            // Validation errors
            if (res.type === "validation") {
               $("#reset-password-message").html("");
               $.each(res.errors, function (field, messages) {

                  const $input = form.find(`[name="${field}"]`);
                  const $errorBox = form.find(`[data-error-for="${field}"]`);

                  $input.addClass("error");

                  const msg = Array.isArray(messages) ? messages.join("<br>") : messages;

                  $errorBox.html(msg);
               });
               return;
            }

            // General error
            $("#reset-password-message").html( `<div class="alert alert-danger">${res.message}</div>`);
         },
         error: function () {
            $("#reset-password-message").html( `<div class="alert alert-danger">Something went wrong.</div>` );
         }
      });
   });

   
   $.ajaxSetup({
      headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
   });

   $('#commentForm').on('submit', function(e) {
      e.preventDefault();

      let form = $(this);
      let formData = form.serialize();

      // reset errors
      $('.error-message').addClass('hidden');
      $('.error-text').text('');
      $('.form-message').addClass('hidden').text('');

      $.ajax({
         url: productCommentAddUrl,
         type: 'POST',
         data: formData,
         success: function(response) {
            $('.form-message')
            .removeClass('hidden')
            .removeClass('text-danger')
            .addClass('text-success')
            .text(response.message);

            $('.error-message').addClass('hidden');
            $('.error-text').text('');

            form.trigger('reset');
            location.reload();
         },
         error: function(xhr) {
            if (xhr.status === 422) {
               let errors = xhr.responseJSON.errors;

               $.each(errors, function(field, messages) {
                  let errorBox = $('[data-error-for="' + field + '"]');
                  errorBox.removeClass('hidden');
                  errorBox.find('.error-text').text(messages[0]);
               });
            }
         }
      });
   });
});

$(document).ready(function(){

    $.ajaxSetup({
     headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
 });

   $(document).on('click', '.package_btn', function (e) {
     e.preventDefault();

     const packageId = $(this).data('package-id');
     const redirectUrl = $(this).attr('href');

    // Call Statamic route to store session
    $.post('/set-package-session', {
        package_id: packageId
    }).done(function () {
        window.location.href = redirectUrl;
    });
  }); 

   /* ===============================
       CATEGORY ACCORDION READ MORE
    =============================== */
    const visibleCategoryCount = 5;
    const $categoryItems = $('#categoryAccordion .accordion-item');
    const $categoryBtn = $('#toggleAccordion');

    if ($categoryItems.length > visibleCategoryCount) {
        $categoryItems.slice(visibleCategoryCount).hide();
        $categoryBtn.show();
    } else {
        $categoryBtn.hide();
    }

    $categoryBtn.on('click', function () {
        const expanded = $(this).hasClass('expanded');

        if (expanded) {
            // Read Less
            $categoryItems.slice(visibleCategoryCount).slideUp();
            $(this).removeClass('expanded').text('Read More');
        } else {
            // Read More
            $categoryItems.slideDown();
            $(this).addClass('expanded').text('Read Less');
        }
    });


    /* ===============================
       COLOR FILTER READ MORE
    =============================== */
    const visibleColorCount = 5;
    const $colorItems = $('#colorList li');
    const $colorBtn = $('#toggleColors');

    if ($colorItems.length > visibleColorCount) {
        $colorItems.slice(visibleColorCount).hide();
        $colorBtn.show();
    } else {
        $colorBtn.hide();
    }

    $colorBtn.on('click', function () {
        const expanded = $(this).hasClass('expanded');

        if (expanded) {
            // Read Less
            $colorItems.slice(visibleColorCount).slideUp();
            $(this).removeClass('expanded').text('Read More');
        } else {
            // Read More
            $colorItems.slideDown();
            $(this).addClass('expanded').text('Read Less');
        }
    });
});