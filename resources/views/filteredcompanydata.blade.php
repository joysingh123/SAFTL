<div class="card-header">Total Result: {{$data->total()}}</div>
<div class="card-body">
    @if($data->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
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
                    <th>MX-Record</th>
                    <th>E. Size</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $k => $row)
                    <tr>
                        <td>{{ $k+1 }}</td>
                        <td>{{ $row->linkedin_id }}</td>
                        <td>{{ $row->company_domain }}</td>
                        <td>{{ $row->company_name }}</td>
                        <td>{{ $row->company_type }}</td>
                        <td>{{ $row->employee_count_at_linkedin }}</td>
                        <td>{{ $row->industry }}</td>
                        <td>{{ $row->city }}</td>
                        <td>{{ $row->country }}</td>
                        <td>{{ $row->mx_record }}</td>
                        <td>{{ $row->employee_size }}</td>
                        <td><a href='/editcompany/{{ $row->id }}' target='_blank'>Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {!! $data->links() !!}
    @else
    <h1>No, Result Found.</h1>
    @endif
</div>