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
                            <h4 class="header-title mt-0">Data Contact</h4>
                            <div class="table-responsive dash-social">
                                <table id="datatable" class="table table-bordered" aria-hidden="true">
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
                                        <select id="selectWitel" name="tk_subregion[]" class="form-control" multiple="multiple" style="width:100%">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="selectRegional" class="col-sm-4 col-form-label">Regional</label>
                                    <div class="col-sm-8">
                                        <select id="selectRegional" name="tk_region[]" class="form-control" style="width:100%" multiple="multiple">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="selectCampaign" class="col-sm-4 col-form-label">Campaign</label>
                                    <div class="col-sm-8">
                                        <select id="selectCampaign" name="campaign[]" class="form-control" multiple="multiple" style="width:100%">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="selectLevel" class="col-sm-4 col-form-label">Level</label>
                                    <div class="col-sm-8">
                                        <select id="selectLevel" name="level[]" class="form-control" multiple="multiple" style="width:100%">
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
                            <form id="addContactForm" action="{{ url('nossablastwa/addContact') }}" method="POST">
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
@endsection