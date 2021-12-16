<?php
session_start();

if (!isset($_SESSION['credentials'])){
    echo json_encode([
        'message' => "You're not authenticated!"
    ]);
    http_response_code(401);
    return; 
}
?>

<?php include_once('./partials/header.php') ?>


<div class="container">
    <div class="row">
        <h1 class="text-center mt-3">You are successfully authenticated on your Google Photos account!</h1>
        <h3 class="mt-5">To start downloading your photos, please click in the first link down below:</h3>
    </div>
    <div class="row mt-5">
        <span class="d-flex justify-content-center">
            <a class="btn btn-primary" id="fetchLinks">Start the downloading links process!</a>
        </span>
    </div>
</div>


<script>
    let baseUrl = "http://localhost:8000"
    document.getElementById('fetchLinks').addEventListener('click', ()=>{
        fetch(baseUrl + '/src/controllers/functions.php?startDownloadPhotos=1')
        .then(resp => {
            return resp.json()
        })
        .then(data => {
            console.log(data);
        })
    })
</script>

<?php include_once('./partials/footer.php') ?>