/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './scss/app.scss';

import $ from 'jquery';

import popper from 'popper.js';

import 'bootstrap/dist/js/bootstrap';

import 'mdbootstrap/js/mdb';
global.app_base = '/erantzun';

$(function() {
    var locale = $('html').attr('lang');
    if ($("select").val() !== "") {
        $("select").addClass("active");
        $("select").siblings().addClass("active").focus();
    }

    $("select").on('click', function(e) {
        $(this).addClass("active");
        $(this).siblings().addClass("active").focus();
    });

    $("js-lang-es").on('click', function(e) {
        $(this).addClass("active");
        $(this).siblings().addClass("active").focus();
    });

    $(".js-datepicker").siblings().on('click', function(e) {
        $(e.currentTarget).siblings().addClass("active");
        $(e.currentTarget).addClass("active");
        $(e.currentTarget).siblings('input').datetimepicker("show");
    });

    $(".js-datepicker").siblings().on('focus', function(e) {
        $(e.currentTarget).siblings().addClass("active");
        $(e.currentTarget).addClass("active");
        $(e.currentTarget).siblings('input').datetimepicker("show");
    });

    $(".js-datepicker").siblings().on('focusout', function(e) {
        if ($(e.currentTarget).siblings('input').val() === '') {
            $(e.currentTarget).siblings().removeClass("active");
            $(e.currentTarget).removeClass("active");
        }
    });

    $("label[for], input[type='text']").on('click', function(e) {
        $(this).addClass("active");
        $(this).siblings("input[type='text']").addClass("active").focus();
    });
});