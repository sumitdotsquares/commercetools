<div class="col-8"><a href="/"><img src="{{ asset('images/logo.png') }}" alt="icon" style="height: 35px; float: left; margin: 10px 20px 10px 10px;"></a></div>
<div class="col-4 align-text-bottom" style="text-align: right; font-size: 2em;">
    <a href="/checkout" style="text-decoration: none; color: inherit;">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="shopping-cart" class="svg-inline--fa fa-shopping-cart fa-w-16 " role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
            <path fill="currentColor" d="M528.12 301.319l47.273-208C578.806 78.301 567.391 64 551.99 64H159.208l-9.166-44.81C147.758 8.021 137.93 0 126.529 0H24C10.745 0 0 10.745 0 24v16c0 13.255 10.745 24 24 24h69.883l70.248 343.435C147.325 417.1 136 435.222 136 456c0 30.928 25.072 56 56 56s56-25.072 56-56c0-15.674-6.447-29.835-16.824-40h209.647C430.447 426.165 424 440.326 424 456c0 30.928 25.072 56 56 56s56-25.072 56-56c0-22.172-12.888-41.332-31.579-50.405l5.517-24.276c3.413-15.018-8.002-29.319-23.403-29.319H218.117l-6.545-32h293.145c11.206 0 20.92-7.754 23.403-18.681z"></path>
        </svg>
        <div class="cart_count">{{getCartItemCount()}}</div>
    </a>

    <a href="/user-dashboard" style="text-decoration: none; color: inherit;">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="36" height="36" fill="white">

            <title>Abstract user icon</title>

            <defs>
                <clipPath id="circular-border">
                    <circle cx="18" cy="18" r="17" />
                </clipPath>
                <clipPath id="avoid-antialiasing-bugs">
                    <rect width="100%" height="30" />
                </clipPath>
            </defs>

            <circle cx="18" cy="18" r="17" fill="currentColor" clip-path="url(#avoid-antialiasing-bugs)" />
            <circle cx="18" cy="13" r="7" />
            <circle cx="18" cy="31" r="12" clip-path="url(#circular-border)" />
        </svg>
    </a>
    @if(getCustomer())
    <a href="/logout" style="text-decoration: none; color: inherit;">
        <svg fill="currentColor" height="25px" width="25px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 384.971 384.971" xml:space="preserve">
            <g>
                <g id="Sign_Out">
                    <path d="M180.455,360.91H24.061V24.061h156.394c6.641,0,12.03-5.39,12.03-12.03s-5.39-12.03-12.03-12.03H12.03
			C5.39,0.001,0,5.39,0,12.031V372.94c0,6.641,5.39,12.03,12.03,12.03h168.424c6.641,0,12.03-5.39,12.03-12.03
			C192.485,366.299,187.095,360.91,180.455,360.91z" />
                    <path d="M381.481,184.088l-83.009-84.2c-4.704-4.752-12.319-4.74-17.011,0c-4.704,4.74-4.704,12.439,0,17.179l62.558,63.46H96.279
			c-6.641,0-12.03,5.438-12.03,12.151c0,6.713,5.39,12.151,12.03,12.151h247.74l-62.558,63.46c-4.704,4.752-4.704,12.439,0,17.179
			c4.704,4.752,12.319,4.752,17.011,0l82.997-84.2C386.113,196.588,386.161,188.756,381.481,184.088z" />
                </g>
            </g>
        </svg>
    </a>
    @endif
</div>