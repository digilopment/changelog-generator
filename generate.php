<?php
require_once __DIR__ . '/src/Changelog.php';

$repositoryPath = $argv[1] ?? '';
$generatePath = $argv[2] ?? '';
(new Changelog($repositoryPath, $generatePath))->generate()->writeToFile()->render();
