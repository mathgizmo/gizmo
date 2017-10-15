<?php
if (file_exists(dirname(__FILE__) .'/ci/index.html')) {
    include('ci/index.html');
} else {
    echo 'please run "npm run prod-build" in ci folder';
}