<?php

require_once __DIR__ . '/src/Changelog.php';

(new Changelog())->generate()->writeToFile();
