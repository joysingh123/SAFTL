@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Extract Data</div>
                <div class="card-body">
                    <form class="form-inline" id="filterform">
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                                <label for="email">Country: </label>
                                <input type="text" class="form-control" id="country">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                            <label for="city">City: </label>
                            <input type="text" class="form-control" id="city">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                            <label for="industry">Industry: </label>
                            <input type="text" class="form-control" id="industry">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                            <label for="department">Department: </label>
                            <input type="text" class="form-control" id="department">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                            <label for="title_level">Title Level: </label>
                            <input type="text" class="form-control" id="title_level">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                            <label for="employee_size">Employee Size: </label>
                            <input type="text" class="form-control" id="employee_size">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="ui-widget">
                            <label for="tag">Tag: </label>
                            <input type="text" class="form-control" id="tag">
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-right: 10px;">
                            <label for="only_email">Only Email: </label>
                            <input type="checkbox" class="form-control" id="only_email">
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <label for="email_valid">Valid Email: </label>
                            <input type="checkbox" class="form-control" id="email_valid">
                        </div>

                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                </div>
            </div>
            <br>
            <div class="card" id="filterdata">
                <div class="card-header">Filter Data Result</div>
                <div class="card-body"></div>
            </div>
        </div>
    </div>
</div>
@endsection
