<?php
    class Utilities
    {
        /**Load DB-config
         * @throws RuntimeException: Failed to parse configuration file
         * @param string $filePath: location of config.ini for Database configuration
         * @return array|false
         */
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

        /**extract nested array from associative array
         * @param array $array:haystack
         * @param $extract:needle
         * @return array
         */
        public static function extractFromAssociativeArray(array $array, $extract): array
        {
            $data = [];
            foreach ($array as $value) {
                $data[]=$value[$extract];
            }
            return $data;
        }


    }