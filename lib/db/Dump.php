<?php


namespace lib\db;

use Exception;
use PDO;

/**
 * Database dump
 */
class Dump
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var string[]
     */
    private $allowedTables;

    /**
     * Dump constructor.
     *
     * @param string[] $tableWhitelist
     *
     * @throws Exception
     */
    public function __construct(array $tableWhitelist)
    {
        $this->pdo = Connection::get_db_pdo();
        $this->allowedTables = $tableWhitelist;
    }

    /**
     * Read a complete table (return contents as an 2-dimensional array)
     *
     * @param string $tableName The name of the table. This is validated against a whitelist.
     *
     * @return array
     */
    public function loadAllRowsFromTable(string $tableName): array
    {
        // check against whitelist
        if (!in_array($tableName, $this->allowedTables, true)) {
            // for now, simply return an empty array.. since this is not a valid use case.
            return [];
        }

        // Concatenating the table name is safe here, since the tableName is
        // checked against a whitelist of allowed values.
        $sql = 'SELECT * FROM ' . $tableName;
        $stmt = $this->pdo->prepare($sql);

        $header = null;
        $result = [];

        // Write DB data to file
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($header === null) {
                    // use keys as header row
                    $header = array_keys($row);
                    // this is also the first row of the result
                    $result[] = $header;
                }
                $r = [];
                // iterate over the header array, to maintain the same order of columns
                foreach ($header as $key) {
                    $r[] = $row[$key];
                }
                $result[] = $r;
            }
        }
        return $result;
    }
}
