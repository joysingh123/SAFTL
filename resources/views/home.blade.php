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
                    @endrole
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
