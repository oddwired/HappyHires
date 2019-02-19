@extends('layouts.base')
@section('title', "User Login")
@section("content")
    <div class="container">
        <div class="card col-md-offset-3 col-md-6">
            <div class="card-title">Login</div>
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
                <form action="" class="form" method="post">
                    {{csrf_field()}}
                    @if(session('re'))
                        <input type="hidden" name="re" value="{{session('re')}}">
                    @endif
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="text" class="form-control" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <button class="btn btn-primary">Login</button>
                </form>

                    <div style="margin-top: 10px">Don't have an account yet?
                        Register
                        <a href="{{url('register')}}">here</a>
                    </div>
            </div>
        </div>
    </div>
@endsection