@extends('layouts.app')
@section('title', "User Profile")
@section("content")
    <ul class="breadcrumb">
        <li><a href="{{url("/")}}">Home</a></li>
        <li class="active">My Profile</li>
    </ul>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <img src="{{asset("img/".$user->photo)}}" alt=""
                         width="200" height="200">
                </div>

                <div style="margin-top: 10px" class="form-group">
                    {{$user->name}}
                </div>
                <div class="form-group">
                    {{$user->email}}
                </div>
                <div class="form-group">
                    {{$user->phone}}
                </div>

                <div>
                    <a href="{{url("settings")}}" class="btn btn-default">Edit</a>
                </div>
            </div>
            <div class="card col-md-8">
                @if(session('info'))
                    <div class="alert alert-info">
                        {{session('info')}}
                    </div>
                @endif
                <div class="card-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home">Jobs Posted</a></li>
                        <li><a data-toggle="tab" href="#menu3">Pending Bids</a></li>
                        <li><a data-toggle="tab" href="#menu1">Won Bids</a></li>
                        <li><a data-toggle="tab" href="#menu2">Lost Bids</a></li>
                        <li><a data-toggle="tab" href="#transactions">Transactions</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
                            <div class="header"> Jobs you have posted </div>

                            <hr>
                            @if(count($user->jobs) == 0)
                                <div class="alert alert-info">You have not posted any job</div>
                            @endif
                            @foreach($user->jobs as $job)

                                <div class="job">
                                    <a href="{{url('job/'.$job->id)}}">
                                        <div class="job-name">
                                            {{$job->name}}
                                        </div>
                                        <div class="job-body col-md-6">
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
                                        </div>
                                    </a>


                                    <div class="col-md-3">
                                        @if(!is_null($job->winner_id))

                                        @else
                                            @if(is_null($job->merchant_request_id))
                                                <div class="alert alert-danger">Transaction failed
                                                    <a href="{{url('retrytransaction/'.$job->id)}}" >Retry transaction</a>
                                                </div>

                                            @endif
                                            <a href="#" class="btn btn-danger">Cancel Job</a>
                                        @endif
                                    </div>
                                </div>

                                <hr>
                            @endforeach
                        </div>
                        <div id="menu3" class="tab-pane fade">
                            <div class="header"> Jobs you have bid for</div>
                            <hr>
                            @if(count(array_filter(array_map(create_function('$job', 'if(($job["winner_id"] == null)) return $job;'), $user->lostbidjobs->toArray()))) == 0)
                                <div class="alert alert-info">You have no pending bids</div>
                            @endif
                            @foreach($user->lostbidjobs as $job)
                                @if($job->user_id == $user->id || $job->winner_id != null)
                                    @continue
                                @endif

                                @foreach(DB::table('bids')->where('user_id', $user->id)
                                ->where('job_id', $job->id)->get() as $bid)
                                        <div class="job">
                                            <div class="job-body col-md-8">
                                                <div class="job-name">
                                                    {{$job->name}}
                                                </div>
                                                <div class="job-description">
                                                    {{$job->description}}
                                                </div>
                                                <div>
                                                    {{$job->location}}
                                                </div>

                                            </div>
                                            <div class="col-md-3">
                                                <div>
                                                    <label for="">Ksh</label>
                                                    <span class="job-price">
                                                {{$job->price}}
                                             </span>
                                                </div>

                                                <div>
                                                    <div>Bid amount:</div>
                                                    <label for="">Ksh</label>
                                                    <span class="job-price">
                                                {{$bid->price}}
                                             </span>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                @endforeach
                            @endforeach
                        </div>
                        <div id="menu1" class="tab-pane fade">
                            <div class="header"> Jobs you have taken</div>
                            <hr>
                            @if(count($user->wonbidjobs) == 0)
                                <div class="alert alert-info">You have not won any bid</div>
                            @endif
                            @foreach($user->wonbidjobs as $job)
                                <a href="{{url('job/'.$job->id)}}">
                                    <div class="job">
                                        <div class="job-body col-md-8">
                                            <div class="job-name">
                                                {{$job->name}}
                                            </div>
                                            <div class="job-description">
                                                {{$job->description}}
                                            </div>
                                            <div>
                                                {{$job->location}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div>
                                                <label for="">Ksh</label>
                                                <span class="job-price">
                                                {{$job->price}}
                                             </span>
                                            </div>
                                            <div>
                                                <div>Bid amount:</div>
                                                <label for="">Ksh</label>
                                                <span class="job-price">
                                                {{DB::table('bids')->where('user_id', $user->id)
                                                ->where('id', $job->winner_id)->first()->price}}
                                             </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <hr>
                            @endforeach
                        </div>
                        <div id="menu2" class="tab-pane fade">
                            <div class="header"> Jobs you bid and lost</div>
                            <hr>
                            @if(count(array_filter(array_map(create_function('$job', 'if(($job["winner_id"] != null)) return $job;'), $user->lostbidjobs->toArray()))) == 0)
                                <div class="alert alert-info">You have not lost any bid</div>
                            @endif
                            @foreach($user->lostbidjobs as $job)
                                @if($job->user_id == $user->id || $job->winner_id == null)
                                    @continue
                                @endif

                                    @foreach(DB::table('bids')->where('id', '!=', $job->winner_id)
                                    ->where('user_id', $user->id)
                                    ->where('job_id', $job->id)->get() as $bid)
                                        <div class="job">
                                            <div class="job-body col-md-8">
                                                <div class="job-name">
                                                    {{$job->name}}
                                                </div>
                                                <div class="job-description">
                                                    {{$job->description}}
                                                </div>
                                                <div>
                                                    {{$job->location}}
                                                </div>

                                            </div>
                                            <div class="col-md-3">
                                                <div>
                                                    <label for="">Ksh</label>
                                                    <span class="job-price">
                                                {{$job->price}}
                                             </span>
                                                </div>

                                                <div>
                                                    <div>Bid amount:</div>
                                                    <label for="">Ksh</label>
                                                    <span class="job-price">
                                                {{$bid->price}}
                                             </span>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    @endforeach

                            @endforeach
                        </div>
                        <div id="transactions" class="tab-pane fade">
                            <div class="header">All Mpesa transactions</div>
                            <hr>
                            <h3>Pending Transactions</h3>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pending_transactions as $pending_transaction)
                                    <tr>
                                        <td>{{date("d/m/Y", strtotime($pending_transaction->date))}}</td>
                                        <td>{{$pending_transaction->amount}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <h3>Mpesa Transactions</h3>
                            <table class="table" id="table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Mpesa Receipt</th>
                                    <th>Phone</th>
                                    <th>Amount</th>
                                    <th>Transaction Description</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{date("d/m/Y", strtotime($transaction->transaction_date))}}</td>
                                        <td>{{$transaction->mpesa_receipt}}</td>
                                        <td>{{$transaction->phone}}</td>
                                        <td>{{$transaction->amount}}</td>
                                        <td>{{$transaction->result_desc}}</td>
                                        <td>{{$transaction->result_code == 0 ? "Success" : "Failed"}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                </div>


                </div>
            </div>
        </div>
    </div>
@endsection
@section('jscontent')
    <script src="https://www.gstatic.com/firebasejs/5.2.0/firebase.js"></script>
    <script>
        var config = {
            // TODO: Insert your firebase config here
        };
        firebase.initializeApp(config);

        const dbRefObject = firebase.database().ref('transactions');

        $(document).ready(function(){
            @php
                echo 'var subs = [';
                foreach ($pending_transactions as $pending_transaction){
                    echo "{\"merchant\": \"".$pending_transaction->merchant_request_id."\"},";
                }
                echo '];';
            @endphp

            subs.forEach(function(sub){
                dbRefObject.child(sub.merchant).on('value', function(snapshot){
                    if(snapshot.exists()){
                        document.location.reload();
                    }
                });
            });

            $('table').dataTable();
        });
    </script>
@endsection
