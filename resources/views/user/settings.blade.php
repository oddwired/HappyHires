@extends('layouts.app')
@section('title', "User Profile")
@section("content")
    <ul class="breadcrumb">
        <li><a href="{{url("/")}}">Home</a></li>
        <li><a href="{{url("user")}}">My Profile</a></li>
        <li class="active">Settings</li>
    </ul>
    <div class="container">
        <div class="row">
            <div class="card col-md-offset-3 col-md-6">
                <div class="card-title">Settings</div>
                <div class="card-body">
                    <div>
                        <img src="{{url('img/'.$user->photo)}}" alt="" width="200" height="200">
                        <button class="btn btn-default" data-toggle="modal" data-target="#dpmodal">Change</button>
                        <div style="margin-top: 10px">

                        </div>

                        <div class="modal fade" id="dpmodal" role="dialog">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Change Display Photo</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{url("bid")}}" class="form">
                                            <div class="form-group">
                                                <label for="">Select a photo</label>
                                                <input id="file" type="file" name="photo" /><br>
                                                <div id="views"></div>
                                                <br>
                                                <button id="cropbutton" class="btn btn-default" type="button">Crop</button>
                                            </div>


                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-default" type="button" onclick="submitForm()">Change</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <form action="" method="post" class="form">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" class="form-control" name="name" value="{{$user->name}}" required>
                        </div>

                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="email" class="form-control" name="email" value="{{$user->email}}" readonly required>
                        </div>

                        <div class="form-group">
                            <label for="">Phone</label>
                            <input type="text" class="form-control" name="phone" value="{{$user->phone}}" required>
                        </div>

                        <div class="form-group">
                            <label for="">National ID</label>
                            <input type="text" class="form-control" name="nationalid" value="{{$user->national_id}}" required>
                        </div>
                        <button class="btn btn-primary">Save</button>
                    </form>
                </div>
                <input type="checkbox" onchange="window.location.href = '{{url('changetrainee')}}'"
                        {{$user->is_trainee == true ? "checked" : ""}}> I am a trainee
                @if($user->is_trainee)
                    <div class="header">Trainee</div>

                @endif
            </div>
        </div>
    </div>
@endsection
@section('jscontent')
    <script src="{{ asset('js/jquery.Jcrop.min.js') }}"></script>
    <script>
        var crop_max_width = 300;
        var crop_max_height = 300;
        var jcrop_api;
        var canvas;
        var context;
        var image;

        var prefsize;

        $.ajaxSetup({

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            }

        });

        $("#file").change(function() {
            loadImage(this);
        });

        function loadImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                canvas = null;
                reader.onload = function(e) {
                    image = new Image();
                    image.onload = validateImage;
                    image.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function dataURLtoBlob(dataURL) {
            var BASE64_MARKER = ';base64,';
            if (dataURL.indexOf(BASE64_MARKER) == -1) {
                var parts = dataURL.split(',');
                var contentType = parts[0].split(':')[1];
                var raw = decodeURIComponent(parts[1]);

                return new Blob([raw], {
                    type: contentType
                });
            }
            var parts = dataURL.split(BASE64_MARKER);
            var contentType = parts[0].split(':')[1];
            var raw = window.atob(parts[1]);
            var rawLength = raw.length;
            var uInt8Array = new Uint8Array(rawLength);
            for (var i = 0; i < rawLength; ++i) {
                uInt8Array[i] = raw.charCodeAt(i);
            }

            return new Blob([uInt8Array], {
                type: contentType
            });
        }

        function validateImage() {
            if (canvas != null) {
                image = new Image();
                image.onload = restartJcrop;
                image.src = canvas.toDataURL('image/png');
            } else restartJcrop();
        }

        function restartJcrop() {
            if (jcrop_api != null) {
                jcrop_api.destroy();
            }
            $("#views").empty();
            $("#views").append("<canvas id=\"canvas\">");
            canvas = $("#canvas")[0];
            context = canvas.getContext("2d");
            canvas.width = image.width;
            canvas.height = image.height;
            context.drawImage(image, 0, 0);
            $("#canvas").Jcrop({
                onSelect: selectcanvas,
                onRelease: clearcanvas,
                boxWidth: crop_max_width,
                boxHeight: crop_max_height,
                aspectRatio: 1
            }, function() {
                jcrop_api = this;
            });
            clearcanvas();
        }

        function clearcanvas() {
            prefsize = {
                x: 0,
                y: 0,
                w: canvas.width,
                h: canvas.height,
            };
        }

        function selectcanvas(coords) {
            prefsize = {
                x: Math.round(coords.x),
                y: Math.round(coords.y),
                w: Math.round(coords.w),
                h: Math.round(coords.h)
            };
        }

        function applyCrop() {
            canvas.width = prefsize.w;
            canvas.height = prefsize.h;
            context.drawImage(image, prefsize.x, prefsize.y, prefsize.w, prefsize.h, 0, 0, canvas.width, canvas.height);
            validateImage();
        }

        $("#cropbutton").click(function(e) {
            applyCrop();
        });

        function submitForm() {

            var blob = dataURLtoBlob(canvas.toDataURL('image/png'));
            var formData = new FormData();
            var file = new File([blob], "cropped.png");
            formData.append("cropped", file);
            $.ajax({
                url: "{{url('changephoto')}}",
                type: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    //alert("Success");
                },
                error: function(data) {
                    alert("Error");
                },
                complete: function(data) {
                    location.reload();
                }
            });

        }

    </script>
@endsection