$(document).ready(function() {
    $('#citiesList tfoot th').each( function (k, v) {
        var title = $(this).text();
        if (title != "Actions")
        {
            $(this).html( '<input class="form-control input-sm" type="text" placeholder="'+title+'" data-column="'+k+'"/>' );
        }
        else
        {
            $(this).html('');
        }
    } );

    //export excel de toutes les lignes
    var oldExportAction = function (self, e, dt, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
            }
            else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
        }
    };

    var newExportAction = function (e, dt, button, config) {
        var self = this;
        var oldStart = dt.settings()[0]._iDisplayStart;
        dt.one('preXhr', function (e, s, data) {
            //Just this once, load all data from the server...
            data.start = 0;
            data.length = 2147483647;
            dt.one('preDraw', function (e, settings) {
                //Call the original action function 
                oldExportAction(self, e, dt, button, config);
                dt.one('preXhr', function (e, s, data) {
                    //DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    //Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });
            //Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
            setTimeout(dt.ajax.reload, 0);
            //Prevent rendering of the full data to the DOM
            return false;
            });
        });
        //Requery the server with the new one-time export settings
        dt.ajax.reload();
    };

    var table = $('#citiesList').DataTable({
        dom: 'Bfrtip',
        columnDefs: [
            { "name": "name",   "targets": 0 },
            { "name": "postalCode",  "targets": 1 },
            { "name": "actions",  "targets": 2, "class": "text-right", "search": false },
        ],
        processing: true,
        serverSide: true,
        ajax: {
            "url": $('#citiesList').data('pathroute'),
            "type": "POST"
        },
        stateSave: true,
        initComplete: function () 
        {
            var api = this.api();

            var state = api.state.loaded();
            api.buttons().container().appendTo('#citiesList .col-md-6:eq(0)');

            // Apply the search
            api.columns().every( function () {
                var that = this;
                
                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );

            if (state !== null)
            {
                state.columns.forEach(function (v, k) {
                    $('input[data-column='+k+'], select[data-column='+k+']').val(v.search.search);
                });
            }

            $('a[data-modal="modal"]').click(function(ev){
                ev.preventDefault();
                
                var target_modal = ev.currentTarget.dataset.targetModal;

                $(target_modal).modal('toggle');
                $(target_modal+' a[data-valid-button="submit"]').attr('href', ev.currentTarget.href);
                
            });
        },
        order: [[ 0, "asc"]],
        pagingType: "full_numbers",
        lengthMenu: [ [10, 15, 20, -1], [10, 15, 20 ,  "Tous"] ], 
        lengthChange: false,
        language: {
            "url": $('#citiesList').data('pathlanguage')
        },
        buttons: [
            { 
                extend: 'pageLength',
                className: "btn btn-light",
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel text-success"></i>',
                className: "btn btn-light btn-outline-success",
                action: newExportAction
            }
        ],
        createdRow: function( row, data, dataIndex ) {
            $(row).find("td").css("white-space", "normal");
        }
    });
});