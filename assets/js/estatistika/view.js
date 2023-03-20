import '../../css/estatistika/view.css';

import '../common/table-list.js';

import '../common/datetime-picker-personalization';

function View() {
   var dom = {
      bilatzailea: $('#bilatzailea'),
      bilaketaTitulua: $('#bilaketa_titulua'),
      noiztikEremua: $('.js-datepicker-noiztik'),
      noraEremua: $('.js-datepicker-nora'),
      atzeraBotoia: $('.js-atzera-botoia'),
   };
   var erakutsi = true;

   function onTituluaClick() {
      dom.bilaketaTitulua.on('click', function () {
         if (!erakutsi) {
            dom.bilatzailea.show();
            erakutsi = true;
         } else {
            dom.bilatzailea.hide();
            erakutsi = false;
         }
         return false;
      });
   }

   function onNoiztikChanged() {
      dom.noiztikEremua.on('change', function () {
         if (dom.noiztikEremua.val() !== '') {
            dom.noiztikEremua.siblings().addClass("active");
         }
      });
   }

   function onNoraChanged() {
      dom.noraEremua.on('change', function () {
         if (dom.noraEremua.val() !== '') {
            dom.noraEremua.siblings().addClass("active");
         }
      });
   }

   function onAtzeraClick() {
      dom.atzeraBotoia.on('click', function (e) {
         var url = $(e.currentTarget).data('url');
         window.location.href = url;
      });
   }
   return {
      onTituluaClick: onTituluaClick,
      onNoiztikChanged: onNoiztikChanged,
      onNoraChanged: onNoraChanged,
      onAtzeraClick: onAtzeraClick
   };
}

$(function () {
   var locale = $('html').attr('lang');

   $('.js-datepicker').datetimepicker({
      locale: locale + '-' + locale,
      format: 'YYYY-MM-DD HH:mm',
   }).attr('type', 'text'); // Honekin chromen ez da testua agertzen
   var view = View();
   view.onTituluaClick();
   view.onAtzeraClick();

});