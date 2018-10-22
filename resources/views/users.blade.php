@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Users</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($users->count() > 0)
                            @foreach($users AS $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->email}}</td>
                                    <td><?php $role_data =  $user->getRoleNames(); echo $role_data[0]; ?></td>
                                    <td>{{$user->created_at}}</td>
                                    <td><a href="{{ URL::to('users/' . $user->id) }}">Edit</a></td>
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
