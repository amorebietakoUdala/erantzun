import '../../css/common/table-list.css';

import $ from 'jquery';

import 'bootstrap-table/dist/bootstrap-table';
import 'tableexport.jquery.plugin/tableExport.min';
import 'bootstrap-table/dist/extensions/export/bootstrap-table-export';
import 'bootstrap-table/dist/locale/bootstrap-table-es-ES';
import 'bootstrap-table/dist/locale/bootstrap-table-eu-EU';
import Swal from 'sweetalert2';

function getUrlVars() {
    var vars = [],
        hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function buildURL($table, url) {
    if ((typeof $table.bootstrapTable) != 'undefined') {
        var options = $table.bootstrapTable('getOptions');
        var pageSize = parseInt($('span.page-size:first-child').text());
        var pageNumber = options.pageNumber;
        let params = new URLSearchParams(url.search.slice(1));
        params.append("returnPage", pageNumber);
        params.append("pageSize", pageSize);
        url = url.toString() + '?' + params.toString();
        return url;
    }
}

$(function() {
    var locale = $('html').attr("lang");
    var pageSize = getUrlVars()["pageSize"];
    if (typeof pageSize == 'undefined' || pageSize == '') {
        pageSize = 10;
    }
    var returnPage = getUrlVars()["returnPage"];
    if (typeof returnPage == 'undefined' || returnPage == '') {
        returnPage = 0;
    }
    $('.taula').bootstrapTable({
        cache: false,
        showExport: true,
        showColumns: true,
        exportTypes: ['excel'],
        exportDataType: 'all',
        exportOptions: {
            ignoreColumn: ['aukerak']
        },
        pagination: true,
        search: true,
        striped: true,
        sortStable: true,
        pageSize: pageSize,
        pageList: [10, 25, 50, 100],
        sortable: true,
        locale: locale + '_' + locale.toUpperCase(),
    });
    var $table = $('.taula');
    var options = $table.bootstrapTable('getOptions');
    if (returnPage == 0 && options.totalPages > 0) {
        returnPage = 1;
    }
    if (returnPage > options.totalPages) {
        returnPage = options.totalPages;
    }
    $table.bootstrapTable('selectPage', returnPage);

    $(function() {
        $('#toolbar').find('select').change(function() {
            $table.bootstrapTable('destroy').bootstrapTable({
                exportDataType: $(this).val(),
            });
        });
    });
    pageSize = parseInt($('span.page-size:first-child').text());
    $(document).find('span.page-list a.dropdown-item').on('click', function(e) {
        e.preventDefault();
        var url = new URL(document.location.href);
        let params = new URLSearchParams(url.search.slice(1));
        params.set('pageSize', $(e.target.firstChild).text());
        url = url.origin + url.pathname + '?' + params.toString();
        document.location.href = url;
    });

    /* Bootstrap table buttons */
    $(document).on('click', '.js-erakutsi_botoia, .js-editatu_botoia', function(e) {
        e.preventDefault();
        var url = new URL($(e.currentTarget).data('url'));
        window.location.href = buildURL($table, url);
    });

    $(document).on("click", ".js-ezabatu_botoia", function(e) {
        e.preventDefault();
        var url = $(e.currentTarget).data('url');
        var locale = $('html').attr('lang');
        if ((typeof $table.bootstrapTable) != 'undefined') {
            var url = new URL($(e.currentTarget).data('url'));
            url = buildURL($table, url);
        }
        Swal({
            title: locale === 'eu' ? 'Ezabatu?' : 'Borrar?',
            text: locale === 'eu' ? 'Konfirmatu mesedez' : 'Confirme por favor',
            confirmButtonText: locale === 'eu' ? 'Bai' : 'Sí',
            cancelButtonText: locale === 'eu' ? 'Ez' : 'No',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            preConfirm: () => Promise.resolve([url]).then(url => document.location.href = url)
        }).catch(function(arg) {

        });
    });

    $(document).on('click', '.js-itxi_botoia', function(e) {
        e.preventDefault();
        var url = new URL($(e.currentTarget).data('url'));
        Swal({
            title: locale === 'eu' ? 'Itxi?' : 'Cerrar?',
            text: locale === 'eu' ? 'Konfirmatu mesedez' : 'Confirme por favor',
            confirmButtonText: locale === 'eu' ? 'Bai' : 'Sí',
            cancelButtonText: locale === 'eu' ? 'Ez' : 'No',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            preConfirm: () => Promise.resolve(buildURL($table, url)).then((url) => {
                window.location.href = url;
            })
        }).catch(function(arg) {
            console.log('Cancelado!');
        });
    });

    $(document).on('click', '.js-erreklamatu_botoia', function(e) {
        console.log('Erreklamatu botoia clicked!!!');
        e.preventDefault();
        var url = new URL($(e.currentTarget).data('url'));
        $table = $('.taula');
        Swal({
            title: locale === 'eu' ? 'Erreklamatu?' : 'Reclamar?',
            text: locale === 'eu' ? 'Konfirmatu mesedez' : 'Confirme por favor',
            confirmButtonText: locale === 'eu' ? 'Bai' : 'Sí',
            cancelButtonText: locale === 'eu' ? 'Ez' : 'No',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            preConfirm: () => Promise.resolve(buildURL($table, url)).then((url) => {
                window.location.href = url;
            })
        }).catch(function(arg) {});
    });

    $(document).on('click', '.js-berria_botoia', function(e) {
        e.preventDefault();
        var url = new URL($(e.currentTarget).data('url'));
        window.location.href = buildURL($table, url);
    });

    var $remove = $('#batchclose');
    $remove.on('click', function() {
        var ids = $.map($table.bootstrapTable('getSelections'), function(row) {
            return row.id;
        });
        $.ajax({
            url: "../../api/batchclose",
            dataType: "json",
            method: "POST",
            data: {
                ids: JSON.stringify(ids),
            },
            success: function(data) {
                $table.bootstrapTable('remove', {
                    field: 'id',
                    values: ids
                });
            }
        });
    });
    /* End Bootstrap table buttons */
});