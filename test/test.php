<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<script>
    var settings = {
        "url": "https://api.staging.superpayments.com/v2/offers",
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json",
            "Referer": "https://www.staging.superpayments.com"
        },
        "data": JSON.stringify({
            "minorUnitAmount": 10000,
            "cart": {
                "id": "cart101",
                "items": [{
                        "name": "Im a product",
                        "quantity": 2,
                        "minorUnitAmount": 10000,
                        "url": "https://www.dev-site-2x6137.wixdev-sites.org/product-page/i-m-a-product-8"
                    },
                    {
                        "name": "Amazing boots",
                        "quantity": 3,
                        "minorUnitAmount": 10000,
                        "url": "https://www.merchant.com/product1.html"
                    }
                ]
            },
            "page": "Checkout",
            "output": "both",
            "test": true
        }),
    };

    $.ajax(settings).done(function(response) {
        console.log(response);
    });
</script>