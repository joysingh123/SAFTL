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
                <div class="card-header">{{ __('Edit Company') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('editcompany') }}" aria-label="{{ __('Register') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{$company->id}}" />
                        <div class="form-group row">
                            <label for="linkedin_id" class="col-md-2 col-form-label text-md-right">{{ __('Linkedin Id') }}</label>
                            <div class="col-md-4">
                                <input type="hidden" name="linkedin_id" value="{{$company->linkedin_id}}" />
                                <input id="linkedin_id" type="text" class="form-control" value="{{$company->linkedin_id}}" name="linkedin_id" value="{{ old('linkedin_id') }}" disabled>
                            </div>
                            <label for="company_name" class="col-md-2 col-form-label text-md-right">{{ __('Company Name') }}</label>
                            <div class="col-md-4">
                                <input id="company_name" type="text" class="form-control{{ $errors->has('company_name') ? ' is-invalid' : '' }}" value="{{$company->company_name}}" name="company_name" value="{{ old('company_name') }}">
                                @if ($errors->has('company_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('company_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="company_domain" class="col-md-2 col-form-label text-md-right">{{ __('Company Domain') }}</label>
                            <div class="col-md-4">
                                <input id="company_domain" type="text" class="form-control {{ $errors->has('company_domain') ? ' is-invalid' : '' }}" value="{{$company->company_domain}}" name="company_domain" value="{{ old('company_domain') }}">
                                @if ($errors->has('company_domain'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('company_domain') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <label for="linkedin_url" class="col-md-2 col-form-label text-md-right">{{ __('Linkedin Url') }}</label>

                            <div class="col-md-4">
                                <input id="linkedin_url" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{$company->linkedin_url}}" name="linkedin_url" value="{{ old('linkedin_url') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="employee_count" class="col-md-2 col-form-label text-md-right">{{ __('Employee Count') }}</label>
                            <div class="col-md-4">
                                <input id="employee_count" type="text" class="form-control{{ $errors->has('employee_count') ? ' is-invalid' : '' }}" value="{{$company->employee_count_at_linkedin}}" name="employee_count" value="{{ old('employee_count') }}">
                                @if ($errors->has('employee_count'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('employee_count') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <label for="industry" class="col-md-2 col-form-label text-md-right">{{ __('Industry') }}</label>
                            <div class="col-md-4">
                                <select class="form-control" name="industry" >
                                    @foreach($seed_data['industry'] AS $industry)
                                        <option value="{{$industry->Industry}}" {{($company->industry ==  $industry->Industry) ? 'selected' : '' }}>{{$industry->Industry}}</option>
                                    @endforeach
                                </select>
                                
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="city" class="col-md-2 col-form-label text-md-right">{{ __('City') }}</label>
                            <div class="col-md-4">
                                <input id="linkedin_id" type="text" class="form-control {{ $errors->has('city') ? ' is-invalid' : '' }}" value="{{$company->city}}" name="city" value="{{ old('city') }}">
                            </div>
                            <label for="postal_code" class="col-md-2 col-form-label text-md-right">{{ __('Postal Code') }}</label>
                            <div class="col-md-4">
                                <input id="linkedin_id" type="text" class="form-control{{ $errors->has('postal_code') ? ' is-invalid' : '' }}" value="{{$company->postal_code}}" name="postal_code" value="{{ old('postal_code') }}">
                                @if ($errors->has('postal_code'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('postal_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="employee_size" class="col-md-2 col-form-label text-md-right">{{ __('Employee Size') }}</label>
                            <div class="col-md-4">
                                <select class="form-control" name="employee_size" >
                                    @foreach($seed_data['employee_size'] AS $emp_size)
                                        <option value="{{$emp_size->employee_size}}" {{($company->employee_size ==  $emp_size->employee_size) ? 'selected' : '' }}>{{$emp_size->employee_size}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="country" class="col-md-2 col-form-label text-md-right">{{ __('Country') }}</label>
                            <div class="col-md-4">
                                <select class="form-control" name="country" >
                                    @foreach($seed_data['country'] AS $country)
                                        <option value="{{$country->country_name}}" {{($country->country_name ==  $company->country) ? 'selected' : '' }}>{{$country->country_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="website" class="col-md-2 col-form-label text-md-right">{{ __('Website') }}</label>
                            <div class="col-md-4">
                                <input id="linkedin_id" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{$company->website}}" name="website" value="{{ old('website') }}">
                            </div>
                            <label for="remark" class="col-md-2 col-form-label text-md-right">{{ __('Remark') }}</label>
                            <div class="col-md-4">
                                <textarea id="remark" class="form-control {{ $errors->has('remark') ? ' is-invalid' : '' }}" name="remark" rows="2" cols="44">{{ old('remark') }}</textarea>
                                @if ($errors->has('remark'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('remark') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="first_name" class="col-md-2 col-form-label text-md-right">{{ __('First Name') }}</label>
                            <div class="col-md-4">
                                <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}">
                                @if ($errors->has('first_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <label for="last_name" class="col-md-2 col-form-label text-md-right">{{ __('Last Name') }}</label>
                            <div class="col-md-4">
                                <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}">
                                @if ($errors->has('last_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="website" class="col-md-2 col-form-label text-md-right">{{ __('Email') }}</label>
                            <div class="col-md-4">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4 offset-md-2">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection