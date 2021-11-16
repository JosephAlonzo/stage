/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// // any CSS you import will output into a single css file (app.css in this case)
// import './styles/app.css';

// // start the Stimulus application
// import './bootstrap';

const $ = require('jquery');
global.$ = global.jQuery = $;

import datepickerFactory from 'jquery-datepicker';
import datepickerJAFactory from 'jquery-datepicker/i18n/jquery.ui.datepicker-fr';

datepickerFactory($);
datepickerJAFactory($);

require('bootstrap');
require('moment');
require('popper.js');

require('@mdi/font/css/materialdesignicons.min.css');

require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

import 'datatables.net-bs4/css/dataTables.bootstrap4.min.css';
import 'datatables.net-bs4/js/dataTables.bootstrap4.min.js';

import jsZip from 'jszip';
import 'datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css';
import 'datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js';
import 'datatables.net-buttons/js/buttons.colVis.min.js';
import 'datatables.net-buttons/js/buttons.flash.min.js';
import 'datatables.net-buttons/js/buttons.html5.min.js';
import 'datatables.net-buttons/js/buttons.print.min.js';
import 'datatables.net-rowreorder-bs4/css/rowReorder.bootstrap4.min.css';
import 'datatables.net-rowreorder-bs4/js/rowReorder.bootstrap4.min.js';
import 'datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css';
import 'datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js';
import 'select2/dist/js/select2.min.js';
import 'select2/dist/css/select2.min.css';


require('datatable-sorting-datetime-moment');

window.JSZip = jsZip;

const imagesContext = require.context('./images', true, /\.(png|jpg|jpeg|gif|ico|svg)$/);
imagesContext.keys().forEach(imagesContext);

// any CSS you require will output into a single css file (app.css in this case)
require('./styles/app.css');

export default () => {
    const $select2 = $('.select2');
    const autocompleteUrl = $select2.data('autocomplete-url');
    $select2.select2({
        ajax: {
        url: autocompleteUrl,
        data: params => ({ s: params.term}),
        processResults: data => ({ results:  data.map(d => ({id: d.id, text: d.name}))})
        },
        theme: 'bootstrap4'
    });
}

$(document).ready(function() {
    $.fn.dataTable.moment( 'DD/MM/YYYY' );
    $.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss' );

    $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip();

    $('a[data-modal="modal"]').click(function(ev){
        ev.preventDefault();
        
        var target_modal = ev.currentTarget.dataset.targetModal;

        $(target_modal).modal('toggle');
        $(target_modal+' a[data-valid-button="submit"]').attr('href', ev.currentTarget.href);

        if (ev.currentTarget.dataset.msg !== undefined)
        {
            if (ev.currentTarget.dataset.msg != '')
            {
                $(target_modal+' div.modal-body').html(ev.currentTarget.dataset.msg);
            }
        }                    
    });

    $('.js-datepicker').datepicker({ changeMonth: true });
    $.datepicker.regional['fr'];
    
    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        var id = $(this).attr('id');
        $('label[for="'+id+'"]').html(fileName);
    });

    
});

