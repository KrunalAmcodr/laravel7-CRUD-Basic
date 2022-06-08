@extends('layouts.app')

@section('content')
    <style>
        label.error {
            color: red;
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="title">{{ __('Dashboard') }}</div>
                        <div class="addbutton">
                            <button class="btn btn-success" id="addajaxitem">Create New Items</button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success" id="successMessageAlert">
                                {{ $message }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <table class="table table-hover" id="itemtable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>S. No</th>
                                    <th>Item Name</th>
                                    <th>Descriptions</th>
                                    <th>Date</th>
                                    <th>Images</th>
                                    <th style="width:210px;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="itemtable-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ajaxitem-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Item</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('items.store') }}" method="POST" class="needs-validation" id="ajaxitem-form"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="update-route" id="update-route">
                        <div class="form-row">
                            <div class="col">
                                <label for="item_name">Item's Name</label>
                                <input type="text" class="form-control @error('item_name') is-invalid @enderror"
                                    name="item_name" id="item_name" placeholder="Item's Name"
                                    value="{{ old('item_name') }}" required>
                                <span class="text-danger errorspan" role="alert"></span>
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <div class="editor w-100">
                                <label for="descriptions">Descriptions</label>
                                <textarea class="ckeditor form-control @error('descriptions') is-invalid @enderror" id="descriptions"
                                    name="descriptions" required>{{ old('descriptions') }}</textarea>
                                <span class="text-danger errorspan" role="alert"></span>
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <label for="manufacture_date">Manufacture Date</label>
                            <input type='text' class="form-control" name="manufacture_date" id="manufacture_date"
                                placeholder="Select Manufacture Date"
                                @if ($errors->any()) value="{{ date('m/d/Y', strtotime(old('manufacture_date'))) }}" @endif
                                required>
                            <span class="text-danger errorspan" role="alert"></span>
                        </div>
                        <div class="form-row my-3">
                            <label for="images" class="form-label">Images</label>
                            <input type="file" class="@error('images') is-invalid @enderror form-control" id="images"
                                name="images[]" value="{{ old('images') }}" required multiple>
                            <span class="text-danger errorspan" role="alert"></span>
                        </div>
                        <div class="mt-3" id="selectedimage">
                            <h5>Selected Images:</h5>
                            <div id="imagelist"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" id="saveajaxitem">Submit Item</button>
                    <button class="btn btn-primary" type="button" id="updateajaxitem">Update Item</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('extrascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.4/jquery.validate.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function($) {
            $.noConflict();

            $('#manufacture_date').datepicker({
                format: 'mm/dd/yyyy',
                autoclose: true,
            })

            var modal = $('#ajaxitem-modal'),
                form = $('#ajaxitem-form'),
                btnAdd = $('#addajaxitem'),
                btnSave = $('#saveajaxitem'),
                btnEdit = $('#editajaxitem'),
                btnUpdate = $('#updateajaxitem'),
                divSelectedImages = $('#selectedimage'),
                path = "{{ asset('image/') }}";

            var table = $('#itemtable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ajaxitems.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        'visible': false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'descriptions',
                        name: 'descriptions',
                    },
                    {
                        data: 'manufacture_date',
                        name: 'manufacture_date',
                        // "render": function(data) {
                        //     var date = new Date(data);
                        //     var month = date.getMonth() + 1;
                        //     return (month.toString().length > 1 ? month : "0" + month) + "/" + date.getDate() + "/" + date.getFullYear();
                        // }
                    },
                    {
                        data: 'images',
                        name: 'images',
                        render: function(data, type, full, meta) {
                            var dataClean = $.parseJSON(data.replaceAll("&quot;", "\""));
                            var imageHtml = '';
                            $.each(dataClean, function(key, value) {
                                imageHtml += '<img src="' + path + '/' + value.replaceAll(
                                        "\"", "") +
                                    '" width="100" height="auto" class="m-1" />';
                            });
                            return imageHtml;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            btnAdd.click(function() {
                $("#ajaxitem_id").val('');
                modal.modal('show');
                modal.find('.modal-title').text('Add New Item');
                btnSave.show();
                btnUpdate.hide();
                divSelectedImages.hide();
            })

            btnSave.click(function(e) {
                e.preventDefault();
                var formData = new FormData($('#ajaxitem-form')[0]);
                formData.append('descriptions', CKEDITOR.instances.descriptions.getData());

                $('.errorspan').text('');

                $.ajax({
                    url: "{{ route('ajaxitems.store') }}",
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#ajaxitem-form')[0].reset();
                        table.draw()
                        $('#ajaxitem-modal').modal('hide');
                    },
                    error: function(response) {
                        $.each(response.responseJSON.errors, function(key, value) {
                            if (key.indexOf('images.') != -1) {
                                $('#' + key.split('.')[0]).parent().find('.errorspan')
                                    .text(
                                        value.join(
                                            ' and ').replaceAll(key, "images"));
                            } else {
                                $('#' + key).parent().find('.errorspan').text(value);
                            }
                        });
                    }
                })
            })

            $(document).on('click', '#editajaxitem', function() {
                btnSave.hide();
                btnUpdate.show();
                modal.find('.modal-title').text('Update Item');
                var rowData = table.row($(this).parents('tr')).data();
                divSelectedImages.show();
                $.each(rowData, function(key, value) {
                    if (key == 'images') {
                        var dataCleanImages = $.parseJSON(value);
                        var imageHtml = '';
                        $.each(dataCleanImages, function(key, value) {
                            imageHtml += '<img src="' + path + '/' + value.replaceAll(
                                    "\"", "") +
                                '" width="100" height="auto" class="m-1" />';
                        });
                        $('#imagelist').empty().append(imageHtml);
                    } else if (key == 'descriptions') {
                        CKEDITOR.instances.descriptions.setData(value)
                    } else {
                        form.find('#' + key).val(value);
                    }
                })
                var getUrl = window.location;
                var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];

                form.find('input#update-route').val(baseUrl + '/updateitem/' + rowData.id);

                modal.modal()
            })

            btnUpdate.click(function() {

                if(!confirm("Are you sure?")) return;
                var formData = new FormData($('#ajaxitem-form')[0]);
                formData.append('descriptions', CKEDITOR.instances.descriptions.getData());
                var itemEditRoute = form.find('input#update-route').val();
                $('.errorspan').text('');
                $.ajax({
                    url: itemEditRoute,
                    data: formData,
                    type: "POST",
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#ajaxitem-form')[0].reset();
                        table.draw()
                        $('#ajaxitem-modal').modal('hide');
                    },
                    error: function(response) {
                        $.each(response.responseJSON.errors, function(key, value) {
                            if (key.indexOf('images.') != -1) {
                                $('#' + key.split('.')[0]).parent().find('.errorspan')
                                    .text(
                                        value.join(
                                            ' and ').replaceAll(key, "images"));
                            } else {
                                $('#' + key).parent().find('.errorspan').text(value);
                            }
                        });
                    }
                })
            })
        })
    </script>
@endpush
