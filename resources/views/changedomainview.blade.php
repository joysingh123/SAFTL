@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Domain Data</div>
                <div class="card-body">
                    <form class="form-inline" id="filterchangedomainform">
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                                <label for="domain">Domain: </label>
                                <input type="text" class="form-control" id="c_domain">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                            <label for="country">Country: </label>
                            <input type="text" class="form-control" id="c_country">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                            <label for="mx_record">Mx Record: </label>
                            <input type="text" class="form-control" id="mx_record">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                            <label for="city">City: </label>
                            <input type="text" class="form-control" id="c_city">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                                <label for="industry">Industry: </label>
                                <input type="text" class="form-control" id="c_industry">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                                <label for="employee_size">Employee Size: </label>
                                <input type="text" class="form-control" id="c_employee_size">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                                <label for="employee_count">Employee Count: </label>
                                <input type="text" class="form-control" id="c_employee_count">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                                <label for="company_type">Company Type: </label>
                                <input type="text" class="form-control" id="c_company_type">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                                <label for="department"></label>
                                <button type="submit" class="btn btn-default">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <div class="card" id="filterdomain">
                <div class="card-header">Filtered Domain</div>
                <div class="card-body"></div>
            </div>
            <div id="json_data" style="display: none">
                
            </div>
        </div>
    </div>
</div>
@endsection