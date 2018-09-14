$(document).ready(function () {

    $("#filterdata").hide();

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
        if (country != "" && city != "" && industry != "" && department != "") {
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
                data: {country: country, city: city, industry: industry, department: department,title_level:title_level,employee_size:employee_size,tag:tag,only_email:only_email,valid_email:valid_email}
            }).done(function (msg) {
//                console.log(msg);
                if(msg.status == "Fail"){
                    $("#filterdata > div.card-body").html("<h1>"+msg.message+"</h1>");
                    $("#filterdata").show();
                }else{
                    $("#filterdata > div.card-body").html();
                    $("#filterdata > div.card-header").html("Total Result: "+msg.total);
                    var table = "<table class='table'>";
                    table += "<thead>";
                    table += "<tr><th>#</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Domain</th><th>Email Validation Date</th><th>Email Status</th></tr>";
                    table += "</thead>";
                    table += "<tbody>";
                    for(var i=0;i < msg.data.length;i++){
                        table += "<tr>";
                        table += "<td>"+(i+1)+"</td>";
                        table += "<td>"+msg.data[i].first_name+"</td>";
                        table += "<td>"+msg.data[i].last_name+"</td>";
                        table += "<td>"+msg.data[i].email+"</td>";
                        table += "<td>"+msg.data[i].domain+"</td>";
                        table += "<td>"+msg.data[i].email_validation_date+"</td>";
                        table += "<td>"+msg.data[i].email_status+"</td>";
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
    $("#country").autocomplete({
        source: 'http://localhost:8000/extractautocomplatedata/country'
    });
    $("#city").autocomplete({
        source: 'http://localhost:8000/extractautocomplatedata/city'
    });
    $("#industry").autocomplete({
        source: 'http://localhost:8000/extractautocomplatedata/industry'
    });
    $("#department").autocomplete({
        source: 'http://localhost:8000/extractautocomplatedata/department'
    });
    $("#title_level").autocomplete({
        source: 'http://localhost:8000/extractautocomplatedata/titlelevel'
    });
    $("#employee_size").autocomplete({
        source: 'http://localhost:8000/extractautocomplatedata/employeesize'
    });
    $("#tag").autocomplete({
        source: 'http://localhost:8000/extractautocomplatedata/tag'
    });
});