@extends('layouts.app')

@section('content')
<div class="container">
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
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Domain Approval</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Linkedin Id</th>
                                <th>Domain</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>E. Count</th>
                                <th>Industry</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>E. Size</th>
                                <th>Approval Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($companies->count() > 0)
                            @foreach($companies AS $company)
                            <tr>
                                <td>{{$company->id}}</td>
                                <td>{{$company->linkedin_id}}</td>
                                <td>{{$company->company_domain}}</td>
                                <td>{{$company->company_name}}</td>
                                <td>{{$company->company_type}}</td>
                                <td>{{$company->employee_count_at_linkedin}}</td>
                                <td>{{$company->industry}}</td>
                                <td>{{$company->city}}</td>
                                <td>{{$company->country}}</td>
                                <td>{{$company->employee_size}}</td>
                                <td>
                                    @if($company->approve)
                                      {{ __('Approved') }}
                                    @else
                                        <a href ="#" onclick="changeApprovalStatus('<?php echo $company->id; ?>')">Not Approved</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
