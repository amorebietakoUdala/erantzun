import '../../scss/eskakizuna/list.scss';

import '../common/table-list.js';
import '../common/datetime-picker-personalization';

$(function () {
    var locale = $('html').attr('lang');

    if ($('.js-datepicker-noiztik').val() !== '') {
        $('.js-datepicker-noiztik').siblings().addClass("active");
    }

    if ($('.js-datepicker-nora').val() !== '') {
        $('.js-datepicker-nora').siblings().addClass("active");
    }

    $('.js-datepicker').datetimepicker({
        locale: locale + '-' + locale,
        format: 'YYYY-MM-DD HH:mm',
    }).attr('type', 'text'); // Honekin chromen ez da testua agertzen

    $('#bilatzailea').show();
    var erakutsi = true;
    $('#bilaketa_titulua').on('click', function () {
        if (!erakutsi) {
            $('#bilatzailea').show();
            erakutsi = true;
        } else {
            $('#bilatzailea').hide();
            erakutsi = false;
        }
    });

    $('#js-btn-bilatu').on('click', function (e) {
        e.preventDefault();
        var action = location.href;
        action = new URI(location.href);
        action.setQuery("returnPage", 1);
        action.setQuery("pageSize", $('li[role="menuitem"].active a').text());
        $('form[name="eskakizuna_bilatzailea_form"]').attr('action', action.toString());
        $('form[name="eskakizuna_bilatzailea_form"]').submit();
    });

    $('#js-btn-garbitu').on('click', function (e) {
        e.preventDefault();
        $('form[name="eskakizuna_bilatzailea_form"] input').not('input[type="hidden"]').val('');
        $('form[name="eskakizuna_bilatzailea_form"] input').not('input[type="hidden"]').siblings().removeClass('active');
        $('form[name="eskakizuna_bilatzailea_form"] select').val('');
        $('form[name="eskakizuna_bilatzailea_form"] select').siblings().removeClass('active');
    });

});