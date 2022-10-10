@extends('layouts.app')

@section('module_css')
    <link rel="stylesheet" href="{{ mix('css/nossablastwa.css') }}">
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <h4 class="header-title">Data Contact</h4>
            </span>
            <button type="button" class="btn btn-primary float-right" onclick="modalAddContactShow(true)">Add New Contact</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable" class="table-striped no-margin" style="width:100%" aria-hidden="true">
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
                            <form id="editCampaignForm" action="{{ url('nossablastwa/saveCampaign') }}" method="POST">
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
                                        <select id="selectWitel" name="tk_subregion[]" class="form-control select2" multiple="multiple" style="width:100%">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="selectRegional" class="col-sm-4 col-form-label">Regional</label>
                                    <div class="col-sm-8">
                                        <select id="selectRegional" name="tk_region[]" class="form-control select2" style="width:100%" multiple="multiple">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="selectCampaign" class="col-sm-4 col-form-label">Campaign</label>
                                    <div class="col-sm-8">
                                        <select id="selectCampaign" name="campaign[]" class="form-control select2" multiple="multiple" style="width:100%">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="selectLevel" class="col-sm-4 col-form-label">Level</label>
                                    <div class="col-sm-8">
                                        <select id="selectLevel" name="level[]" class="form-control select2" multiple="multiple" style="width:100%">
                                            <option value="1">Level 1</option>
                                            <option value="2">Level 2</option>
                                            <option value="3">Level 3</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" id="userId" name="id"/>
                                <button type="button" class="btn btn-danger float-left" onclick="deleteContact()">Delete</button>
                                <input type="submit" value="Update" class="btn btn-success float-right">
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
                            <form id="addContactForm" action="{{ url('nossablastwa/addContact') }}" method="POST">
                                <div class="form-group row">
                                    <label for="nama" class="col-sm-4 col-form-label">Nama</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="input_nama" name="nama" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="jabatan" class="col-sm-4 col-form-label">Jabatan</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="input_jabatan" name="jabatan" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="contact_number" class="col-sm-4 col-form-label">Contact Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="input_contact_number" name="contact_number" class="form-control" onkeyup="findContact()" />
                                    </div>
                                </div>
                                <div class="row" style="float: right;">
                                    <input type="submit" value="Save" class="btn btn-success">
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
        generateDataLokerContact();
        getListCampaign();
        getListWitel();

    </script>
@endsection