@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Upload Video</div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('video.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="video" class="form-label">Choose Video</label>
                            <input type="file" class="form-control" id="video" name="video" accept="video/*" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="title" class="form-label">Video Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload Video</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection