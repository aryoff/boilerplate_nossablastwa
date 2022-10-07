{{-- @extends('dynamicticket::layouts.master') --}}
@extends('layouts.app')

@section('module_css')
    <link rel="stylesheet" href="{{ mix('css/nossablastwa.css') }}">
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <h4 class="header-title">Nossa Blast WA Logs</h4>
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="table_report" class="table-striped no-margin" style="width:100%" aria-hidden="true">
                <thead>
                </thead>
                <tbody>
                </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('module_js')
    <script src="{{ mix('js/nossablastwa.js') }}"></script>
    <script>
        callbackGenerateTable();

        function callbackGenerateTable(callbackColumns, serverSide) {
            let columnData = [{
                    "data": "campaign",
                    "title": "Campaign",
                    "render": function (data, type, row, meta) {
                        let rowData = JSON.parse(row['data']);
                        if (typeof rowData['campaign'] != 'undefined') {
                            return rowData['campaign'];
                        } else {
                            return '';
                        }
                    }
                },{
                    "data": "incident",
                    "title": "Incident",
                    "render": function (data, type, row, meta) {
                        let rowData = JSON.parse(row['data']);
                        return rowData.incident;
                    }
                },
                {
                    "data": "tgl_kirim",
                    "title": "Tanggal Kirim",
                },
                {
                    "data": "penerima",
                    "title": "Penerima",
                    "render": function (data, type, row, meta) {
                        let rowData = JSON.parse(row['data']);
                        if (typeof rowData['penerima'] != 'undefined') {
                            return rowData['penerima'];
                        } else {
                            return rowData['phone_number'];
                        }
                    }
                },
                {
                    "data": "keluhan",
                    "title": "Keluhan",
                    "render": function (data, type, row, meta) {
                        let rowData = JSON.parse(row['data']);
                        if (typeof rowData['keluhan'] != 'undefined') {
                            return rowData['keluhan'];
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "data": "lapul",
                    "title": "Lapul",
                    "render": function (data, type, row, meta) {
                        let rowData = JSON.parse(row['data']);
                        if (typeof rowData['lapul'] != 'undefined') {
                            return rowData['lapul'];
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "data": "tk_urgensi",
                    "title": "Urgensi",
                    "render": function (data, type, row, meta) {
                        let rowData = JSON.parse(row['data']);
                        if (typeof rowData['tk_urgensi'] != 'undefined') {
                            return rowData['tk_urgensi'];
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "data": "reportdate",
                    "title": "Tanggal Open",
                    "render": function (data, type, row, meta) {
                        let rowData = JSON.parse(row['data']);
                        if (typeof rowData['reportdate'] != 'undefined') {
                            return rowData['reportdate'];
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "data": "tk_region",
                    "title": "Regional",
                    "render": function (data, type, row, meta) {
                        let rowData = JSON.parse(row['data']);
                        if (typeof rowData['tk_region'] != 'undefined') {
                            return rowData['tk_region'];
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "data": "tk_subregion",
                    "title": "Witel",
                    "render": function (data, type, row, meta) {
                        let rowData = JSON.parse(row['data']);
                        if (typeof rowData['tk_subregion'] != 'undefined') {
                            return rowData['tk_subregion'];
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "data": "level",
                    "title": "Level",
                    "render": function (data, type, row, meta) {
                        let rowData = JSON.parse(row['data']);
                        if (typeof rowData['level'] != 'undefined') {
                            return rowData['level'];
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "data": "blast_status",
                    "title": "Status",
                },
            ];

            let tfoot = createNewElement(document.getElementById("table_report"), {
                kind: 'tfoot'
            });
            let footer = createNewElement(tfoot, {
                kind: 'tr'
            });
            columnData.forEach(element => {
                createNewElement(footer, {
                    kind: 'th'
                });
            });

            reportTable = $('#table_report').on('draw.dt', function () {
                $('[data-toggle="tooltip"]').tooltip();
            }).DataTable({
                "fixedHeader": {
                    header: true,
                    footer: true
                },
                "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
                "order": [[2, 'desc']],
                "initComplete": function (settings, json) {
                    $("#table_report").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
                    this.api().columns(':not(.no_filter)').every(function () { //tambahkan filter per column yg searchable
                        let column = this;
                        let title = $(column.header()).html();
                        let searchText = $('<div class="form-group form-inline"><input type="text" class="form-control flex-fill" placeholder="Search ' + title + '"/>' + column.search() + '</div>')
                            .appendTo($(column.footer()).empty())
                            .on('keyup change', function (e) {
                                if (column.search() !== this.getElementsByTagName('input')[0].value) {
                                    if (!reportTable.init().serverSide) {
                                        column.search(this.getElementsByTagName('input')[0].value).draw();
                                    } else {
                                        column.search(this.getElementsByTagName('input')[0].value);
                                    }
                                } else {
                                    if (e.keyCode == 13) {
                                        column.draw();
                                    }
                                }
                            });
                    });
                    $('#table_report_length').parent().removeClass('col-md-6').addClass('col-md-4');
                    reportTable.buttons().container().addClass('col-sm-12 col-md-4').insertBefore( $('#table_report_filter').parent() );
                    $('#table_report_filter').parent().removeClass('col-md-6').addClass('col-md-4');
                },
                "lengthMenu": [
                    [10, 25, 50, 100, 1000, -1],
                    [10, 25, 50, 100, 1000, "All"]
                ],
                "pageLength": 10,
                "scrollCollapse": true,
                "paging": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ url('/nossablastwa/ViewLogs') }}",
                    "type": "POST",
                },
                "columns": columnData,
            });
        }
    </script>
@endsection
