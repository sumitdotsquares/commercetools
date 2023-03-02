function checkUser() {
  $.ajax({
    type: "POST",
    url: "/customer",
    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
    data: {
      email: $("#email").val(),
      password: $("#password").val(),
      name: $("#name").val(),
      address: $("#address").val(),
      city: $("#city").val(),
      country: $("#country").val(),
    },
    success: function (data) {
      console.log(data);
      if (data != 400) {
        $("#checkout #customer_id").val(data.id);
        $("#checkout #name").val(data.firstName + " " + data.lastName);
        $("#checkout #address").val(data.addresses[0].streetName);
        $("#checkout #city").val(data.addresses[0].city);
        $(
          "#checkout #country option[value='" + data.addresses[0].country + "']"
        ).prop("selected", true);
        location.reload(true);
      } else {
        $('.loginFail').show()
        $("#checkout #customer_id").val("");
        $("#checkout #name").val("");
        $("#checkout #address").val("");
        $("#checkout #city").val("");
        $("#checkout #country option[value='']").prop("selected", true);
      }
    },
  });
}

$(document).ready(function () {
  $(".loginCustomer").on("click", function () {
    checkUser();
  });
});
