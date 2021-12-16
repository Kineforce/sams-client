<?php

require '../../vendor/autoload.php';
set_time_limit(0);

session_start();

if (!isset($_SESSION['credentials'])){
    echo json_encode([
        'message' => "You're not authenticated!"
    ]);
    http_response_code(401);
    return; 
}

use Google\Photos\Library\V1\PhotosLibraryClient;

if (isset($_GET["startDownloadPhotos"])){
    try {
        // Set up the Photos Library Client that interacts with the API
        $photosLibraryClient = new PhotosLibraryClient(["credentials" => $_SESSION["credentials"]]);
    
        // Array of links
        $lh3Links = []; 
    
        // Current page
        $currPage = 1;
    
        $pagedResponse = $photosLibraryClient->listMediaItems([
            'pageSize' => 100
        ]);
    
        foreach ($pagedResponse->iteratePages() as $page) {
            foreach ($page as $element) {
                array_push($lh3Links, [
                    "baseUrl" => $element->getBaseUrl(),
                    "filename" => $element->getFilename()
                ]);            
            };
            break;       
        }
    
        $localLh3Links = json_decode(file_get_contents("../assets/links.json"), true);

        foreach($lh3Links as $link){
            array_push($lh3Links, [
                "baseUrl" => $link["baseUrl"],
                "filename" => $link["filename"]
            ]);   
        }
        
        // After iterate all elements, save the array in a .json file
        file_put_contents("../assets/links.json", json_encode($lh3Links)); 
        
        echo json_encode([
            "message" => "Os links foram salvos com sucesso!",
            "qtdLinksBaixados" => count($lh3Links)
        ]);

        // Call download method
        downloadMedia(count($lh3Links));
        return;

    } catch (\Google\ApiCore\ValidationException $e) {
        echo json_encode($e);
        return;
    } catch (Exception $e) {
        echo json_encode($e);
        return;
    }
}

function downloadMedia($count){

    if (!$count){
        echo json_encode([
            "message" => "Não foram encontradas midias disponíveis no seu Google Photos!",
            "msgColor" => "red"
        ]);
        return;
    }

    // Get downloaded links
    $localLh3Links = json_decode(file_get_contents("../assets/links.json"), true);

    // Check folder to save media
    $localStorage = file_exists('../assets/googlePhotosMedia');

    if ($localStorage){
        foreach($localLh3Links as $link){
            $media = file_get_contents($link['baseUrl']);
            file_put_contents('../assets/googlePhotosMedia/' . $link["filename"] , $media);
        }
    } else {
        echo json_encode([
            "message" => "Houve um erro ao baixar as midias para o diretório do seu computador, o diretório não existe!",
            "msgColor" => "red"
        ]);
        return;
    }

    echo json_encode([
        "message" => "Foram baixadas $count midias disponíveis no seu Google Photos!",
        "msgColor" => "green"
    ]);
    return;
}