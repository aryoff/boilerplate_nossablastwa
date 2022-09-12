let datatable;
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
    }
});
generateDataLokerContact();
getListWitel();
getListRegional();
getListCampaign();
$('#selectLevel').prepend('<option selected=""></option>').select2({
    theme: "bootstrap",
    placeholder: "Pilih Campaign Level",
    allowClear: true
});
$('#selectTag').select2({
    placeholder: "Pilih Tag",
    allowClear: true
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
let cacheWitel=[];
window.getListWitel = function() {
    $.ajax({
        url: basse_path+'/getListWitel',
        type: "POST",
        success: function(result) {
            cacheWitel = result;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // console.log('ERR Get Header');
            // ajaxErrorResponse(xhr, ajaxOptions, thrownError);
        }
    });
}
let cacheRegional=[];
window.getListRegional = function() {
    $.ajax({
        url: base_path+'/getListRegional',
        type: "POST",
        success: function(result) {
            cacheRegional = result;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // console.log('ERR Get Header');
            // ajaxErrorResponse(xhr, ajaxOptions, thrownError);
        }
    });
}
let cacheCampaign=[];
window.getListCampaign = function() {
    $.ajax({
        url: base_path+'/getListCampaign',
        type: "POST",
        success: function(result) {
            cacheCampaign = result;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // console.log('ERR Get Header');
            // ajaxErrorResponse(xhr, ajaxOptions, thrownError);
        }
    });
}
document.getElementById('editCampaignForm').addEventListener("submit", function(e) {
    e.preventDefault(); // before the code
    let formData = Object.fromEntries(new FormData(this));
    formData[$("#selectWitel").attr("name")] = $("#selectWitel").select2("val");
    formData[$("#selectRegional").attr("name")] = $("#selectRegional").select2("val");
    $.ajax({
        type: "POST",
        url: base_path+'/saveCampaign',
        data: formData,
        dataType: 'json',
        success: function () {
            datatable.ajax.reload( null, false );
            modalEditCampaignShow(false);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // ajaxErrorResponse(xhr, ajaxOptions, thrownError);
        }
    });
    return false;
});
document.getElementById('addContactForm').addEventListener("submit", function(e) {
    e.preventDefault(); // before the code
    let formData = Object.fromEntries(new FormData(this));
    formData[$("#selectTag").attr("name")] = $("#selectTag").select2("val");
    $.ajax({
        type: "POST",
        url: base_path+'/addContact',
        data: formData,
        dataType: 'json',
        success: function () {
            datatable.ajax.reload( null, false );
            modalAddContactShow(false);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // ajaxErrorResponse(xhr, ajaxOptions, thrownError);
        }
    });
    return false;
});

window.generateDataLokerContact = function() {
    let columnData = [{
            "data": "nama",
            "title": "Nama"
        },
        {
            "data": "jabatan",
            "title": "Jabatan",
        },
        {
            "data": "contact_number",
            "title": "Contact Number",
        },
        {
            "data": "wilayah",
            "title": "Wilayah",
            "render": function ( data, type, row, meta ) {
                let returnVal = '';
                let temp = JSON.parse(data);
                Object.entries(temp).forEach(([key_result, value_result]) => {
                    returnVal += '<div class="row"><div class="col-4">'+key_result+'</div><div class="col-8">';
                    value_result.forEach(element => {
                        returnVal += element+' ';
                    })
                    returnVal += '</div></div>';
                });
                return returnVal;
            }
        },
        {
            "data": "campaign_level",
            "title": "Campaign",
            "render": function ( data, type, row, meta ) {
                let returnVal = '';
                let temp = JSON.parse(data);
                Object.entries(temp).forEach(([key_result, value_result]) => {
                    returnVal += '<div class="row"><div class="col">'+key_result+'&nbsp;:&nbsp;'+value_result+'</div></div>';
                });
                return returnVal;
            }
        },
        {
            "data": "tag",
            "title": "Tag",
            "render": function ( data, type, row, meta ) {
                let returnVal = '';
                let temp = JSON.parse(data);
                temp.forEach(element => {
                    returnVal += element.substring(1)+' ';
                });
                return returnVal;
            }
        },
        {
            "data": null,
            "title": "Status",
            "render": function ( data, type, row, meta ) {
                    let status = 'Active';
                    if (row['campaign_level']=='{}') {status = 'Non Active';}
                    return status;
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
                "url": base_path+'nossablastwa/getDataLokerContact',
                "type": "POST",
            },
            "columns": columnData,
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
        let wilayah = JSON.parse(data.wilayah);
        let campaign_level = JSON.parse(data.campaign_level);
        if ($('#selectWitel').hasClass("select2-hidden-accessible")) {
            $('#selectWitel').select2('destroy');
        }
        document.getElementById('selectWitel').innerHTML = '';
        $('#selectWitel').select2({
            placeholder: "Pilih Witel",
            allowClear: true
        });
        cacheWitel.forEach(element=>{
            let newOption = new Option(element, element, false, false);
            $('#selectWitel').append(newOption);
        });
        if (typeof wilayah.witel != 'undefined') {
            wilayah.witel.forEach(element => {
                if ($('#selectWitel').find("option[value='" + element + "']").length) {
                    $('#selectWitel').val(element).trigger('change');
                } else {
                    // Create a DOM Option and pre-select by default
                    let newOption = new Option(element, element, true, true);
                    // Append it to the select
                    $('#selectWitel').append(newOption);
                }
            });
        }
        if ($('#selectRegional').hasClass("select2-hidden-accessible")) {
            $('#selectRegional').select2('destroy');
        }
        document.getElementById('selectRegional').innerHTML = '';
        $('#selectRegional').select2({
            placeholder: "Pilih Regional",
            allowClear: true
        });
        cacheRegional.forEach(element=>{
            let newOption = new Option(element, element, false, false);
            $('#selectRegional').append(newOption);
        });
        if (typeof wilayah.regional != 'undefined') {
            wilayah.regional.forEach(element => {
                if ($('#selectRegional').find("option[value='" + element + "']").length) {
                    $('#selectRegional').val(element).trigger('change');
                } else {
                    let newOption = new Option(element, element, true, true);
                    $('#selectRegional').append(newOption);
                }
            });
        }
        if ($('#selectCampaign').hasClass("select2-hidden-accessible")) {
            $('#selectCampaign').select2('destroy');
        }
        document.getElementById('selectCampaign').innerHTML = '';
        $('#selectCampaign').prepend('<option selected=""></option>').select2({
            theme: 'bootstrap',
            placeholder: "Pilih Campaign",
            allowClear: true
        });
        cacheCampaign.forEach(element=>{
            let newOption = new Option(element, element, false, false);
            $('#selectCampaign').append(newOption);
        });
        if (!(campaign_level && Object.keys(campaign_level).length === 0 && Object.getPrototypeOf(campaign_level) === Object.prototype)) {
            Object.entries(campaign_level).forEach(([key_result, value_result]) => {
                if ($('#selectCampaign').find("option[value='" + key_result + "']").length) {
                    $('#selectCampaign').val(key_result).trigger('change');
                } else {
                    let newOption = new Option(key_result, key_result, true, true);
                    $('#selectCampaign').append(newOption);
                }
            });
        }
        if (!(campaign_level && Object.keys(campaign_level).length === 0 && Object.getPrototypeOf(campaign_level) === Object.prototype)) {
            Object.entries(campaign_level).forEach(([key_result, value_result]) => {
                if ($('#selectLevel').find("option[value='" + value_result + "']").length) {
                    console.log(campaign_level)
                    $('#selectLevel').val(value_result).trigger('change');
                }
            });
        }
        document.getElementById('nama').value = data.nama;
        document.getElementById('jabatan').value = data.jabatan;
        document.getElementById('contact_number').value = data.contact_number;
        document.getElementById('userId').value = data.id;
        modalEditCampaignShow(true);
    });
}