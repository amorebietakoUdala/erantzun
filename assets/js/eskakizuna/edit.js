import '../../scss/eskakizuna/edit.scss';

const routes = require('../../../public/js/fos_js_routes.json');
import $ from 'jquery';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import 'urijs/src/URI.min.js';
import Swal from 'sweetalert2';
import 'jquery-ui-bundle/jquery-ui';
import '../common/datetime-picker-personalization';
import 'tinymce/tinymce';
import 'tinymce/themes/modern';


function getUrlParameter(url, name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(url);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};

function init_map(latitudea, longitudea) {
    var var_location = new google.maps.LatLng(latitudea, longitudea);

    var var_mapoptions = {
        center: var_location,
        zoom: 16,
        scrollwheel: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
    };

    var var_marker = new google.maps.Marker({
        position: var_location,
        map: var_map,
        draggable: true,
        animation: google.maps.Animation.DROP,
    });

    var var_map = new google.maps.Map(document.getElementById("map-container"),
        var_mapoptions);

    var_marker.setMap(var_map);

    google.maps.event.addListener(var_marker, 'drag', function (event) {
        document.getElementById('eskakizuna_form_georeferentziazioa_latitudea').value = event.latLng.lat();
        document.getElementById('eskakizuna_form_georeferentziazioa_longitudea').value = event.latLng.lng();
    });

    google.maps.event.addListener(var_marker, 'dragend', function (event) {
        document.getElementById('eskakizuna_form_georeferentziazioa_latitudea').value = event.latLng.lat();
        document.getElementById('eskakizuna_form_georeferentziazioa_longitudea').value = event.latLng.lng();
    });
}

window.init_map = init_map;

function parse_results(results) {
    $("#eskakizuna_form_georeferentziazioa_longitudea").val(results[0].geometry.location.lng);
    $("#eskakizuna_form_georeferentziazioa_latitudea").val(results[0].geometry.location.lat);
    $("#eskakizuna_form_georeferentziazioa_google_address").val(results[0].formatted_address);
}

function localizar(elemento, direccion) {
    var geocoder = new google.maps.Geocoder();

    var map = new google.maps.Map(document.getElementById(elemento), {
        zoom: 16,
        scrollwheel: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        draggable: true
    });

    geocoder.geocode({
        'address': direccion
    }, function (results, status) {
        if (status === 'OK') {
            var resultados = results[0].geometry.location,
                resultados_lat = resultados.lat(),
                resultados_long = resultados.lng();

            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location,
                title: direccion,
                animation: google.maps.Animation.DROP,
                draggable: true
            });
            google.maps.event.addListener(marker, 'drag', function (event) {
                document.getElementById('eskakizuna_form_georeferentziazioa_latitudea').value = event.latLng.lat();
                document.getElementById('eskakizuna_form_georeferentziazioa_longitudea').value = event.latLng.lng();
            });

            google.maps.event.addListener(marker, 'dragend', function (event) {
                document.getElementById('eskakizuna_form_georeferentziazioa_latitudea').value = event.latLng.lat();
                document.getElementById('eskakizuna_form_georeferentziazioa_longitudea').value = event.latLng.lng();
            });

            parse_results(results);
        } else {
            var mensajeError = "";
            if (status === "ZERO_RESULTS") {
                mensajeError = "No hubo resultados para la dirección ingresada.";
            } else if (status === "OVER_QUERY_LIMIT" || status === "REQUEST_DENIED" || status === "UNKNOWN_ERROR") {
                mensajeError = "Error general del mapa.";
            } else if (status === "INVALID_REQUEST") {
                mensajeError = "Error de la web. Contacte con Name Agency.";
            }
            alert(mensajeError);
        }
    });
}

function swal_alert(locale, title_es, text_es, title_eu, text_eu) {
    Swal({
        title: locale === 'eu' ? title_eu : title_es,
        text: locale === 'eu' ? text_eu : text_es,
        confirmButtonText: locale === 'eu' ? 'Onartu' : 'Aceptar',
        showLoaderOnConfirm: false,
    });
}

function checkSize(input, max_size) {
    if (input.files && input.files[0]) {
        var filesize = input.files[0].size;
        if (filesize <= max_size) {
            return true;
        } else {
            return false;
        }
    } else
        return true;
}

function addEventToCheckSize(locale, selector, max_size) {
    var files = $(selector);
    $(files).off('change');
    var i;
    for (i = 0; i < files.length; i++) {
        var file = $(selector)[i];
        $(file).on('change', function () {
            var ok = checkSize(file, max_size);
            if (!ok)
                swal_alert(locale,
                    "El fichero es demasiado grande", "Elija uno más pequeño. Menor de " + max_size / 1024 / 1024 + "Mb",
                    "Fitxategia handiegia da.", "Hautatu beste bat mesedez. " + max_size / 1024 / 1024 + "Mb baino txikiago.");
        });
    }
}

$(function () {
    var latitudea = 43.2206664;
    var longitudea = -2.733066600000029;
    var locale = $('html').attr('lang');
    var role = $('html').attr('role');
    Routing.setRoutingData(routes);

    if ($("#eskakizuna_form_georeferentziazioa_longitudea").val() !== '' && $("#eskakizuna_form_georeferentziazioa_latitudea").val() !== '') {
        latitudea = $("#eskakizuna_form_georeferentziazioa_latitudea").val();
        longitudea = $("#eskakizuna_form_georeferentziazioa_longitudea").val();
    }

    if (typeof $("#eskakizuna_form_eskatzailea_izena").val() !== "undefined") {
        /* INICIO Autocompletes */
        $("#eskakizuna_form_eskatzailea_izena").autocomplete({
                minLength: 3,
                source: function (request, response) {
                    $.ajax({
                        url: Routing.generate('api_eskatzailea_list'),
                        dataType: "json",
                        data: {
                            izena: request.term
                        },
                        success: function (data) {
                            response(data.eskatzaileak);
                        }
                    });
                },
                focus: function (event, ui) {
                    $("#eskakizuna_form_eskatzailea_izena").val(ui.item.izena);
                    return false;
                },
                select: function (event, ui) {
                    $("#eskakizuna_form_eskatzailea_id").val(ui.item.id);
                    $("#eskakizuna_form_eskatzailea_nan").val(ui.item.nan);
                    $("#eskakizuna_form_eskatzailea_izena").val(ui.item.izena);
                    $("#eskakizuna_form_eskatzailea_emaila").val(ui.item.emaila);
                    $("#eskakizuna_form_eskatzailea_telefonoa").val(ui.item.telefonoa);
                    $("#eskakizuna_form_eskatzailea_faxa").val(ui.item.faxa);
                    $("#eskakizuna_form_eskatzailea_helbidea").val(ui.item.helbidea);
                    $("#eskakizuna_form_eskatzailea_postaKodea").val(ui.item.posta_kodea);
                    $("#eskakizuna_form_eskatzailea_herria").val(ui.item.herria);
                    $("i + label + input").each(function (x) {
                        if ($(this).val() !== "") {
                            $(this).addClass("active");
                            $(this).siblings().addClass("active");
                        }
                    });
                    return false;
                }
            })
            .autocomplete("instance")._renderItem = function (ul, item) {
                return $("<li>")
                    .append("<div>" + item.izena + "</div>")
                    .appendTo(ul);
            };
    }
    if (typeof $("#eskakizuna_form_eskatzailea_nan").val() !== "undefined") {
        $("#eskakizuna_form_eskatzailea_nan").autocomplete({
                minLength: 3,
                source: function (request, response) {
                    $.ajax({
                        url: "../../api/eskatzailea",
                        dataType: "json",
                        data: {
                            nan: request.term
                        },
                        success: function (data) {
                            console.log(data);
                            response(data.eskatzaileak);
                        }
                    });
                },
                focus: function (event, ui) {
                    $("#eskakizuna_form_eskatzailea_nan").val(ui.item.nan);
                    return false;
                },
                select: function (event, ui) {
                    $("#eskakizuna_form_eskatzailea_id").val(ui.item.id);
                    $("#eskakizuna_form_eskatzailea_nan").val(ui.item.nan);
                    $("#eskakizuna_form_eskatzailea_izena").val(ui.item.izena);
                    $("#eskakizuna_form_eskatzailea_emaila").val(ui.item.emaila);
                    $("#eskakizuna_form_eskatzailea_telefonoa").val(ui.item.telefonoa);
                    $("#eskakizuna_form_eskatzailea_faxa").val(ui.item.faxa);
                    $("#eskakizuna_form_eskatzailea_helbidea").val(ui.item.helbidea);
                    $("#eskakizuna_form_eskatzailea_postaKodea").val(ui.item.postaKodea);
                    $("#eskakizuna_form_eskatzailea_herria").val(ui.item.herria);
                    $("i + label + input").each(function (x) {
                        if ($(this).val() !== "") {
                            $(this).addClass("active");
                            $(this).siblings().addClass("active");
                        }
                    });
                    return false;
                }
            })
            .autocomplete("instance")._renderItem = function (ul, item) {
                return $("<li>")
                    .append("<div>" + item.nan + "</div>")
                    .appendTo(ul);
            };
    }
    /* FIN Autocompletes */

    tinymce.init({
        selector: 'textarea',
        menubar: false,
        resize: false,
        statusbar: false,
        theme: 'modern',
        init_instance_callback: function (editor) {
            if (editor.id === 'eskakizuna_form_mamia') {
                if (role === "KANPOKO_TEKNIKARIA") {
                    editor.setMode('readonly');
                }
            }
        }
    });

    /* INICIO Eventos */
    $("label[for], input[type='text']").on('click', function (e) {
        $(this).addClass("active");
        $(this).siblings("input[type='text']").addClass("active").focus();
    });

    $("select").on('click', function (e) {
        $(this).addClass("active");
        $(this).siblings().addClass("active").focus();
    });

    $.extend(true, $.fn.datetimepicker.defaults, {
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-arrow-up',
            down: 'fa fa-arrow-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-calendar-check-o',
            clear: 'fa fa-trash',
            close: 'fa fa-times'
        }
    });

    $('.js-datepicker').datetimepicker({
        locale: locale + '-' + locale,
        format: 'YYYY-MM-DD HH:mm',
    }).attr('type', 'text'); // Honekin chromen ez da testua agertzen
    if (document.location.href.includes('/new')) {
        // If it's new eskakizuna we initialize date to current date
        $('.js-datepicker').data("DateTimePicker").date(new Date());
    }

    if ($('.js-datepicker').val() !== '') {
        $('.js-datepicker').siblings().addClass("active");
    }

    $(".js-btn-erantzun").on('click', function () {
        $(".js-erantzuna").show();
    });

    $("#buscar").on('click', function () {
        var direccion = $("#eskakizuna_form_kalea").val();
        if (direccion !== "") {
            localizar("map-container", direccion + ", Amorebieta-Etxano");
        }
    });

    google.maps.event.addDomListener(window, 'load', init_map(latitudea, longitudea));

    var max_size = 4 * 1024 * 1024;

    $(".js-gorde_botoia, .js-erantzun_botoia").on('click', function (e) {
        // Eranskinak eta argazkien tamainia konprobatu
        var files = $('.js-eranskinaFile,.js-file');
        var all_ok = true;
        var ok = true;
        var i;
        for (i = 0; i < files.length; i++) {
            var file = files[i];
            ok = checkSize(file, max_size);
            if (!ok) {
                all_ok = false;
            }
        }

        if (!all_ok) {
            swal_alert(locale,
                "El fichero es demasiado grande", "Elija uno más pequeño. Menor de " + max_size / 1024 / 1024 + "Mb",
                "Fitxategia handiegia da.", "Hautatu beste bat mesedez. " + max_size / 1024 / 1024 + "Mb baino txikiago.");
        } else {
            $('#eskakizunaForm').submit();
        }
    });

    /* INICIO Eranskinak */
    $('#add-another-eranskina').on('click', function (e) {
        e.preventDefault();

        var eranskinakList = $('#js-eranskinak-list');
        var eranskinakCount = $('#js-eranskinak-list li').length;

        var newWidget = eranskinakList.attr('data-prototype');

        newWidget = newWidget.replace(/__name__/g, eranskinakCount);
        eranskinakCount++;

        // create a new list element and add it to the list
        var newLi = $('<li></li>').html(newWidget);
        newLi.appendTo(eranskinakList);
        $(newLi).show();
        $(newLi).find('.js-file').css('opacity', '1');
        $(newLi).find('.js-file').show();
        addEventToCheckSize(locale, $(newWidget).find('.js-file').attr('id'), max_size);
    });

    $('.js-eranskina-ezabatu').on('click', function (e) {
        $(e.currentTarget).parents('li').remove();
    });

    /* FIN Eranskinak */

    /* INICIO Argazkiak */
    $('#add-another-argazkia').on('click', function (e) {
        e.preventDefault();
        var argazkiakList = $('#js-argazkiak-list');
        var argazkiakCount = $('#js-argazkiak-list li').length;

        var newWidget = argazkiakList.attr('data-prototype');

        newWidget = newWidget.replace(/__name__/g, argazkiakCount);
        argazkiakCount++;

        // create a new list element and add it to the list
        var newLi = $('<li></li>').html(newWidget);
        newLi.appendTo(argazkiakList);
        $(newLi).show();
        $(newLi).find('.js-file').css('opacity', '1');
        $(newLi).find('.js-file').show();
        addEventToCheckSize(locale, '.js-file', max_size);
    });

    var uri = new URI(document.location.href);
    $(document).on('click', '.js-atzera-botoia', function (e) {
        e.preventDefault();
        var url = Routing.generate('admin_eskakizuna_list', {
            '_locale': locale
        }) + '?' + uri.query();
        window.location.href = url;
    });

});