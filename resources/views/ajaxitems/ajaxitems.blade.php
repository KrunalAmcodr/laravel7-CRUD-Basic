@extends('layouts.app')

@section('content')
    <style>
        label.error {
            color: red;
        }

        tbody.itemtable-body tr td {
            align-items: center;
            vertical-align: middle;
            justify-content: center;
        }

        i.close-icon {
            top: -10px;
            right: 0;
            cursor: pointer;
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="title">{{ __('Dashboard') }}</div>
                        <div class="addbutton">
                            <a href="{{ route('ajaxitems.excelexport') }}" class="btn btn-primary">Export Exclesheet</a>
                            <a href="{{ route('ajaxitems.pdfexport') }}" class="btn btn-primary">Export PDF</a>
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
                                    <th>Sr. No</th>
                                    <th>Item Name</th>
                                    <th>Descriptions</th>
                                    <th>Date</th>
                                    <th>Images</th>
                                    <th>Action</th>
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
                            <input type="hidden" id="itemslectedimage" name="itemslectedimage">
                            <h5>Selected Images:</h5>
                            <div id="imagelist" class="d-flex mt-3"></div>
                        </div>
                    </form>
                    <div id="viewitems">
                        <div class="item_name"></div>
                        <div class="descriptions"></div>
                        <div class="manufacture_date"></div>
                        <div class="images"></div>
                    </div>
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
                btnUpdate = $('#updateajaxitem'),
                divSelectedImages = $('#selectedimage'),
                divViewItems = $('#viewitems');
            path = "{{ asset('image/') }}";

            var table = $('#itemtable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ajaxitems.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        'visible': false,
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        width:'50px',
                        searchable: false
                    },
                    {
                        data: 'item_name',
                        name: 'item_name',
                        width:'100px'
                    },
                    {
                        data: 'descriptions',
                        name: 'descriptions',
                    },
                    {
                        data: 'manufacture_date',
                        name: 'manufacture_date',
                        "render": function(data) {
                            var date = new Date(data);
                            // var month = date.getMonth() + 1;
                            // return (month.toString().length > 1 ? month : "0" + month) + "/" + date.getDate() + "/" + date.getFullYear();
                            return (('0' + (date.getMonth() + 1)).slice(-2) + "/" + ('0' + date
                                .getDate()).slice(-2) + "/" + +date.getFullYear());
                        },
                        width:'50px',
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
                        width:'250px',
                        searchable: false
                    },
                    {
                        data: null,
                        name: 'action',
                        render: function(data, type, full, meta) {
                            var html =
                                '<button class="btn btn-info btn-edit" id="editajaxitem" data-rowid="' +
                                data.id +
                                '"><i class="bi bi-pencil-square"></i></button> ';
                            html += '<button class="btn btn-primary btn-view" data-rowid="' + data
                                .id + '" id="viewajaxitem"><i class="bi bi-eye"></i></button> ';
                            html += '<button data-rowid="' + data.id +
                                '" class="btn btn-danger btn-delete" id="deleteajaxitem"><i class="bi bi-trash3"></i></button>';
                            return html;
                        },
                        // className: "dt-center editor-edit",
                        // defaultContent: '<i class="bi bi-pencil-square"></i>',
                        width:'140px',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            btnAdd.click(function() {
                $("#ajaxitem_id").val('');
                modal.modal('show');
                modal.find('.modal-title').text('Add New Item');
                $('#ajaxitem-form')[0].reset();
                CKEDITOR.instances.descriptions.setData('');
                btnSave.show();
                btnUpdate.hide();
                divSelectedImages.hide();
                divViewItems.hide();
                form.show();
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
                divViewItems.hide();
                form.show();
                modal.find('.modal-title').text('Update Item');
                var rowData = table.row($(this).parents('tr')).data();
                divSelectedImages.show();
                $.each(rowData, function(key, value) {
                    if (key == 'images') {
                        var dataCleanImages = $.parseJSON(value);
                        var imageHtml = '';
                        $.each(dataCleanImages, function(key, value) {
                            imageHtml +=
                                "<div class='selected-image-container position-relative d-inline-flex mr-2'><input type='hidden' class='selectedimageinput' name='selectedimageinput[]' value='" +value +"'>";imageHtml += '<img src="' + path + '/' + value.replaceAll("\"", "") +'" width="100" height="auto" /><i class="bi bi-x-circle-fill position-absolute cursor-pointer close-icon text-danger" id="removeimage"></i></div>';
                        });
                        $('#imagelist').empty().append(imageHtml);
                    } else if (key == 'descriptions') {
                        CKEDITOR.instances.descriptions.setData(value)
                    } else if (key == 'manufacture_date') {
                        var date = new Date(value);
                        var dateFormated = ('0' + (date.getMonth() + 1)).slice(-2) + "/" + ('0' +
                            date.getDate()).slice(-2) + "/" + +date.getFullYear();
                        form.find('#' + key).val(dateFormated);
                    } else {
                        form.find('#' + key).val(value);
                    }
                })
                modal.modal()
                for (var i = 0; i < $(".selectedimageinput").length; i++) {
                    console.log($($(".selectedimageinput")[i]).val());
                }
            })

            $(document).on('click', '#removeimage', function() {
                $(this).parent().remove();
                if ($('#imagelist').is(':empty')){
                    divSelectedImages.hide();
                }
            })

            btnUpdate.click(function() {

                // if (!confirm("Are you sure?")) return;
                var formData = new FormData($('#ajaxitem-form')[0]);
                formData.append('descriptions', CKEDITOR.instances.descriptions.getData());
                $('.errorspan').text('');
                $.ajax({
                    url: "{{ route('ajaxitems.store') }}",
                    data: formData,
                    method: 'POST',
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

            $(document).on('click', '#deleteajaxitem', function() {
                if (!confirm("Are you sure?")) return;
                $.ajax({
                    type: "DELETE",
                    url: '/ajaxitems/' + $(this).data('rowid'),
                    success: function(data) {
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            })

            $(document).on('click', '#viewajaxitem', function() {
                divViewItems.show();
                form.hide();
                btnSave.hide();
                btnUpdate.hide();
                modal.modal();
                modal.find('.modal-title').text('View Item Details');
                var rowData = table.row($(this).parents('tr')).data();
                $.each(rowData, function(key, value) {
                    console.log(key);
                    if (key == 'images') {
                        var dataCleanImages = $.parseJSON(value);
                        var imageHtml = '';
                        $.each(dataCleanImages, function(key, value) {
                            imageHtml += '<img src="' + path + '/' + value.replaceAll(
                                    "\"", "") +
                                '" width="100" height="auto" class="m-1" />';
                        });
                        divViewItems.find('.' + key).empty().append('<h4 class="text-capitalize">' +
                            key.replace('_', ' ') + '</h4>' + imageHtml);
                    } else {
                        var heading = '<h4>' + divViewItems.find('.' + key).find('h4').html() +
                            '</h4>'
                        divViewItems.find('.' + key).empty().append('<h4 class="text-capitalize">' +
                            key.replace('_', ' ') + '</h4>' + value);
                    }
                })
            })
        })
    </script>
@endpush
