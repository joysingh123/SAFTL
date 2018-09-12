@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center">
        Contact Import
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

    <form action="{{ route('importcontactdata') }}" method="POST" enctype="multipart/form-data">
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
                    Full Name, 
                    Title,
                    Company, 
                    Experience, 
                    Location, 
                    Industry, 
                    Profile link,
<!--                    Tag,
                    Title Level,
                    Department,-->
                    Company Url
                    ] .
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
        <h2>Conatct Stats</h2>
        <form action="{{ route('exportjunkcontactdata') }}" method="POST">
        {{ csrf_field() }}
        @foreach($stats_data['invalid_array'] AS $d)
            <input type="hidden" name='data[]' value="{{$d}}">
        @endforeach
        <input type="submit" value='Download Junk Excel' class="btn btn-primary btn-lg" style="margin-top: 3%">
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>New Inserted</th>
                    <th>Duplicate</th>
                    <th>Duplicate In Sheet</th>
                    <th>Junk Contact</th> 
                    <th>Company Id Not Exist</th>
                    <th>Name Found Invalid</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$stats_data['inserted']}}</td>
                    <td>{{$stats_data['duplicate']}}</td>
                    <td>{{$stats_data['duplicate_in_sheet']}}</td>
                    <td>{{$stats_data['invalid_record']}}</td>
                    <td>{{$stats_data['campaign_id_not_exist']}}</td>
                    <td>{{$stats_data['invalid_name']}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if(count($stats_data['invalid_array']) > 0)
    <br>
    <br>
    <div class="container">
        <h2>Junk Contact</h2>         
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>LinkedIn Id</th>
                    <th>Full Name</th>
                    <th>Job Title</th>
                    <th>Company</th> 
                    <th>Location</th>
                    <th>Experiance</th>
                    <th>Company Url</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats_data['invalid_array'] AS $k=>$data)
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$data->linkedin_id}}</td>
                    <td>{{$data->full_name}}</td>
                    <td>{{$data->title}}</td>
                    <td>{{$data->company}}</td>
                    <td>{{$data->location}}</td>
                    <td>{{$data->experience}}</td>
                    <td>{{$data->company_url}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endif
</div>
@endsection