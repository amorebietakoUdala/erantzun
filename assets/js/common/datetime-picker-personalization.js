import $ from 'jquery'

import 'eonasdan-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker';

$(function () {
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
   })
});