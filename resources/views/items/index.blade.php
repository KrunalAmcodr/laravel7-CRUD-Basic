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
                                <p>{{ $message }}</p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (isset($items_array) && !empty($items_array) && count($items_array) > 0)
                            <table class="table table-hover">
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
                                        <td>{{ $item->manufacture_date }}</td>
                                        <td>
                                            @foreach (json_decode($item->images) as $image)
                                                <img src="{{ asset('image/' . $image) }}" width="100" height="auto" class="m-1"/>
                                            @endforeach
                                        </td>
                                        <td>
                                            <form action="{{ route('items.destroy',$item->id) }}" method="POST">
                                                <a href="{{ route('items.edit', $item->id) }}" class="btn btn-info" role="button">Edit</a>
                                                <a href="{{ route('items.show', $item->id) }}" class="btn btn-primary" role="button">View</a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
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
            $('#loginAlert').alert('close');
            $('#successMessageAlert').alert('close');
        }, 2500);
    </script>
@endpush
