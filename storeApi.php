<?php
    header("Content-Type: application/json");

    //Defines all Languages supported by Steam with thei corresponding Steam Language Codes
    $languages = array(
        'BG' => 'bulgarian',
        'CZ' => 'czech',
        'DA' => 'danish',
        'NL' => 'dutch',
        'EN' => 'english',
        'FI' => 'finnish',
        'FR' => 'french',
        'EL' => 'greek',
        'DE' => 'german',
        'HU' => 'hungarian',
        'IT' => 'italian',
        'JP' => 'japanese',
        'KO' => 'korean',
        'NO' => 'norwegian',
        'PL' => 'polish',
        'PT' => 'portuguese',
        'BR' => 'brazilian',
        'RU' => 'russian',
        'RO' => 'romanian',
        'SC' => 'schinese',
        'ES' => 'spanish',
        'SV' => 'swedish',
        'TC' => 'tchinese',
        'TH' => 'thai',
        'TR' => 'turkish',
        'UK' => 'ukrainian'
    );

    $method = $_SERVER['REQUEST_METHOD'];

    /* 
     * preg_match for the 2 parameters: Steam Language Code, appid
     * unset because we dont need the whole string from preg_match
     */
    $parameter = array();
    $request = preg_match("/^\/.*[.php]\/([a-zA-Z][A-Za-z])\/(\d*)$/", $_SERVER['REQUEST_URI'], $parameter);
    unset($parameter[0]);
    
    //Drop all requests which are not GET
    //Also print error if their arent any parameters set or they dont match up with the pattern
    if($method != 'GET'){
        response(array('success' => 'false', 'error' => 'Method: '.$method.' not allowed!'), 405);
    }
    
    if(count($parameter) != 2){
        $site = array();
        preg_match("/.*[.php]/", $_SERVER['REQUEST_URI'], $site);
        response(array('success' => 'false', 'error' => 'Not enough or wrong parameters set!', 'usage' =>  $_SERVER['SERVER_NAME'].$site[0].'/param1/param2', 'param1' => '2 Digit Steam Language Code (full list: https://github.com/UberDoge/steamStore-api/wiki/Steam-Language-Codes)', 'param2' => 'Steam appid (e.g 10, 730, 319630)'), 400);
    }

    if(!array_key_exists(strtoupper($parameter[1]), $languages)){
        response(array('success' => 'false', 'error' => 'Language code '.$parameter[1].' not found. Full list of Steam Language Codes (https://github.com/UberDoge/steamStore-api/wiki/Steam-Language-Codes)'), 400);
    }
    
    $storeUrl = 'http://store.steampowered.com/app/'.$parameter[2].'?l='.$languages[strtoupper($parameter[1])];
    $content = file_get_contents($storeUrl);

    $regex_patterns = array(
        '/class="apphub_AppIcon"><img src="(.*)"><div/', //Get game icon
        '/class="apphub_AppName">(.*)<\/div>/', //get game name
        '/class="highlight_screenshot_link" .*>\s*<img src="(.*)"/', //get game screenshots
        '/<video class="highlight_player_item highlight_movie" src="(.*)[?].*" d.*/', //get game movies
        '/<img class="game_header_image_full" src="(.*)[?].*"/', //get game header img
        '/<div class="game_description_snippet">\s*(.*)<\/div>/', //get game description [maybe find better pattern]
        '/<div class="user_reviews_summary_row" data-store-tooltip="(.*) of the (.*) u.*"/',//get games recent and overall review score
        '/<span class="date">(.*)<\/span>/', //get release date
        '/<a .* class="app_tag" .*>\s*(.*)[\s]{12,}<\/a>/', //get tags
        '/<div id="game_area_metascore">\s*<span>(\d\d)<\/span>/', //get metascore
        '/<div class="details_block">\s*.*\s*.*>(.*)<\/a>/', //get genre
        '/<div class="details_block">\s*.*\s*.*\s*.*\s.*>(.*)<\/a>/', //get developer
        '/<div class="details_block">\s*.*\s*.*\s*.*\s.*\s*.*\s*.*\s*.*>(.*)<\/a>/', //get publisher
        '/<div class="game_area_details_specs">.*><a class="name" href=".*">(.*)<\/a><\/div>/' //get game categories (not sure if this works)
    );
    
    //Todo: all the api stuff

    function response($data, $statusCode = 200){
        header("HTTP/1.1 ".$statusCode);
        print_r(json_encode($data));
        exit(0);
    }