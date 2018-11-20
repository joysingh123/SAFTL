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
                            <div class="autocomplete">
                                <input id="c_domain" type="text" placeholder="Domain" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="autocomplete">
                                <input id="c_country" type="text" placeholder="Country" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <select class="form-control" id='mx_record' >
                                <option value="">MX Record</option>
                                <option value="0">False</option>
                                <option value="1">True</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="autocomplete">
                                <input id="c_city" type="text" placeholder="City" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;">
                            <div class="autocomplete">
                                <input id="c_industry" type="text" placeholder="Industry" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;margin-top: 10px;">
                            <div class="autocomplete">
                                <input id="c_employee_size" type="text" placeholder="Employee Size" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;margin-top: 10px;">
                            <div class="autocomplete">
                                <input id="c_employee_count" type="text" placeholder="Employee Count" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;margin-top: 10px;">
                            <div class="autocomplete">
                                <input id="c_company_type" type="text" placeholder="Company Type" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 10px;margin-top: 10px;">
                            <div class="ui-widget">
                                <label for="department"></label>
                                <button type="submit" class="btn btn-default">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <div class="card" id="filterdomain"></div>
        </div>
    </div>
    <script type="text/javascript">
    var country = <?php echo $seed_data['country']; ?>
    </script>
    <script type="text/javascript">
    var industry = <?php echo $seed_data['industry']; ?>
    </script>
    <script type="text/javascript">
    var employee_size = <?php echo $seed_data['employeesize']; ?>
    </script>
</div>
@endsection