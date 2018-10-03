@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center">
        Email Data Import
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

    <form action="{{ route('importemaildatadump') }}" method="POST" enctype="multipart/form-data">
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
                    Email,
                    Full Name,
                    First Name,
                    Last Name,
                    Company Name,
                    Industry,
                    Country, 
                    Job Title] .
                </span>
            </li>
            <li>Excel Sheet should not have more then one sheet .</li>
            <li>Excel Sheet should have contains max 5,000 records.</li>
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
                    <th>Duplicate</th>
                    <th>Duplicate In Sheet</th>
                    <th>Email Found Invalid</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$stats_data['inserted']}}</td>
                    <td>{{$stats_data['duplicate']}}</td>
                    <td>{{$stats_data['duplicate_in_sheet']}}</td>
                    <td>{{$stats_data['invalid_email']}}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if(count($stats_data['emails_not_load']) > 0)
    <br>
    <br>
    <div class="container">
        <h2>Email Not Loaded</h2>         
        <table class="table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Company Name</th>
                    <th>domain</th>
                    <th>Country</th>
                    <th>Job Title</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats_data['emails_not_load'] AS $enl)
                <tr>
                    <td>{{$enl['email']}}</td>
                    <td>{{$enl['full_name']}}</td>
                    <td>{{$enl['first_name']}}</td>
                    <td>{{$enl['last_name']}}</td>
                    <td>{{$enl['company_name']}}</td>
                    <td>{{$enl['domain']}}</td>
                    <td>{{$enl['country']}}</td>
                    <td>{{$enl['job_title']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endif
</div>
@endsection