@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="title">{{ __('Dashboard') }}</div>
                        <div class="addbutton">
                            <a class="btn btn-success" href="{{ route('items.create') }}"> Create New Items</a>
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
                                                    <img src="{{ asset('image/' . $image) }}" width="100" height="auto"
                                                        class="m-1" />
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ route('items.edit', $item->id) }}" class="btn btn-info"
                                                    role="button">Edit</a>
                                                <a href="{{ route('items.show', $item->id) }}" class="btn btn-primary"
                                                    role="button">View</a>
                                                {{-- Normal Delete Method --}}
                                                {{-- <form action="{{ route('items.destroy',$item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form> --}}
                                                {{-- Ajax Delete Method --}}
                                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                                    data-target="#model-item-{{ $item->id }}">
                                                    Delete
                                                </button>
                                                <!-- Modal -->
                                                <div class="modal fade" id="model-item-{{ $item->id }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalCenterTitle"
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
                                                                <button type="button" id="close-modelbtn" class="btn btn-secondary"
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
@endsection

@push('extrascript')
    <script type="text/javascript">
        setTimeout(function() {
            // Closing the alert
            $('#successMessageAlert').alert('close');
        }, 2500);
        $(".delete-item").click(function() {
            // $('#close-modelbtn').click();
            var trbutton = $(this);
            var id = $(this).data("itemid");
            var token = $("meta[name='csrf-token']").attr("content");
            
            $('#model-item-'+id).modal('toggle');

            window.setTimeout(function(){
                trbutton.closest('tr').remove();
                $.ajax({
                    url: "items/" + id,
                    type: 'DELETE',
                    data: {
                        "id": id,
                        "_token": token,
                    },
                    success: function() {
    
                        $('.card-body').append('<div class="alert alert-success" id="successMessageAlert">Item deleted successfully <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span></button></div>');

                        if($('.itemtable tbody tr').length == false){
                            $('.itemtable').remove();
                            $('.card-body').append('<div class="alert alert-warning" role="alert">Not Found</div>')
                        }

                        setTimeout(function() {
                            // Closing the alert
                            $('#successMessageAlert').alert('close');
                        }, 2500);

                    }
                });
            }, 500);
        });
    </script>
@endpush
