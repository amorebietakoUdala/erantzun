import '../../scss/enpresa/edit.scss';

import $ from 'jquery';
const routes = require('../../../public/js/fos_js_routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
//import 'urijs/src/URI.min.js';

$(function() {
    Routing.setRoutingData(routes);
    var locale = $('html').attr("lang");

    var url = new URL(document.location.href);
    let params = new URLSearchParams(url.search.slice(1));
    $("#atzera").on('click', function() {
        window.location.href = global.app_base + Routing.generate('admin_enpresa_list', { '_locale': locale }) + '?' + params.toString();
    });
});