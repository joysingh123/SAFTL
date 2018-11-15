$(document).ready(function () {

    $("#filterdata").hide();
    $("#csvexportbutton").hide();

    $("#filterform").submit(function (event) {
        event.preventDefault();
        var country = $('#country').val();
        var city = $('#city').val();
        var industry = $('#industry').val();
        var department = $('#department').val();
        var title_level = $('#title_level').val();
        var employee_size = $('#employee_size').val();
        var only_email = $("#only_email").is(":checked");
        var valid_email = $("#email_valid").is(":checked");
//        console.log("only_email: "+only_email);
//        console.log("valid_email: "+valid_email);
        var tag = $('#tag').val();
//        console.log("country: "+country);
        if ((country != "" && city != "") && (industry != "" && department != "") && (title_level != "" && employee_size != "" && tag != "") && (only_email || valid_email)) {
            console.log(only_email);
            $("#filterdata > div.card-body").html("<h1>No, Filter Found</h1>");
            $("#filterdata").show();
        } else {
            var origin = window.location.origin;
            var url = window.location.origin + "/extractdata";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                method: "POST",
                url: url,
                data: {country: country, city: city, industry: industry, department: department, title_level: title_level, employee_size: employee_size, tag: tag, only_email: only_email, valid_email: valid_email}
            }).done(function (msg) {
//                console.log(msg);
                if (msg.status == "Fail") {
                    $("#filterdata > div.card-body").html("<h1>" + msg.message + "</h1>");
                    $("#filterdata").show();
                } else {
                    $("#filterdata > div.card-body").html();
                    $("#filterdata > div.card-header").html("Total Result: " + msg.total);
                    $("div#json_data").text(JSON.stringify(msg.data));
                    $("#csvexportbutton").show();
                    var table = "<table class='table'>";
                    table += "<thead>";
                    table += "<tr><th>#</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Domain</th><th>Email Validation Date</th><th>Email Status</th></tr>";
                    table += "</thead>";
                    table += "<tbody>";
                    for (var i = 0; i < msg.data.length; i++) {
                        table += "<tr>";
                        table += "<td>" + (i + 1) + "</td>";
                        table += "<td>" + msg.data[i].first_name + "</td>";
                        table += "<td>" + msg.data[i].last_name + "</td>";
                        table += "<td>" + msg.data[i].email + "</td>";
                        table += "<td>" + msg.data[i].domain + "</td>";
                        table += "<td>" + msg.data[i].email_validation_date + "</td>";
                        table += "<td>" + msg.data[i].email_status + "</td>";
                        table += "</tr>";
                    }
                    table += "</tbody>";
                    table += "</table>";
                    $("#filterdata > div.card-body").html(table);
                    $("#filterdata").show();
                }
            });
        }
    });


});
jQuery(document).ready(function () {
    var origin = window.location.origin;
    $("#country").autocomplete({
        source: origin + '/extractautocomplatedata/country',
        messages: {
            noResults: '',
            results: function () {}
        }
    });
    $("#city").autocomplete({
        source: origin + '/extractautocomplatedata/city',
        messages: {
            noResults: '',
            results: function () {}
        }
    });
    $("#industry").autocomplete({
        source: origin + '/extractautocomplatedata/industry',
        messages: {
            noResults: '',
            results: function () {}
        }
    });
    $("#department").autocomplete({
        source: origin + '/extractautocomplatedata/department',
        messages: {
            noResults: '',
            results: function () {}
        }
    });
    $("#title_level").autocomplete({
        source: origin + '/extractautocomplatedata/titlelevel',
        messages: {
            noResults: '',
            results: function () {}
        }
    });
    $("#employee_size").autocomplete({
        source: origin + '/extractautocomplatedata/employeesize',
        messages: {
            noResults: '',
            results: function () {}
        }
    });
    $("#tag").autocomplete({
        source: origin + '/extractautocomplatedata/tag',
        messages: {
            noResults: '',
            results: function () {}
        }
    });
    
    $("#c_industry").autocomplete({
        source: origin + '/extractautocompletedataforchangedomain/industry',
        messages: {
            noResults: '',
            results: function () {
            }
        }
    });
    
    $("#c_country").autocomplete({
        source: origin + '/extractautocompletedataforchangedomain/country',
        messages: {
            noResults: '',
            results: function () {
            }
        }
    });
    
    $("#c_employee_size").autocomplete({
        source: origin + '/extractautocompletedataforchangedomain/employeesize',
        messages: {
            noResults: '',
            results: function () {
            }
        }
    });
});

function download_csv() {
    var data = $("div#json_data").text();
    var csv = 'First Name,Last Name,Email,Domain,Email Validation Date,Email Status\n';
    var obj = JSON.parse(data);
    obj.forEach(function (row) {
        var data_array = [row.first_name, row.last_name, row.email, row.domain, row.email_validation_date, row.email_status];
        csv += data_array.join(',');
        csv += "\n";
    });
    var hiddenElement = document.createElement('a');
    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
    hiddenElement.target = '_blank';
    hiddenElement.download = 'data.csv';
    hiddenElement.click();
//    console.log(csv);
//    alert(data);
}

$(document).ready(function () {
    $("#filterdomain").hide();
    $("#filterchangedomainform").submit(function (event) {
        event.preventDefault();
        var domain = $('#c_domain').val();
        var country = $('#c_country').val();
        var mx_record = $('#mx_record').val();
        var city = $('#c_city').val();
        var industry = $('#c_industry').val();
        var employee_size = $('#c_employee_size').val();
        var employee_count = $('#c_employee_count').val();
        var company_type = $('#c_company_type').val();
        // var json_data = {domain: domain, country: country, mx_record: mx_record, city: city, industry: industry, employee_size: employee_size, employee_count: employee_count, company_type: company_type};
        // console.log(json_data);
        var origin = window.location.origin;
        var url = window.location.origin + "/changedomain";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            method: "POST",
            url: url,
            data: {domain: domain, country: country, mx_record: mx_record, city: city, industry: industry, employee_size: employee_size, employee_count: employee_count, company_type: company_type}
        }).done(function (msg) {
                console.log(msg);
                if (msg.status == "Fail") {
                    $("#filterdomain > div.card-body").html("<h1>" + msg.message + "</h1>");
                    $("#filterdomain").show();
                }else{
                    var table = "<table class='table'>";
                    table += "<thead>";
                    table += "<tr><th>#</th><th>Linkedin Id</th><th>Domain</th><th>Name</th><th>Type</th><th>E. Count</th><th>Industry</th><th>City</th><th>Country</th><th>MX-Record</th><th>E. Size</th><th>Edit</th></tr>";
                    table += "</thead>";
                    table += "<tbody>";
                    for (var i = 0; i < msg.data.length; i++) {
                        table += "<tr>";
                        table += "<td>" + (i + 1) + "</td>";
                        table += "<td>" + msg.data[i].linkedin_id + "</td>";
                        table += "<td>" + msg.data[i].company_domain + "</td>";
                        table += "<td>" + msg.data[i].company_name + "</td>";
                        table += "<td>" + msg.data[i].company_type + "</td>";
                        table += "<td>" + msg.data[i].employee_count_at_linkedin + "</td>";
                        table += "<td>" + msg.data[i].industry + "</td>";
                        table += "<td>" + msg.data[i].city + "</td>";
                        table += "<td>" + msg.data[i].country + "</td>";
                        table += "<td>" + msg.data[i].mx_record + "</td>";
                        table += "<td>" + msg.data[i].employee_size + "</td>";
                        table += "<td> <a href='/editcompany/" + msg.data[i].id + "' target='_blank'>Edit</a></td>";
                        table += "</tr>";
                    }
                    table += "</tbody>";
                    table += "</table>";
                    $("#filterdomain > div.card-body").html(table);
                    $("#filterdomain").show();
                }
        });
    });
});

function changeApprovalStatus(id){
    var txt;
    var r = confirm("Are You sure want to approve this domain?");
    if (r == true) {
        $.get( "/approvedomainforchange/"+id, function( data ) {
        $( ".result" ).html( data );
            window.location.reload();
        });
    } else {
        txt = "You pressed Cancel!";
    }
    return r;
}