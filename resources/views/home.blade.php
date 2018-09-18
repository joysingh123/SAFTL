@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    
                    @role('Admin')
                    <div class="container">
                        <h2>Stats</h2>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Table</th>
                                    <th>Total</th>
                                    <th>Processed</th>
                                    <th>Not Processed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Companies With Domain</td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['companies_data']['total']); ?></td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['companies_data']['processed']); ?></td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['companies_data']['not_processed']); ?></td>
                                </tr>
                                <tr>
                                    <td>Companies Without Domain</td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['cwd_data']['total']); ?></td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['cwd_data']['processed']); ?></td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['cwd_data']['not_processed']); ?></td>
                                </tr>
                                <tr>
                                    <td>Contacts</td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['contacts_stats']['total']); ?></td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['contacts_stats']['processed']); ?></td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['contacts_stats']['not_processed']); ?></td>
                                </tr>
                                <tr>
                                    <td>Available Email</td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['available_email']['total']); ?></td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['available_email']['processed']); ?></td>
                                    <td><?php echo \App\Helpers\UtilString::IND_money_format($data['available_email']['not_processed']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <br>
                    @endrole
                    @role('Admin')
                    <div class="links">
                        <a href="/importcompaniesdata">Companies With Domain Import</a>
                    </div>
                    @endrole
                    <div class="links">
                        <a href="/importcontactdata">Contact Import</a>
                    </div>
                    @role('Admin')
                    <div class="links">
                        <a href="/importemaildata">Email Import For Format Generation</a>
                    </div>
                    <div class="links">
                        <a href="/importbounceemaildata">Import Bounce Email</a>
                    </div>

                    <div class="links">
                        <a href="/contactcompanymatch">Matched Contact</a>
                    </div>
                    <div class="links">
                        <a href="/makeemailformat">Generate Email Formate</a>
                    </div>
                    <div class="links">
                        <a href="/createemail">Create Email</a>
                    </div>
                    @endrole
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
