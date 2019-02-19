@extends('layouts.app')
@section('title', "Job")
@section("content")
    <ul class="breadcrumb">
        <li><a href="{{url("/")}}">Home</a></li>
        <li><a href="{{url("user")}}">My Profile</a></li>
        <li class="active">job</li>
    </ul>
    <div class="container">
        <div class="row">
            <div class="card col-md-offset-2 col-md-8">
                <div class="card-title">{{$job->name}}</div>
                <div class="card-body">
                    @if(Auth::guard("user")->id() != $user->id && !is_null($job->winner_id))
                        @if(Auth::guard("user")->id() == $winner_user->id)
                            <div class="alert alert-info">You won the bid for Ksh {{$winner_bid->price}}</div>
                        @endif
                    @endif
                    <h4>Description:</h4>
                    <div style="margin-left: 10px">{{$job->description}}</div>
                    <div style="margin-top: 10px">
                        <h4>Job Location:</h4>
                        <div style="margin-left: 10px">{{$job->location}}</div>
                    </div>
                     @if(is_null($job->winner_id))
                            <div>
                                <h4>Price:</h4>
                                <div style="margin-left: 10px">Ksh {{$job->price}}</div>
                            </div>
                     @endif

                    @if((Auth::guard("user")->guest() || Auth::guard("user")->id() != $user->id))
                        <h4>Employer</h4>
                        <div style="margin-left: 10px">
                            <div>{{$user->name}}</div>
                            <div>{{$user->email}}</div>
                        </div>

                        @if(is_null($job->winner_id))
                                <h4>Enter your bid</h4>
                                <div class="col-md-6" style="margin-left: 10px">
                                    <form action="{{url("bid")}}" class="form">
                                        <input type="hidden" name="jobid" value="{{$job->id}}">
                                        <div class="input-group">
                                            <span class="input-group-addon">KSH</span>
                                            <input type="number" step=".01" min="1" name="offer" class="form-control" required>
                                            <div class="input-group-btn"><button class="btn btn-primary">Bid</button></div>
                                        </div>
                                    </form>
                                </div>
                        @endif


                    @elseif(Auth::guard("user")->id() == $user->id && is_null($job->winner_id))
                        <h4>Bids:</h4>
                        <div style="margin-left: 10px">
                            <table class="table">
                                <thead>

                                </thead>
                                <tbody>
                                @foreach($job->bids as $bid)
                                    <tr>
                                        <td>{{DB::table("users")->where('id', $bid->user_id)->first()->name}}</td>
                                        <td>{{$bid->price}}</td>
                                        <td>
                                            <a href="{{url("acceptbid/".$job->id."/".$bid->id)}}" class="btn btn-primary">Accept bid</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    @elseif(Auth::guard("user")->id() == $user->id && !is_null($job->winner_id))
                        <h4>Bid winner:</h4>
                        <a href="">
                            <div style="margin-left: 10px">
                                <div class="row">
                                    <div class="col-md-2">
                                        <img src="{{url("img/".$winner_user->photo)}}" alt="" width="90" height="90">
                                    </div>
                                    <div class="col-md-3">
                                        {{$winner_user->name}}
                                        <div>Bid Price: {{$winner_bid->price}}</div>
                                    </div>
                                </div>

                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection