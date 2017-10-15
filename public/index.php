<?php
if (file_exists(dirname(__FILE__) .'/ci/index.html')) {
    include('ci/index.html');
} else {
    echo 'please run "ng build --prod" in ci folder';
}