<?php
$arFiles = glob('lang/ar/*.php');
foreach ($arFiles as $arFile) {
    $filename = basename($arFile);
    $frFile = "lang/fr/$filename";
    $enFile = "lang/en/$filename";

    if (!file_exists($frFile)) {
        echo "Missing French file: $filename\n";
        continue;
    }

    $ar = include $arFile;
    $fr = include $frFile;
    $en = file_exists($enFile) ? include $enFile : [];

    $arKeys = array_keys($ar);
    $frKeys = array_keys($fr);
    
    $missingInFr = array_diff($arKeys, $frKeys);
    $missingInAr = array_diff($frKeys, $arKeys);

    if (count($missingInFr) > 0) {
        echo "Missing in French ($filename): " . count($missingInFr) . " keys\n";
        foreach ($missingInFr as $key) echo "  - $key\n";
    }
    if (count($missingInAr) > 0) {
        echo "Missing in Arabic ($filename): " . count($missingInAr) . " keys\n";
        foreach ($missingInAr as $key) echo "  - $key\n";
    }
}
