import '../../scss/jatorria/edit.scss';

import $ from 'jquery';
const routes = require('../../../public/js/fos_js_routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import 'urijs/src/URI.min.js';

$(function() {
    Routing.setRoutingData(routes);
    var locale = $('html').attr("lang");
    var uri = new URI(document.location.href);
    $("#atzera").on('click', function() {
        window.location.href = global.app_base + Routing.generate('admin_jatorria_list', { '_locale': locale }) + '?' + uri.query();
    });
});