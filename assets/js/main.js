// =====================================
// SVU Events Guide - Main JavaScript
// =====================================

// =====================================
// Common Functionality (All Pages)
// =====================================
function initCommonFunctionality() {
    // Landing Page logic — show once per session
    if (!sessionStorage.getItem('landingShown')) {
        $('header, main, footer, .collapsible-sections, .cookie-banner').hide();
        $('#landingPage').show();

        setTimeout(function () {
            $('#landingPage').fadeOut(600, function () {
                $('header, main, footer, .collapsible-sections, .cookie-banner').fadeIn(600);
            });
            sessionStorage.setItem('landingShown', 'true');
        }, 4000); // 4-second landing display
    } else {
        $('#landingPage').remove();
    }

    // Mobile menu toggle
    $('#hamburger').click(function () {
        $('#navMenu').toggleClass('active');
    });

    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('#backToTop').addClass('show');
        } else {
            $('#backToTop').removeClass('show');
        }
    });

    $('#backToTop').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 800);
        return false;
    });

    /*
    // Prevent right-click with toast
    $(document).bind('contextmenu', function (e) {
        var lang = document.documentElement.getAttribute('lang') || 'ar';
        var toastMsg = (lang === 'en')
            ? '© 2026 SVU Events Guide. All Rights Reserved.'
            : 'جميع الحقوق محفوظة © 2026 دليل فعاليات الجامعة الافتراضية السورية';
        showToast(toastMsg);
        return false;
    });
    */
}

/*
// Show toast notification
function showToast(message) {
    // Detect page direction for positioning
    var isRTL = document.documentElement.getAttribute('dir') === 'rtl';
    var positionProp = isRTL ? 'right' : 'left';

    var toast = $('<div>', {
        class: 'toast-message',
        text: message,
        css: {
            position: 'fixed',
            top: '20px',
            backgroundColor: 'rgb(207,244,252)',
            color: 'rgb(5,81,96)',
            padding: '10px 20px',
            borderRadius: '4px',
            zIndex: '9999',
            boxShadow: '0 2px 10px rgba(0,0,0,0.1)',
            fontWeight: 'bold',
        }
    });

    // Set dynamic position based on language direction
    toast.css(positionProp, '20px');

    $('body').append(toast);

    setTimeout(function () {
        toast.fadeOut(function () {
            $(this).remove();
        });
    }, 3000);
}
*/

// =====================================
// Initialize on Document Ready
// =====================================
$(document).ready(function () {
    initCommonFunctionality();
    console.log("SVU Events Guide — Loaded Successfully");
});