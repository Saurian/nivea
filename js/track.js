/**
 * This file is part of the vanocni_soutez
 * Copyright (c) 2014
 *
 * @file    track.js
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

(function ($) {

    $('.track').click(function () {
        var value = $(this).attr('name') + '-' + $(this).attr('value');
        if (value !== undefined) {
            Omniture.trackNOPV(value);
        }
    });

    $('.track-btn').click(function () {
        var value = $(this).data('name');
        if (value !== undefined) {
            Omniture.trackNOPV(value);
        }
    });

    $('form').find('.next').click(function() {
        var form = $(this).closest('form').attr('name');
        var name = $(this).attr('name');
        if (form !== undefined && name !== undefined ) {
            Omniture.trackNOPV(form + '-' + name);
        }
    });

    $('.track-external').click(function () {
        if ((value = $(this).data('url')) !== undefined) {
            Omniture.trackExternalLink(value);
        }
    });

})(jQuery);
