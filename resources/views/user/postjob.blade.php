@extends('layouts.app')
@section('title', "User Login")
@section("content")
    <div class="container">
        <div class="card col-md-offset-3 col-md-6">
            <div class="card-title">Post a job</div>
            <div class="card-body">
                @if(count($errors) > 0)
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger">
                            {{$error}}
                        </div>
                    @endforeach
                @endif

                    @if(session('info'))
                        <div class="alert alert-info">
                            {{session('info')}}
                        </div>
                    @endif
                <form action="" id="form" class="form" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="">Job Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="">Job Description</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="">Job Location</label>
                        <input name="location" class="form-control" placeholder="e.g Nakuru, Nairobi..." required>
                    </div>

                    <div class="form-group">
                        <label for="">Price</label>
                        <input type="number" class="form-control" step=".01" min="1" name="price" required>
                    </div>

                    <button class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
@endsection