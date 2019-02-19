@extends('layouts.app')
@section("title", "Home")
@section("content")
    <div class="container">
        <div class="card card-body col-md-offset-2 col-md-8">
            <div class="card-title">Browse Jobs</div>
            <hr>

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
            @if(DB::table('jobs')->whereNull('winner_id')->count() == 0)
                <div class="alert alert-info">There are no available jobs at the moment.</div>
            @endif
            @foreach($jobs as $job)
                @if(!is_null($job->winner_id))
                    @continue
                @endif
                <a href="{{url('job/'.$job->id)}}">
                    <div class="job">
                        <div class="job-name">
                            {{$job->name}}
                        </div>
                        <div class="job-body col-md-8">
                            <div class="job-description">
                                {{$job->description}}
                            </div>
                            <div>
                                {{$job->location}}
                            </div>

                        </div>
                        <div class="col-md-3">
                            <label for="">Ksh</label>
                            <span class="job-price">
                                                {{$job->price}}
                                             </span>

                            <div style="margin: 5px">
                                {{$job->bids->count()}} Bids
                            </div>

                            @if(!is_null(Auth::guard("user")->user()) && $job->user_id == Auth::guard("user")->id())

                            @else
                                <div>
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#modal{{$job->id}}">Bid</button>
                                </div>
                            @endif


                        </div>
                    </div>
                </a>

                    <div class="modal fade" id="modal{{$job->id}}" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">{{$job->name}}</h4>
                                </div>
                                <div class="modal-body">
                                    <form action="{{url("bid")}}" class="form">
                                        <input type="hidden" name="jobid" value="{{$job->id}}">
                                        <div class="input-group">
                                            <label for="">Enter your bid</label>
                                            <span class="input-group-addon">KSH</span>
                                            <input type="number" step=".01" min="1" name="offer" class="form-control" required>
                                            <div class="input-group-btn"><button class="btn btn-primary">Bid</button></div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>

                        </div>
                    </div>


                    <hr>
            @endforeach
        </div>
    </div>
@endsection