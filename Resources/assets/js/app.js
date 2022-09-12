let datatable;
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
    }
});
let cacheWitel=['Telkom ACEH','Telkom BABEL','Telkom BALIKPAPAN','Telkom BANDUNG','Telkom BANDUNG BARAT','Telkom BANTEN','Telkom BEKASI','Telkom BENGKULU','Telkom BOGOR','Telkom CIREBON','Telkom DENPASAR','Telkom GORONTALO','Telkom JAKBAR','Telkom JAKPUS','Telkom JAKSEL','Telkom JAKTIM','Telkom JAKUT','Telkom JAMBI','Telkom JEMBER','Telkom KALBAR','Telkom KALSEL','Telkom KALTARA','Telkom KALTENG','Telkom KARAWANG','Telkom KEDIRI','Telkom KUDUS','Telkom LAMPUNG','Telkom MADIUN','Telkom MADURA','Telkom MAGELANG','Telkom MAKASAR','Telkom MALANG','Telkom MALUKU','Telkom MEDAN','Telkom NTB','Telkom NTT','Telkom PAPUA','Telkom PAPUA BARAT','Telkom PASURUAN','Telkom PEKALONGAN','Telkom PURWOKERTO','Telkom RIDAR','Telkom RIKEP','Telkom SAMARINDA','Telkom SEMARANG','Telkom SIDOARJO','Telkom SINGARAJA','Telkom SOLO','Telkom SUKABUMI','Telkom SULSELBAR','Telkom SULTENG','Telkom SULTRA','Telkom SULUT & MALUT','Telkom SUMBAR','Telkom SUMSEL','Telkom SUMUT','Telkom SURABAYA SELATAN','Telkom SURABAYA UTARA','Telkom TANGERANG','Telkom TASIKMALAYA','Telkom YOGYAKARTA'];
let cacheRegional=['01','02','03','04','05','06','07'];
let cacheCampaign=[];

generateDataLokerContact();
getListCampaign();

$('#selectLevel').prepend('<option selected=""></option>').select2({
    theme: "bootstrap",
    placeholder: "Pilih Campaign Level",
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

document.getElementById('editCampaignForm').addEventListener("submit", function(e) {
    e.preventDefault(); // before the code
    let formData = Object.fromEntries(new FormData(this));
    formData[$("#selectWitel").attr("name")] = $("#selectWitel").select2("val");
    formData[$("#selectRegional").attr("name")] = $("#selectRegional").select2("val");
    formData[$("#selectCampaign").attr("name")] = $("#selectCampaign").select2("val");
    formData[$("#selectLevel").attr("name")] = $("#selectLevel").select2("val");
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
            ajaxErrorResponse(xhr, ajaxOptions, thrownError);
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
        url: base_path+'nossablastwa/addContact',
        data: formData,
        dataType: 'json',
        success: function () {
            datatable.ajax.reload( null, false );
            modalAddContactShow(false);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ajaxErrorResponse(xhr, ajaxOptions, thrownError);
        }
    });
    return false;
});

function getListCampaign() {
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
function fillSelectValue(id, variable) {
    if (variable != null) {
        if (Array.isArray(variable)) {
            variable.forEach(element => {
                if ($('#'+id).find("option[value='" + element + "']").length) {
                    $('#'+id).val(element).trigger('change');
                }
            });    
        } else {
            if ($('#'+id).find("option[value='" + variable + "']").length) {
                $('#'+id).val(variable).trigger('change');
            }
        }
    }
}
function concatenator(data) {
    if (Array.isArray(data)) {
        let arrTemp = '';
        data.forEach(element => {
            arrTemp += element;
        });
        return arrTemp.substring(0,arrTemp.length-1);
    } else {
        return data;
    }
}
function generateDataLokerContact() {
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
        fillSelectValue('selectWitel',tk_subregion);
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
        fillSelectValue('selectRegional',tk_region);
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
        fillSelectValue('selectCampaign',campaign);
        $('#selectLevel').val(null).trigger('change');
        fillSelectValue('selectLevel',level);
        document.getElementById('nama').value = data.nama;
        document.getElementById('jabatan').value = data.jabatan;
        document.getElementById('contact_number').value = data.phone_number;
        document.getElementById('userId').value = data.phone_number;
        modalEditCampaignShow(true);
    });
}