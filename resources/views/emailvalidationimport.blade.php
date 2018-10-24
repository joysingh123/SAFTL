@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center">
        Email Import For Verification
    </h2>

    @if ( Session::has('success') )
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
        </button>
        <strong>{{ Session::get('success') }}</strong>
    </div>
    @endif

    @if ( Session::has('error') )
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
        </button>
        <strong>{{ Session::get('error') }}</strong>
    </div>
    @endif

    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
        <div>
            @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif

    <form action="{{ route('importemailvalidationdump') }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        Choose your xls File : <input type="file" name="file" class="form-control">

        <input type="submit" class="btn btn-primary btn-lg" style="margin-top: 3%">
    </form>

    @if(!Session::has('stats_data'))
    <br>
    <div class="container">
        <h2>Instruction</h2>         
        <ul>
            <li>Excel Sheet Should have column 
                <span class="excel-column">[
                    Email
                    ] .
                </span>
            </li>
            <li>Excel Sheet should not have more then one sheet .</li>
            <!--<li>Excel Sheet should have contains max 5,000 records.</li>-->
        </ul>
    </div>
    @endif

    @if(Session::has('stats_data'))

    @php
    $stats_data = Session::get('stats_data')
    @endphp
    <br>
    <br>
    <div class="container">
        <h2>Email Stats</h2>         
        <table class="table">
            <thead>
                <tr>
                    <th>New Inserted</th>
                    <th>Duplicate In Sheet</th>
                    <th>Already Exist</th>
                    <th>Email Found Invalid</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$stats_data['inserted']}}</td>
                    <td>{{$stats_data['duplicate_in_sheet']}}</td>
                    <td>{{$stats_data['already_exist']}}</td>
                    <td>{{$stats_data['invalid_email']}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection