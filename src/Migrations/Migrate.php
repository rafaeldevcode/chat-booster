<?php

namespace ChatBooster\Migrations;

use ChatBooster\Conection;
use Exception;
use PDO;

class Migrate extends Conection
{
    private $columns = [];
    private $current_column = [];
    private $current_indice = 0;
    private $current_foreign_key = 0;
    private $timestamps = false;
    private $constraint = [];
    public $table;

    public function __call(string $method, array $arguments): self
    {
        if(isset($arguments[0])):
            $lenght = isset($arguments[1]) ? $arguments[1] : 255;

            $this->current_column = [
                'column_name' => $arguments[0],
                'column_type' => $this->getColumnType($method, (int)$lenght),
                'nullable' => ' NOT NULL',
                'default' => '',
                'primary_key' => '',
                'unique' => ''
            ];

            $this->columns[] = $this->current_column;

            $this->current_indice = count($this->columns)-1;
        endif;

        return $this;
    }

    public function nullable(): self
    {
        if(empty($this->columns[$this->current_indice]['default'])):
            $this->columns[$this->current_indice]['nullable'] = ' DEFAULT NULL';
        endif;

        return $this;
    }

    public function default(string $value): self
    {
        if($this->columns[$this->current_indice]['nullable'] == ' NOT NULL'):
            $this->columns[$this->current_indice]['default'] = " DEFAULT '{$value}'";
        endif;

        return $this;
    }

    public function unique(): self
    {
        $this->columns[$this->current_indice]['unique'] = " UNIQUE";
        return $this;
    }

    public function timestamps(): self
    {
        $this->timestamps = true;
        return $this;
    }

    public function primaryKey(): self
    {
        $this->columns[$this->current_indice]['primary_key'] = " AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function foreignKey(string $foreign_key): self
    {
        $this->constraint[] = [
            'foreign_key' => $foreign_key
        ];

        $this->current_foreign_key = count($this->constraint)-1;

        return $this;
    }

    public function references(string $column_references): self
    {
        $this->constraint[$this->current_foreign_key]['column_references'] = $column_references;

        return $this;
    }

    public function on(string $table): self
    {
        $this->constraint[$this->current_foreign_key]['table'] = $table;

        return $this;
    }

    public function create(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS $this->table";
        $count_column = count($this->columns);

        if($count_column > 0) $query .= " (";

        foreach($this->columns as $column):
            $column_name = $column['column_name'];
            $column_type = $column['column_type'];
            $nullable = $column['nullable'];
            $default = $column['default'];
            $primary_key = $column['primary_key'];
            $unique = $column['unique'];

            $query .= "`{$column_name}` {$column_type}{$nullable}{$default}{$primary_key}{$unique}, ";
        endforeach;

        if($this->timestamps):
            $query .= "`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        endif;

        if($count_column > 0) $query .= ")";
        $query = str_replace(', )', ')', $query);

        $this->connection->exec($query);

        if(!empty($this->constraint)):
            foreach($this->constraint as $key):
                $query = "ALTER TABLE {$this->table} ADD KEY `fk_{$key['table']}_{$this->table}` (`{$key['foreign_key']}`)";
                $this->connection->exec($query);
                
                $query = "ALTER TABLE {$this->table} ADD CONSTRAINT `fk_{$key['table']}_{$this->table}` FOREIGN KEY (`{$key['foreign_key']}`) REFERENCES `{$key['table']}` (`{$key['column_references']}`)";
                $this->connection->exec($query);
            endforeach;
        endif;
    }

    public function hasTable(string $table): bool
    {
        $query = "SHOW TABLES LIKE :table";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':table', $table, PDO::PARAM_STR);
        $statement->execute();

        return $statement->rowCount() > 0 ? true : false;
    }

    private function getColumnType(string $method, int $lenght = 255): string
    {
        switch($method):
            case 'string':
                return "VARCHAR({$lenght})";
            case 'integer':
                return 'INT';
            case 'char':
                return "CHAR({$lenght})";
            case 'date':
                return "DATE";
            case 'datetime':
                return "DATETIME";
            case 'text':
                return "TEXT({$lenght})";
            case 'longtext':
                return "LONGTEXT";
            case 'boolean':
                return "BOOLEAN";
            case 'json':
                return "JSON";
            default:
                throw new Exception("Tipo de coluna inválido: $method");
        endswitch;
    }
}
