let datatable;
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
    }
});
let cacheWitel=[];
let cacheRegional=['REG-1','REG-2','REG-3','REG-4','REG-5','REG-6','REG-7'];
let cacheCampaign=[];


$('select').select2({
    theme: 'bootstrap4',
});

window.modalEditCampaignShow = function(flag) {
    if (flag) {
        $('#editCampaign').modal('show');
    } else {
        $('#editCampaign').modal('hide');
    }
}

window.modalAddContactShow = function(flag) {
    if (flag) {
        $('#selectTag').val(null).trigger('change');
        $('#addContact').modal('show');
    } else {
        $('#addContact').modal('hide');
    }
}

window.deleteContact = function() {
    let phone_number = document.getElementById('userId').value;
    if (confirm('Delete Phone Number '+phone_number+' dari database ?')) {
        let formData = {
            id: phone_number,
        };
        $.ajax({
            type: "POST",
            url: base_path+'nossablastwa/deleteContact',
            data: formData,
            dataType: 'json',
            success: function () {
                datatable.ajax.reload( null, false );
                modalEditCampaignShow(false);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('ERR deleteContact');
                ajaxErrorResponse(xhr, ajaxOptions, thrownError);
            }
        });
    }
}
window.findContact = function() {
    let phone_number = document.getElementById('input_contact_number').value;
    if (phone_number.length>8) {
        let formData = {
            id: phone_number,
        };
        $.ajax({
            type: "POST",
            url: base_path+'nossablastwa/findContact',
            data: formData,
            dataType: 'json',
            success: function (data) 
            {
                if (typeof data.nama != 'undefined' && typeof data.jabatan != 'undefined') {
                    document.getElementById('input_nama').value = data.nama;
                    document.getElementById('input_jabatan').value = data.jabatan;
                    document.getElementById('input_nama').disabled = true;
                    document.getElementById('input_jabatan').disabled = true;
                } else {
                    document.getElementById('input_nama').disabled = false;
                    document.getElementById('input_jabatan').disabled = false;
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('ERR deleteContact');
                ajaxErrorResponse(xhr, ajaxOptions, thrownError);
            }
        });

    }
}
if (!!document.getElementById('editCampaignForm')) {
    document.getElementById('editCampaignForm').addEventListener("submit", function(e) {
        e.preventDefault(); // before the code
        let formData = Object.fromEntries(new FormData(this));
        if ($("#selectWitel").select2("val")!='') {
            formData[$("#selectWitel").attr("name")] = $("#selectWitel").select2("val");
        }
        if ($("#selectRegional").select2("val")!='') {
            formData[$("#selectRegional").attr("name")] = $("#selectRegional").select2("val");
        }
        if ($("#selectCampaign").select2("val")!='') {
            formData[$("#selectCampaign").attr("name")] = $("#selectCampaign").select2("val");
        }
        if ($("#selectLevel").select2("val")!='') {
            formData[$("#selectLevel").attr("name")] = $("#selectLevel").select2("val");
        }
        $.ajax({
            type: "POST",
            url: base_path+'nossablastwa/updateCampaign',
            data: formData,
            dataType: 'json',
            success: function () {
                datatable.ajax.reload( null, false );
                modalEditCampaignShow(false);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('ERR updateCampaign');
                ajaxErrorResponse(xhr, ajaxOptions, thrownError);
            }
        });
        return false;
    });    
}

if (!!document.getElementById('addContactForm')) {
    document.getElementById('addContactForm').addEventListener("submit", function(e) {
        e.preventDefault(); // before the code
        let formData = Object.fromEntries(new FormData(this));
        formData[$("#selectTag").attr("name")] = $("#selectTag").select2("val");
        $.ajax({
            type: "POST",
            url: base_path+'nossablastwa/addContact',
            data: formData,
            dataType: 'json',
            success: function () {
                datatable.ajax.reload( null, false );
                modalAddContactShow(false);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('ERR addContact');
                ajaxErrorResponse(xhr, ajaxOptions, thrownError);
            }
        });
        return false;
    });    
}

window.getListCampaign = function () {
    $.ajax({
        url: base_path+'nossablastwa/listDataCampaign',
        type: "GET",
        success: function(result) {
            cacheCampaign = result;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log('ERR listDataCampaign');
            ajaxErrorResponse(xhr, ajaxOptions, thrownError);
        }
    });
}
window.getListWitel = function () {
    $.ajax({
        url: base_path+'nossablastwa/listDataWitel',
        type: "GET",
        success: function(result) {
            cacheWitel = result;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log('ERR listDataWitel');
            ajaxErrorResponse(xhr, ajaxOptions, thrownError);
        }
    });
}
function concatenator(data) {
    if (Array.isArray(data)) {
        let arrTemp = '';
        data.forEach(element => {
            arrTemp += element+',';
        });
        return arrTemp.substring(0,arrTemp.length-1);
    } else {
        return data;
    }
}
window.initWitel = function () {
    $('#selectWitel').select2({
        placeholder: "Pilih Witel",
        allowClear: true
    });    
    document.getElementById('selectWitel').innerHTML = '';
    cacheWitel.forEach(element=>{
        let newOption = new Option(element, element, false, false);
        $('#selectWitel').append(newOption);
    });
}
window.initRegional = function () {
    $('#selectRegional').select2({
        placeholder: "Pilih Regional",
        allowClear: true
    });
    
    document.getElementById('selectRegional').innerHTML = '';
    cacheRegional.forEach(element=>{
        let newOption = new Option(element, element, false, false);
        $('#selectRegional').append(newOption);
    });
}
window.initCampaign = function () {
    $('#selectCampaign').select2({
        placeholder: "Pilih Campaign Blast",
        allowClear: true
    });
    
    document.getElementById('selectCampaign').innerHTML = '';
    cacheCampaign.forEach(element=>{
        let newOption = new Option(element, element, false, false);
        $('#selectCampaign').append(newOption);
    });
}
window.initLevel = function () {
    $('#selectLevel').select2({
        placeholder: 'Pilih Campaign Level',
        allowClear: true
    });
    
    $('#selectLevel').val(null).trigger('change');
}
window.generateDataLokerContact = function () {
    let columnData = [{
            "data": "nama",
            "title": "Nama"
        },
        {
            "data": "jabatan",
            "title": "Jabatan",
        },
        {
            "data": "phone_number",
            "title": "Contact Number",
        },
        {
            "data": "campaign",
            "title": "Campaign",
            "render": function ( data, type, row, meta ) {
                return concatenator(data);
            }
        },
        {
            "data": "level",
            "title": "Level",
            "render": function ( data, type, row, meta ) {
                return concatenator(data);
            }
        },
        {
            "data": "tk_region",
            "title": "Regional",
            "render": function ( data, type, row, meta ) {
                return concatenator(data);
            }
        },
        {
            "data": "tk_subregion",
            "title": "Witel",
            "render": function ( data, type, row, meta ) {
                return concatenator(data);
            }
        },
    ];
    let tableTemp = document.getElementById('datatable');
    let tableFoot = document.createElement('tfoot');
    tableTemp.appendChild(tableFoot);
    let tableRow = document.createElement('tr');
    tableFoot.appendChild(tableRow);
    columnData.forEach(column => {
        let tableHead = document.createElement('th');
        tableHead.innerHTML = column.title;
        tableRow.appendChild(tableHead);
    });
    datatable = $('#datatable').DataTable({
            "destroy": true,
            "ajax": {
                "url": base_path+'nossablastwa/listDataContact',
                "type": "POST",
            },
            "columns": columnData,
            "lengthMenu": [
                [10, 25, 50, 100, 1000, -1],
                [10, 25, 50, 100, 1000, "All"]
            ],
            "initComplete": function () {
                // Apply the search
                this.api().columns().every(function () {
                    let that = this;
                    $('input', this.footer()).on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                });
            },
        });
    $('#datatable tfoot th').each(function () {
        let title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
    });
    $('#datatable tbody').on('dblclick', 'tr', function () { //Edit data contact
        let data = datatable.rows(this).data()[0];
        let tk_subregion = data.tk_subregion;
        let tk_region = data.tk_region;
        let campaign = data.campaign;
        let level = data.level;
        initWitel();
        $('#selectWitel').val(tk_subregion).trigger('change');
        initRegional();
        $('#selectRegional').val(tk_region).trigger('change');
        initCampaign();
        $('#selectCampaign').val(campaign).trigger('change');
        initLevel();
        $('#selectLevel').val(level).trigger('change');
        document.getElementById('nama').value = data.nama;
        document.getElementById('jabatan').value = data.jabatan;
        document.getElementById('contact_number').value = data.phone_number;
        document.getElementById('userId').value = data.phone_number;
        modalEditCampaignShow(true);
    });
}