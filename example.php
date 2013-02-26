<?php

require "vendor/autoload.php";

use Rezzza\Vaultage\Vaultage;

$vaultage = new Vaultage('.vaultage.json.example');
print "<pre>";
var_dump($vaultage);
print "</pre>";
exit('ici');
