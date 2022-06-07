@extends('layouts.app')

@section('content')    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="title">{{ __('Dashboard') }}</div>
                        <div class="addbutton">
                            <a class="btn btn-success" href="{{ route('items.index') }}">Back</a>
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
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>item Name</th>
                                    <th>Descriptions</th>
                                    <th>Date</th>
                                    <th>Images</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{!! $item->descriptions, 0, 100 !!}</td>
                                    <td>{{ date('M d, Y', strtotime($item->manufacture_date)) }}</td>
                                    <td>
                                        @foreach (json_decode($item->images) as $image)
                                            <img src="{{ asset('image/' . $image) }}" width="100" height="auto" class="m-1"/>
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
