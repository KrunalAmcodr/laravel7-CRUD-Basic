@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="title">{{ __('Dashboard') }}</div>
                        <div class="addbutton">
                            <button class="btn btn-success" onclick="addPost()">Create New Items</button>
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

                        @if (isset($items_array) && !empty($items_array) && count($items_array) > 0)
                            <table class="table table-hover itemtable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>item Name</th>
                                        <th>Descriptions</th>
                                        <th>Date</th>
                                        <th>Images</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 0;
                                    @endphp
                                    @foreach ($items_array as $item)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $item->item_name }}</td>
                                            <td>{!! substr($item->descriptions, 0, 100) !!}</td>
                                            <td>{{ date('M d, Y', strtotime($item->manufacture_date)) }}</td>
                                            <td>
                                                @foreach (json_decode($item->images) as $image)
                                                    <img src="{{ asset('image/' . str_replace("\"","",$image)) }}" width="100" height="auto"
                                                        class="m-1" />
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ route('items.edit', $item->id) }}" class="btn btn-info"
                                                    role="button">Edit</a>
                                                <a href="{{ route('items.show', $item->id) }}" class="btn btn-primary"
                                                    role="button">View</a>
                                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                                    data-target="#model-item-{{ $item->id }}">
                                                    Delete
                                                </button>
                                                <!-- Modal -->
                                                <div class="modal fade" id="model-item-{{ $item->id }}"
                                                    tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLongTitle">Delete
                                                                    Confirmation</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you want to delete {{ $item->item_name }}?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" id="close-modelbtn"
                                                                    class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-danger delete-item"
                                                                    data-itemid="{{ $item->id }}">Delete</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-warning" role="alert">
                                {{ __('Not Found') }}
                            </div>
                        @endif
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
                        <input type="hidden" name="ajaxitem_id" id="ajaxitem_id">
                        <div class="form-row">
                            <div class="col">
                                <label for="item_name">Item's Name</label>
                                <input type="text" class="form-control @error('item_name') is-invalid @enderror"
                                    name="item_name" id="item_name" placeholder="Item's Name"
                                    value="{{ old('item_name') }}" required>
                            </div>
                            @error('item_name')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="form-row mt-3">
                            <div class="editor w-100">
                                <label for="descriptions">Descriptions</label>
                                <textarea class="ckeditor form-control @error('descriptions') is-invalid @enderror" id="descriptions"
                                    name="descriptions" required>{{ old('descriptions') }}</textarea>
                                @error('descriptions')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <label for="manufacture_date">Manufacture Date</label>
                            <input type='text' class="form-control" name="manufacture_date" id="manufacture_date"
                                placeholder="Select Manufacture Date"
                                @if ($errors->any()) value="{{ date('m/d/Y', strtotime(old('manufacture_date'))) }}" @endif
                                required>
                            @error('manufacture_date')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="form-row my-3">
                            <label for="itemImages" class="form-label">Images</label>
                            <input type="file" class="@error('images') is-invalid @enderror form-control" id="itemImages"
                                name="images[]" value="{{ old('images') }}" required multiple>
                            @error('images')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <button class="btn btn-primary mt-4" type="button" onclick="createPost()">Submit Item</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('extrascript')
    <script type="text/javascript">
        $('#manufacture_date').datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true,
        })

        function addPost() {
            $("#ajaxitem_id").val('');
            $('#ajaxitem-modal').modal('show');
        }

        function createPost() {
            var item_name = $('#item_name').val();
            var descriptions = CKEDITOR.instances.descriptions.getData();;
            var manufacture_date = $('#manufacture_date').val();
            var Images = [];
            var itemImages_length = $('#itemImages').get(0).files.length;
            let files = $('#itemImages')[0];
            for (var i = 0; i < itemImages_length; ++i) {
                Images.push(JSON.stringify($('#itemImages').get(0).files[i].name));
            }
            Images = JSON.stringify(Images)

            $.ajax({
                url: "{{ route('ajaxitems.store') }}",
                type: 'POST',
                data: {
                    item_name: item_name,
                    descriptions: descriptions,
                    manufacture_date: manufacture_date,
                    images: Images,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(response) {
                    console.log(response);
                    // $('#itemNameError').text(response.responseJSON.errors.item_name);
                    // $('#descriptionsError').text(response.responseJSON.errors.descriptions);
                    // $('#manufactureDateError').text(response.responseJSON.errors.manufacture_date);
                    // $('#ImagesError').text(response.responseJSON.errors.Images);
                }
            })

        }
    </script>
@endpush
