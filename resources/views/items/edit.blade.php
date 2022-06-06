@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="title">{{ __('Create Item') }}</div>
                        <div class="addbutton">
                            <a class="btn btn-success" href="{{ route('items.index') }}">Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('items.update', $item->id) }}" method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="form-row">
                                <div class="col">
                                    <label for="item_name">Item's Name</label>
                                    <input type="text" class="form-control @error('item_name') is-invalid @enderror" name="item_name" id="item_name"
                                        placeholder="Item's Name" value="{{ $item->item_name }}">
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                    <div class="invalid-feedback">
                                        Please add item name.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row mt-3">
                                <div class="editor w-100">
                                    <label for="descriptions">Descriptions</label>
                                    <textarea class="ckeditor form-control @error('descriptions') is-invalid @enderror" id="descriptions" name="descriptions" required>{{ $item->descriptions }}</textarea>
                                </div>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please add descriptions.
                                </div>
                            </div>
                            <div class="form-row mt-3">
                                <label for="manufacture_date">Manufacture Date</label>
                                <input type="date" class="form-control @error('manufacture_date') is-invalid @enderror" name="manufacture_date" id="manufacture_date" value="{{ $item->manufacture_date }}" required>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please select manufacture date.
                                </div>
                            </div>
                            <div class="form-row my-3">
                                <label for="validatedImagesFile" class="form-label">Images</label>
                                <input type="file" class="@error('images') is-invalid @enderror form-control" id="validatedImagesFile" name="images[]" value="{{ $item->images }}" multiple>
                            </div>
                            <h5>Selected Images:</h5>
                            <div>
                                @foreach (json_decode($item->images) as $image)
                                    <img src="{{ asset('image/' . $image) }}" width="100" height="auto" class="m-1"/>
                                @endforeach
                            </div>
                            <button class="btn btn-primary mt-4" type="submit">Submit Item</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('extrascript')
    <script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })(jQuery($));
    </script>
@endpush
