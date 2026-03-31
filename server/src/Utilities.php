<?php
    class Utilities
    {
        public static function loadConfig(string $filePath): array|false
        {
            if (!file_exists($filePath)) {
                throw new RuntimeException("Config file not found: $filePath");
            }

            $config = parse_ini_file($filePath, false, INI_SCANNER_TYPED);

            if ($config === false) {
                throw new RuntimeException("Failed to parse config.ini");
            }
            return $config;
        }
        public static function extractFromAssociativeArray(array $array): array
        {
            $data = [];
            foreach ($array as $value) {
                $data[]=$value["Field"];
            }
            return $data;
        }


    }