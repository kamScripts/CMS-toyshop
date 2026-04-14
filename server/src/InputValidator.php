<?php
/**Php input validator against controller's tables schema - tableMaps
 * Validates data type and length of varchar fields
 * Number of validations reduced to data types used in project
 * Future use need adding additional validators
 */

class InputValidator
{
    public function __construct(private array $tableMap) {}
    public function validateInput(string $tableName, array $input, bool $is_new = false): array {
        if (!isset($this->tableMap[$tableName])) {
            return ["error" => "Table '$tableName' does not exist."];
        }
        $schema = $this->tableMap[$tableName];
        $errors = [];

        foreach ($schema["columns"] as $index => $column) {
            if ($index === "variant_id") continue;
            if ($is_new && $index==0) {
                continue; //First column record Id autoincrement skip}
            }

            $rawValue = $input[$column] ?? null;
            $value = is_string($rawValue) ? trim($rawValue) : $rawValue;
            $type = $schema["types"][$index];

            if ($value) {
                if (str_starts_with($type, "int")) {
                    if (filter_var($rawValue, FILTER_VALIDATE_INT) === false) {
                        $errors[] = "$column must be an integer";
                    }
                }
                if (str_starts_with($type, "varchar")) {
                    // Function assumes all text fields have specified length

                    $size = (int) trim(substr($type, strlen("varchar")), "()");

                    if(mb_strlen($value) > $size) {
                        $errors[] = "$column must be less than or equal to $size.";
                    }
                    if (preg_match('/[^a-zA-Z0-9.,_\s-]/', $value))  {
                        $errors[] = "$column must contain only alphanumeric characters or (,_- ).";
                    }
                }
                if(str_starts_with($type, "decimal")) {
                    if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
                        $errors[] = "$column must be a float.";
                    }
                }

            }
        }
        return $errors;
    }

}