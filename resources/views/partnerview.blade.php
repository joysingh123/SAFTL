@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center">
        Partner Import
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

    <form action="{{ route('importpartner') }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        
        Install Base:<select name="partner" class="form-control">
            <option value="">Select Your Partner</option>
            @foreach($partner_base AS $is)
            <option value="{{$is->id}}">{{$is->name}}</option>
            @endforeach
        </select><br>
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
                    Domain
                    ] .
                </span>
            </li>
            <li>Excel Sheet should not have more then one sheet .</li>
            <li>Excel Sheet should have contains max 20,000 records.</li>
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
        <h2>Domain Stats</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>New Inserted</th>
                    <th>Duplicate In Sheet</th>
                    <th>Invalid Domain</th> 
                    <th>Domain Already Exist</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$stats_data['inserted']}}</td>
                    <td>{{$stats_data['duplicate_in_sheet']}}</td>
                    <td>{{$stats_data['invalid_domain']}}</td>
                    <td>{{$stats_data['already_exist_in_db']}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if(count($stats_data['invalid_domain_array']) > 0)
    <br>
    <br>
    <div class="container">
        <h2>Invalid Domain</h2>         
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Domain</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats_data['invalid_domain_array'] AS $k=>$data)
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$data}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endif
</div>
@endsection