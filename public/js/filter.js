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
        var mx_record = $('#mx_record :selected').val();
        var city = $('#c_city').val();
        var industry = $('#c_industry').val();
        var employee_size = $('#c_employee_size').val();
        var employee_count = $('#c_employee_count').val();
        var company_type = $('#c_company_type').val();
        var url = window.location.origin + "/filtercompanydata?domain="+domain+"&country="+country+"&mx_record="+mx_record+"&city="+city+"&industry="+industry+"&employee_size="+employee_size+"&employee_count="+employee_count+"&company_type="+company_type;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
//        $.ajax({url: url}).done(function (data) {
//            $('#filterdomain').html(data);
//            $("#filterdomain").show();
//        });
        $.ajax({
            url: url,
            cache: false,
            beforeSend: function(){
                $('.loading').show();
            },
            complete: function(){
                $('.loading').hide();
            },
            success: function(data){
                $('#filterdomain').html(data);
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

$(document).ready(function(){
    $(document).on('click', '.pagination a', function(event){
        event.preventDefault(); 
        var page = $(this).attr('href').split('page=')[1];
        fetch_data(page);
    });

    function fetch_data(page){
        var domain = $('#c_domain').val();
        var country = $('#c_country').val();
        var mx_record = $('#mx_record :selected').val();
        var city = $('#c_city').val();
        var industry = $('#c_industry').val();
        var employee_size = $('#c_employee_size').val();
        var employee_count = $('#c_employee_count').val();
        var company_type = $('#c_company_type').val();
        var url = window.location.origin + "/filtercompanydata?page="+page+"&domain="+domain+"&country="+country+"&mx_record="+mx_record+"&city="+city+"&industry="+industry+"&employee_size="+employee_size+"&employee_count="+employee_count+"&company_type="+company_type;
        console.log(url);
//        $.ajax({
//            url:url,
//            success:function(data){
//                $('#filterdomain').html(data);
//                $("#filterdomain").show();
//            }
//            
//        });
        $.ajax({
            url: url,
            cache: false,
            beforeSend: function(){
                $('.loading').show();
            },
            complete: function(){
                $('.loading').hide();
            },
            success: function(data){
                $('#filterdomain').html(data);
                $("#filterdomain").show();
            }
        });
    }
});


function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
  });
}

/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
autocomplete(document.getElementById("c_country"), country);
autocomplete(document.getElementById("c_industry"), industry);
autocomplete(document.getElementById("c_employee_size"), employee_size);