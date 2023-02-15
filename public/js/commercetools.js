function checkUser(e) {
  $.ajax({
    type: "POST",
    url: "/customer",
    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
    data: {
      email: $("#email").val(),
      password: $("#password").val(),
    },
    success: function (data) {
      console.log(data);
      if (data != false) {
        // $("#checkout #customer_id").val(data.id);
        // $("#checkout #name").val(data.firstName + " " + data.lastName);
        // $("#checkout #address").val(data.addresses[0].streetName);
        // $("#checkout #city").val(data.addresses[0].city);
        // $(
        //   "#checkout #country option[value='" + data.addresses[0].country + "']"
        // ).prop("selected", true);
        location.reload(true);
      } else {
        $("#checkout #customer_id").val("");
        $("#checkout #name").val("");
        $("#checkout #address").val("");
        $("#checkout #city").val("");
        $("#checkout #country option[value='']").prop("selected", true);
      }
    },
  });
}

// function getOffer() {
//   const SP_BASE_URL = "https://api.staging.superpayments.com/v2";
//   const SP_API_KEY = "PSK_V6FrAxwm4T8lhnLwiPoM-xNSZnDKTYEUSLNme6v2";

//   var data = '{"cartId":"c372589a-25e9-4543-8a63-e390e9dc88eb","lineItem":[{"name":"Bag “Greyson“ Guess","quantity":1,"minorUnitAmount":14900,"url":"http://commercetools.24livehost.com"}]}';

//   console.log(SP_BASE_URL + "/offers");

//   $.ajax({
//     type: "POST",
//     url: SP_BASE_URL + "/offers",
//     headers: {
//       "Content-Type": "application/json",
//       Referer: "https://commercetools.24livehost.com/checkout",
//       "checkout-api-key": SP_API_KEY,
//     },
//     data: data,
//     success: function (data) {
//       console.log(data);
//     },
//   });
// }
// getOffer();
