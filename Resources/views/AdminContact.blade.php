@extends('layouts.app')

@section('module_css')
    <link rel="stylesheet" href="{{ mix('css/nossablastwa.css') }}">
@endsection

@section('content')
    <div class="card mt-2">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-primary" onclick="modalAddContactShow(true)">Add New Contact</button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0">Data Table</h4>
                            <div class="table-responsive dash-social">
                                <table id="datatable" class="table table-bordered">
                                    <thead class="thead-light" style="text-align: center;">
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editCampaign" tabindex="-1" aria-labelledby="editCampaignLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="card">
                    <div class="card-body">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCampaignLabel">Edit Campaign</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="editCampaignBody">
                            <form id="editCampaignForm" action="{{ url('/saveCampaign') }}" method="POST">
                                <div class="form-group row">
                                    <label for="nama" class="col-sm-4 col-form-label">Nama</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="nama" name="nama" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="jabatan" class="col-sm-4 col-form-label">Jabatan</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="jabatan" name="jabatan" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="contact_number" class="col-sm-4 col-form-label">Contact Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="contact_number" name="contact_number" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="selectWitel" class="col-sm-4 col-form-label">Witel</label>
                                    <div class="col-sm-8">
                                        <select id="selectWitel" name="listWitel[]" class="form-control" multiple="multiple" style="width:100%">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="selectRegional" class="col-sm-4 col-form-label">Regional</label>
                                    <div class="col-sm-8">
                                        <select id="selectRegional" name="listRegional[]" class="form-control" style="width:100%" multiple="multiple">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="selectCampaign" class="col-sm-4 col-form-label">Campaign</label>
                                    <div class="col-sm-8">
                                        <select id="selectCampaign" name="campaign" class="form-control" style="width:100%">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="selectLevel" class="col-sm-4 col-form-label">Level</label>
                                    <div class="col-sm-8">
                                        <select id="selectLevel" name="level" class="form-control" style="width:100%">
                                            <option value="1">Level 1</option>
                                            <option value="2">Level 2</option>
                                            <option value="3">Level 3</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" id="userId" name="id"/>
                                <div class="row" style="float: right;">
                                    <input type="submit" value="Submit" class="btn btn-success">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addContact" tabindex="-1" aria-labelledby="addContactLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="card">
                    <div class="card-body">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addContactLabel">Add New Contact</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="addContactBody">
                            <form id="addContactForm" action="{{ url('/addContact') }}" method="POST">
                                <div class="form-group row">
                                    <label for="nama" class="col-sm-4 col-form-label">Nama</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="nama" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="jabatan" class="col-sm-4 col-form-label">Jabatan</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="jabatan" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="contact_number" class="col-sm-4 col-form-label">Contact Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="contact_number" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="selectTag" class="col-sm-4 col-form-label">Tag</label>
                                    <div class="col-sm-8">
                                        <select id="selectTag" name="tag[]" class="form-control" multiple="multiple" style="width:100%">
                                            <option value="telkom">Telkom</option>
                                            <option value="telkomakses">Telkom Akses</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" style="float: right;">
                                    <input type="submit" value="Submit" class="btn btn-success">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('module_js')
    <script src="{{ mix('js/nossablastwa.js') }}"></script>
    <script>
        






        // var topAgentStatusTable;
        // const waitlistPerCampaignChart = new Chart(document.getElementById('waitlistPerCampaignChart'), {
        //     type: 'horizontalBar',
        //     data: {
        //         labels: [],
        //         datasets: [{
        //             label: 'Waitlist Campaign',
        //             data: [],
        //             backgroundColor: [
        //                 'rgba(255, 99, 132, 0.2)',
        //                 'rgba(54, 162, 235, 0.2)',
        //                 'rgba(255, 206, 86, 0.2)',
        //                 'rgba(75, 192, 192, 0.2)',
        //                 'rgba(153, 102, 255, 0.2)',
        //                 'rgba(255, 159, 64, 0.2)'
        //             ],
        //             borderColor: [
        //                 'rgba(255, 99, 132, 1)',
        //                 'rgba(54, 162, 235, 1)',
        //                 'rgba(255, 206, 86, 1)',
        //                 'rgba(75, 192, 192, 1)',
        //                 'rgba(153, 102, 255, 1)',
        //                 'rgba(255, 159, 64, 1)'
        //             ],
        //             borderWidth: 1
        //         }]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: true,
        //         scales: {
        //             xAxes: [{
        //                 ticks: {
        //                     beginAtZero: true
        //                 }
        //             }]
        //         },
        //         events: false,
        //         animation: {
        //             duration: 500,
        //             easing: "easeOutQuart",
        //             onComplete: function () {
        //                 barLabel(this);
        //             }
        //         }
        //     }
        // });
        // const topOccupancyChart = new Chart(document.getElementById('topOccupancyChart'), {
        //     type: 'horizontalBar',
        //     data: {
        //         labels: [],
        //         datasets: [{
        //             label: 'Top Agent',
        //             data: [],
        //             backgroundColor: [
        //                 'rgba(255, 99, 132, 0.2)',
        //                 'rgba(54, 162, 235, 0.2)',
        //                 'rgba(255, 206, 86, 0.2)',
        //                 'rgba(75, 192, 192, 0.2)',
        //                 'rgba(153, 102, 255, 0.2)',
        //                 'rgba(255, 159, 64, 0.2)'
        //             ],
        //             borderColor: [
        //                 'rgba(255, 99, 132, 1)',
        //                 'rgba(54, 162, 235, 1)',
        //                 'rgba(255, 206, 86, 1)',
        //                 'rgba(75, 192, 192, 1)',
        //                 'rgba(153, 102, 255, 1)',
        //                 'rgba(255, 159, 64, 1)'
        //             ],
        //             borderWidth: 1
        //         }]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: true,
        //         scales: {
        //             xAxes: [{
        //                 ticks: {
        //                     beginAtZero: true
        //                 }
        //             }]
        //         },
        //         events: false,
        //         animation: {
        //             duration: 500,
        //             easing: "easeOutQuart",
        //             onComplete: function () {
        //                 barLabel(this);
        //             }
        //         }
        //     }
        // });
        // const bottomOccupancyChart = new Chart(document.getElementById('bottomOccupancyChart'), {
        //     type: 'horizontalBar',
        //     data: {
        //         labels: [],
        //         datasets: [{
        //             label: 'Bottom Agent',
        //             data: [],
        //             backgroundColor: [
        //                 'rgba(255, 99, 132, 0.2)',
        //                 'rgba(54, 162, 235, 0.2)',
        //                 'rgba(255, 206, 86, 0.2)',
        //                 'rgba(75, 192, 192, 0.2)',
        //                 'rgba(153, 102, 255, 0.2)',
        //                 'rgba(255, 159, 64, 0.2)'
        //             ],
        //             borderColor: [
        //                 'rgba(255, 99, 132, 1)',
        //                 'rgba(54, 162, 235, 1)',
        //                 'rgba(255, 206, 86, 1)',
        //                 'rgba(75, 192, 192, 1)',
        //                 'rgba(153, 102, 255, 1)',
        //                 'rgba(255, 159, 64, 1)'
        //             ],
        //             borderWidth: 1
        //         }]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: true,
        //         scales: {
        //             xAxes: [{
        //                 ticks: {
        //                     beginAtZero: true
        //                 }
        //             }]
        //         },
        //         events: false,
        //         animation: {
        //             duration: 500,
        //             easing: "easeOutQuart",
        //             onComplete: function () {
        //                 barLabel(this);
        //             }
        //         }
        //     }
        // });
        // const waitlistChart = new Chart(document.getElementById('waitlistChart'), {
        //     type: 'doughnut',
        //     data: {
        //         labels: [],
        //         datasets: [{
        //             data: [],
        //             backgroundColor: [
        //                 'rgba(255, 99, 132, 0.2)',
        //                 'rgba(54, 162, 235, 0.2)',
        //                 'rgba(255, 206, 86, 0.2)',
        //                 'rgba(75, 192, 192, 0.2)',
        //                 'rgba(153, 102, 255, 0.2)',
        //                 'rgba(255, 159, 64, 0.2)'
        //             ],
        //             borderColor: [
        //                 'rgba(255, 99, 132, 1)',
        //                 'rgba(54, 162, 235, 1)',
        //                 'rgba(255, 206, 86, 1)',
        //                 'rgba(75, 192, 192, 1)',
        //                 'rgba(153, 102, 255, 1)',
        //                 'rgba(255, 159, 64, 1)'
        //             ],
        //             borderWidth: 1
        //         }]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: true,
        //         events: false,
        //         animation: {
        //             duration: 500,
        //             easing: "easeOutQuart",
        //             onComplete: function () {
        //                 let ctx = this.chart.ctx;
        //                 ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontFamily, 'normal', Chart.defaults.global.defaultFontFamily);
        //                 ctx.textAlign = 'center';
        //                 ctx.textBaseline = 'bottom';

        //                 this.data.datasets.forEach(function (dataset) {
        //                     for (let i = 0; i < dataset.data.length; i++) {
        //                         let model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model,
        //                             total = dataset._meta[Object.keys(dataset._meta)[0]].total,
        //                             mid_radius = model.innerRadius + (model.outerRadius - model.innerRadius) / 2,
        //                             start_angle = model.startAngle,
        //                             end_angle = model.endAngle,
        //                             mid_angle = start_angle + (end_angle - start_angle) / 2;

        //                         let x = mid_radius * Math.cos(mid_angle);
        //                         let y = mid_radius * Math.sin(mid_angle);

        //                         ctx.fillStyle = dataset.borderColor[i];

        //                         let val = dataset.data[i];
        //                         let percent = String(Math.round(val / total * 100)) + "%";

        //                         if (val != 0) {
        //                             ctx.fillText(dataset.data[i], model.x + x, model.y + y);
        //                             // Display percent in another line, line break doesn't work for fillText
        //                             ctx.fillText(percent, model.x + x, model.y + y + 15);
        //                         }
        //                     }
        //                 });
        //             }
        //         }
        //     }
        // });
        // function barLabel(barChart) {
        //     let ctx = barChart.chart.ctx;
        //     ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontFamily, 'normal', Chart.defaults.global.defaultFontFamily);
        //     ctx.textAlign = "center";
        //     ctx.textBaseline = "bottom";
        //     barChart.data.datasets.forEach(function (dataset) {
        //         for (let i = 0; i < dataset.data.length; i++) {
        //             let model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model,
        //                 val = dataset.data[i];
        //             if (val != 0) {
        //                 ctx.fillStyle = dataset.borderColor[i];
        //                 ctx.fillText(dataset.data[i], model.x * 0.9, model.y);
        //             }
        //         }
        //     });
        // }
        // function getTotalAgentOnlineT2() {
        //     $.ajax({
        //         url: "{{ url('/nossablastwa/getTotalAgentOnlineT2') }}",
        //         type: "GET",
        //         dataType: "json",
        //         success: function (data) {
        //             if (data) {
        //                 let jParse = JSON.parse(data);
        //                 // console.log(jParse);
        //                 topOccupancyChart.data.datasets[0].data = jParse.top_value;
        //                 topOccupancyChart.data.labels = jParse.top_label;
        //                 topOccupancyChart.update();
        //                 bottomOccupancyChart.data.datasets[0].data = jParse.bottom_value;
        //                 bottomOccupancyChart.data.labels = jParse.bottom_label;
        //                 bottomOccupancyChart.update();
        //                 if ($.fn.DataTable.isDataTable('#topAgentStatusTable')) {
        //                     topAgentStatusTable.destroy();
        //                     $('#topAgentStatusTable').empty();
        //                 }
        //                 topAgentStatusTable = $('#topAgentStatusTable').DataTable({
        //                     "fixedHeader": {
        //                         header: true,
        //                         footer: true
        //                     },
        //                     "initComplete": function (settings, json) {
        //                         $("#topAgentStatusTable").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        //                     //     this.api().columns('campaign_name:name').every(function () {
        //                     //         let column = this;
        //                     //         let select = $('<select><option value="" selected>All Campaign</option></select>')
        //                     //             .appendTo($(column.header()).empty())
        //                     //             .on('change', function () {
        //                     //                 let val = $.fn.dataTable.util.escapeRegex(
        //                     //                     $(this).val()
        //                     //                 );
        //                     //                 column
        //                     //                     .search(val ? '^' + val + '$' : '', true, false)
        //                     //                     .draw();
        //                     //             });
        //                     //         column.data().unique().sort().each(function (d, j) {
        //                     //             select.append('<option value="' + d + '">' + d + '</option>')
        //                     //         });
        //                     //     });
        //                     },
        //                     "lengthMenu": [
        //                         [10, 25, 50, 100, -1],
        //                         [10, 25, 50, 100, "All"]
        //                     ],
        //                     "pageLength": 10,
        //                     "scrollY": "250px",
        //                     "scrollCollapse": true,
        //                     "paging": true,
        //                     "stateSave": true,
        //                     "data": jParse.data,
        //                     "columns": [{
        //                             "data": "agent_name",
        //                             "title": "Nama Agent"
        //                         },
        //                         {
        //                             "data": "distribution_status",
        //                             "title": "Distribution Status",
        //                             "render": function (data, type, row, meta) {
        //                                 switch (data) {
        //                                     case 'online':
        //                                         return '<h4 class="badge btn-success">Online</h4>';
        //                                         break;
        //                                     case 'offline':
        //                                         return '<h4 class="badge btn-danger">Offline</h4>';
        //                                         break;
        //                                     case 'aux_1':
        //                                         return '<h4 class="badge btn-warning">Konsultasi</h4>';
        //                                         break;
        //                                     case 'aux_2':
        //                                         return '<h4 class="badge btn-warning">Supporting</h4>';
        //                                         break;
        //                                     case 'aux_3':
        //                                         return '<h4 class="badge btn-warning">Gangguan</h4>';
        //                                         break;
        //                                     case 'aux_4':
        //                                         return '<h4 class="badge btn-warning">Toilet</h4>';
        //                                         break;
        //                                     case 'aux_5':
        //                                         return '<h4 class="badge btn-warning">Air Minum</h4>';
        //                                         break;
        //                                     case 'aux_6':
        //                                         return '<h4 class="badge btn-warning">Sholat</h4>';
        //                                         break;
        //                                     case 'aux_7':
        //                                         return '<h4 class="badge btn-warning">Lunch Break</h4>';
        //                                         break;
        //                                     case 'aux_8':
        //                                         return '<h4 class="badge btn-warning">Briefing</h4>';
        //                                         break;
        //                                     case 'aux_9':
        //                                         return '<h4 class="badge btn-warning">Update System</h4>';
        //                                         break;
        //                                     default:
        //                                         return '<h4 class="badge btn-info">'+data+'</h4>';
        //                                         break;
        //                                 }
        //                             }
        //                         },
        //                         {
        //                             "data": "dist_status_duration",
        //                             "title": "Durasi",
        //                             "render": function (data, type, row, meta) {
        //                                 let num;
        //                                 let ret;
        //                                 if (data == 0) {
        //                                     ret = '00:00:00';
        //                                 } else {
        //                                     num = data;
        //                                     let sec = num % 60;
        //                                     if (sec<10) {
        //                                         ret = '0'+sec;
        //                                     } else {
        //                                         ret = sec;
        //                                     }
        //                                     num = (num-sec) / 60;
        //                                     let min = num % 60;
        //                                     if (min<10) {
        //                                         ret = '0'+min+':'+ret;
        //                                     } else {
        //                                         ret = min+':'+ret;
        //                                     }
        //                                     let hour = (num-min) / 60;
        //                                     ret = hour+':'+ret;
        //                                 }
        //                                 return ret;
        //                             }
        //                         },
        //                         {
        //                             "data": "pbx_status",
        //                             "title": "PBX Status"
        //                         },
        //                         {
        //                             "data": "connected_number",
        //                             "title": "Connected Number"
        //                         },
        //                         {
        //                             "data": "status_duration",
        //                             "title": "Durasi",
        //                             "render": function (data, type, row, meta) {
        //                                 let num;
        //                                 let ret;
        //                                 if (data == 0) {
        //                                     ret = '00:00:00';
        //                                 } else {
        //                                     num = data;
        //                                     let sec = num % 60;
        //                                     if (sec<10) {
        //                                         ret = '0'+sec;
        //                                     } else {
        //                                         ret = sec;
        //                                     }
        //                                     num = (num-sec) / 60;
        //                                     let min = num % 60;
        //                                     if (min<10) {
        //                                         ret = '0'+min+':'+ret;
        //                                     } else {
        //                                         ret = min+':'+ret;
        //                                     }
        //                                     let hour = (num-min) / 60;
        //                                     ret = hour+':'+ret;
        //                                 }
        //                                 return ret;
        //                             }
        //                         },
        //                         {
        //                             "data": "aht",
        //                             "title": "AHT",
        //                             "render": function (data, type, row, meta) {
        //                                 if (parseInt(row['total_call'])!=0) {
        //                                     return Math.round(parseInt(row['handlingtime']) / parseInt(row['total_call']));
        //                                 } else {
        //                                     return 0;
        //                                 }
        //                             }
        //                         },
        //                     ],
        //                 });

        //             }
        //             setTimeout(getTotalAgentOnlineT2, 60000);
        //         },
        //         error: function (data) {
        //             console.log(data);
        //         }
        //     })
        // }
        // function getWaitlistT2() {
        //     $.ajax({
        //         url: "{{ url('/nossablastwa/getWaitlistT2') }}",
        //         type: "GET",
        //         dataType: "json",
        //         success: function (data) {
        //             if (data) {
        //                 let jParse = JSON.parse(data);
        //                 waitlistPerCampaignChart.data.datasets[0].data = jParse.count;
        //                 waitlistPerCampaignChart.data.labels = jParse.name;
        //                 waitlistPerCampaignChart.update();
        //                 waitlistChart.data.datasets[0].data = jParse.total_count;
        //                 waitlistChart.data.labels = jParse.total_label;
        //                 waitlistChart.update();
        //             }
        //             setTimeout(getWaitlistT2, 60000);
        //         },
        //         error: function (data) {
        //             console.log(data);
        //         }
        //     })
        // }
        // getTotalAgentOnlineT2();
        // getWaitlistT2();
    </script>
@endsection