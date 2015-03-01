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

    var handler = function(event) {
        form = $(event.currentTarget).closest('form');
        el = $(form).find('input:checkbox:first');
        rules = $(el).data('nette-rules');
        value = $(event.currentTarget).val();

        $.each(rules, function(){
            if ($.inArray(value, this.arg) == -1) {
                Nette.validateControl(event.target);
                $(event.currentTarget).addClass('invalid');
                $(event.currentTarget).removeClass('valid');

            } else {
                $(event.currentTarget).addClass('valid');
                $(event.currentTarget).removeClass('invalid');
            }
        });
    };

    $('.quiz-form input[type="checkbox"]').on('change', handler);


    /**
     * @return {boolean}
     */
    Nette.validators.FormValidators_validateChecked = function (elem, arg, value) {

        var val = [];
        $(':checkbox:checked').each(function(i) {
            val[i] = $(this).val();
        });
        return $(arg).not(val).length === 0 && $(val).not(arg).length === 0;
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
                $(element).find('.message').html(message);
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
    
    $('.bubble').css("display","none");
        
    $('input[data-value]').click(function() {
        var $val = $(this).data("value");
        $('input[data-value]').parent('.quest').removeClass('active');
        $(this).parent('.quest').addClass('active');
        $("input[name=quizOne][value=" + $val + "]").prop('checked', true);
        
        $('.bubble').css("display","none");
    
        if ($val=='B') $('#bubble-yes').css("display","block");
        else $('#bubble-no').css("display","block");
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