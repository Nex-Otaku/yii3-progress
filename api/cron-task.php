<?php
    require_once __DIR__ . '/vendor/autoload.php';
    
    use NexOtaku\MinimalFilesystem\Filesystem;

    function isStableVersion($version) {
        if ($version === '') {
            return false;
        }

        $parts = explode('.', $version);
        $firstPart = $parts[0];
        $firstVersionNumber = intval($firstPart);

        return $firstVersionNumber >= 1;
    }

    function getResponse()
    {
        $url = "https://www.yiiframework.com/status/3.0";
        $html = file_get_contents($url);

        $response = [];

        if ($html === FALSE) {
            echo "Error retrieving the URL.";
            $response["error"] = "Can't obtain URL-address!";

            return $response;
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        $xpath = new DOMXPath($dom);
        $table = $xpath->query("//*[@id='w0']/table/tbody")->item(0);

        $stablePackageCount = 0;

        if (!$table) {
            $response["error"] = "Can't find the table!";

            return $response;
        }

        $rows = $xpath->query(".//tr", $table);
        $totalPackCount = 0;

        if ($rows->length <= 0) {
            $response["error"] = "Zero row length!";

            return $response;
        }

        foreach($rows as $row) {
            $cells = $xpath->query(".//td", $row);
            $isOptional = $cells->item(11)->textContent === 'Yes';

            if ($isOptional) {
                continue;
            }

            $totalPackCount++;
            $latestVersion = $cells->item(2)->textContent;
            $isStable = isStableVersion($latestVersion);

            if ($isStable) {
                $stablePackageCount++;
            }
        }

        $progress = floor((100 *  $stablePackageCount) / $totalPackCount);

        $response['totalPackages'] = $totalPackCount;
        $response['stablePackages'] = $stablePackageCount;
        $response['progress'] = $progress;

        date_default_timezone_set('Europe/Moscow');
        $response['updatedAt'] = date('d.m.Y H:i:s');

        return $response;
    }

    $response = getResponse();

    $fs = new Filesystem();
    $filePath = __DIR__ . '/../docs/data.js';
    $jsContent = "window.yii3 = ".json_encode($response, JSON_PRETTY_PRINT).";";
    $fs->writeFile($filePath, $jsContent);
?>