import '../../scss/eskakizun_mota/edit.scss';

import $ from 'jquery';
const routes = require('../../../public/js/fos_js_routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import 'urijs/src/URI.min.js';

$(function () {
  Routing.setRoutingData(routes);
  var locale = $('html').attr("lang");
  $("#atzera").on('click', function () {
    window.location.href = Routing.generate('admin_eskakizun_mota_list', {
      '_locale': locale
    }) + '?' + uri.query();
  });
});