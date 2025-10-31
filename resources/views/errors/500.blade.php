<?php

?>
<style>
    body {
        font-family: "Roboto", sans-serif;
        background-color: #f1f1f1;
    }

    .server-error-page .page-section {
        display: flex;
        justify-content: center;
        height: 100%;
        flex-direction: column;
        align-items: center;
    }

   .page-section .text-content {
        margin-top: -75px;
    }

   .page-section .heading {
        font-size: 110px;
        margin: 0;
        font-weight: 700;
        color: #3b933f;
        text-align: center;
    }

    .page-section .sub-heading {
        margin: 0;
        text-align: center;
        font-weight: 400;
        font-size: 45px;
    }
</style>

<section class="server-error-page">
    <div class="container">
        <div class="page-section">
            <div class="image">
                <img src="{{ asset ('images/server-error.png') }}" alt="500 Error">
            </div>
            <div class="text-content">
                <h3 class="heading">500</h3>
                <p class="sub-heading">Internal server error</p>
            </div>
        </div>
    </div>
</section>