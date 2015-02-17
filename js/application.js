/**
 * application.js
 */

(function ($) {

    var messageWait = 2000;
    var element;
    var int;

    function displayError() {
        $(element).animate({
            opacity: 0
        }, messageWait, function () {
            $(this).css({
                opacity: "",
                display: "none"
            });
        });
    }

    $.fn.message = function() {
        var _this = this;
        $(this).css("display", 'block');
        setTimeout((function() {
            $(_this).animate({
                opacity: 0
            }, messageWait, function() {
                $(_this).css({
                    opacity: "",
                    display: "none"
                });
            });
        }), messageWait);
    };

    Nette.addError = function(elem, message) {
        if (elem.focus) {
            elem.focus();
            if ($(elem).hasClass("select2-offscreen")) {
                $(elem).select2('open');
            }
        }
        if (message) {
            element = $('.error-messages');
            if ($(element).length) {
                $(element).find('.message').text(message);
                $(element).css("display", 'block');
                clearInterval(int);
                int = setTimeout((function() {
                    displayError(element);
                }), messageWait);
            }
        }
    };

    $('input[name="lostPassword"]').click(function() {
        $('.popup.logged').removeClass('active');
        $('.popup.lost').addClass('active');
    });

    $('input[data-value]').click(function() {
        var $val = $(this).data("value");
        $('input[data-value]').removeClass('active');
        $(this).addClass('active');
        $("input[name=quizOne][value=" + $val + "]").prop('checked', true);
    });

    $('.flash').message();
    $('select').select2({
    });

    $('.login').click(function(e) {
        $('.popup.logged').addClass('active');
        e.stopPropagation();
    });
    $('.popup .close').click(function() {
        $('.popup').removeClass('active');
    });

    $('body').click(function(e) {
        if ($(e.target).hasClass('popup') && $(e.target).hasClass('logged') && $(e.target).hasClass('active')) {
            $('.popup.logged').removeClass('active');
            e.preventDefault();
        }
    });

})(jQuery);