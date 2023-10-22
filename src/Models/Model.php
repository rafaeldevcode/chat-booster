<?php

namespace ChatBooster\Models;

use ChatBooster\Conection;
use Exception;
use PDO;
use stdClass;

class Model extends Conection
{
    public $table;
    public $data = null;
    private $wheres = [];
    private $foreign_pivot_key = null;
    private $related_pivot_key = null;
    private $related_id = null;
    private $related_table = null;

    public function where(string $column, string $operator, string $value, string $exec = 'AND'): self
    {
        $this->wheres[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'exec' => $exec,
        ];

        return $this;
    }

    public function find(int $id = 1): self
    {
        $this->wheres = [];
        $data = $this->where('id', '=', $id)->get();

        if(!empty($data)):
            $this->data = json_decode(json_encode($data[0]));
        endif;

        return $this;
    }

    public function count(): int|null
    {
        $where_clause = $this->whereClausure();

        $query = "SELECT COUNT(id) as total FROM {$this->table}{$where_clause->clausure}";

        $statement = $this->connection->prepare($query);

        foreach ($where_clause->bindings as $column => $value):
            $statement->bindValue(":{$column}", $value);
        endforeach;

        $statement->execute();

        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($data)):
            return (int)$data[0]['total'];
        endif;

        return null;
    }

    public function first(): stdClass|null
    {
        $where_clause = $this->whereClausure();

        $query = "SELECT * FROM {$this->table}{$where_clause->clausure} LIMIT 1";

        $statement = $this->connection->prepare($query);

        foreach ($where_clause->bindings as $column => $value):
            $statement->bindValue(":{$column}", $value);
        endforeach;

        $statement->execute();

        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($data)):
            $this->data = json_decode(json_encode($data[0]));
        endif;

        return $this->data;
    }

    public function last(?string $order_column = null): stdClass|null
    {
        $order_column = isset($order_column) ? $order_column : 'id';

        $where_clause = $this->whereClausure();

        $query = "SELECT * FROM {$this->table}{$where_clause->clausure} ORDER BY {$order_column} DESC LIMIT 1";

        $statement = $this->connection->prepare($query);

        foreach ($where_clause->bindings as $column => $value):
            $statement->bindValue(":{$column}", $value);
        endforeach;

        $statement->execute();

        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($data)):
            $this->data = json_decode(json_encode($data[0]));
        endif;

        return $this->data;
    }

    public function create(array $data): stdClass|null
    {
        $columns = implode('`, `', array_keys($data));
        $bindings = ':' . implode(', :', array_keys($data));

        $query = "INSERT INTO {$this->table} (`{$columns}`) VALUES ({$bindings})";
        
        $statement = $this->connection->prepare($query);

        foreach($data as $indice => $value):
            $statement->bindValue(":{$indice}", $value);
        endforeach;

        $statement->execute();

        $last_insert_id = $this->connection->lastInsertId();

        if($statement->rowCount() > 0 && $this->existColumn('id')):
            $this->find($last_insert_id);

            return $this->data;
        endif;

        return null;
    }

    public function get(): array|null
    {
        $where_clause = $this->whereClausure();

        $query = "SELECT * FROM {$this->table}{$where_clause->clausure}";

        $statement = $this->connection->prepare($query);

        foreach ($where_clause->bindings as $column => $value):
            $statement->bindValue(":{$column}", $value);
        endforeach;

        $statement->execute();

        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->data = empty($data) ? null : json_decode(json_encode($data));

        return $this->data;
    }

    public function update(array $data): bool
    {
        $query = '';
        $where_clause = $this->whereClausure();

        foreach($data as $indice => $value):
            $query .= "{$indice} = :d_{$indice}, ";
        endforeach;

        $query = rtrim($query, ', ');

        $query = "UPDATE {$this->table} SET {$query}{$where_clause->clausure}";

        $statement = $this->connection->prepare($query);

        foreach($data as $indice => $value):
            $statement->bindValue(":d_{$indice}", $value);
        endforeach;

        foreach ($where_clause->bindings as $column => $value):
            $statement->bindValue(":{$column}", $value);
        endforeach;

        return $statement->execute();
    }

    public function delete(): bool
    {
        $where_clause = $this->whereClausure();

        $query = "DELETE FROM {$this->table}{$where_clause->clausure}";

        $statement = $this->connection->prepare($query);

        foreach ($where_clause->bindings as $column => $value):
            $statement->bindValue(":{$column}", $value);
        endforeach;

        return $statement->execute();
    }

    public function paginate(int $limit, string $order_column = 'id'): stdClass
    {
        $count = ceil(($this->count() / $limit));
        $page = ($count == 0 ? 0 : 1);
        $requests = requests();

        if(isset($requests->page)):
            $page = filter_var($requests->page, FILTER_VALIDATE_INT);
            $page = !$page ? 1 : $page;
        endif;

        $start = (($page ==  0 ? 1 : $page) * $limit) - $limit;

        $where_clause = $this->whereClausure();

        $query = "SELECT * FROM {$this->table}{$where_clause->clausure} ORDER BY $order_column DESC LIMIT $start, $limit";

        $statement = $this->connection->prepare($query);

        foreach ($where_clause->bindings as $column => $value):
            $statement->bindValue(":{$column}", $value);
        endforeach;

        $statement->execute();

        $this->data = json_decode(json_encode($statement->fetchAll(PDO::FETCH_ASSOC)));

        return json_decode(json_encode([
            'count' => $count,
            'page' => ($count == 0 ? 0 : $page),
            'next' => ($page == $count ? null : $page+1),
            'prev' => (($page == 1 || $page == 0) ? null : $page-1),
            'data' => $this->data,
            'search' => isset($requests->search) ? $requests->search : null,
            'total' => $this->count()
        ]));
    }

    public function belongsToMany(string $related, string $table, string $foreign_pivot_key, string $related_pivot_key)
    {
        $related = new $related();

        $id = empty($this->data) ? 0 : $this->data->id;

        $this->setRelatioships($related, $id, $table, $foreign_pivot_key, $related_pivot_key);

        $query = "
            SELECT {$related->table}. *
            FROM {$related->table}
            INNER JOIN {$table} ON {$related->table}.id = {$table}.{$related_pivot_key}
            WHERE {$table}.{$foreign_pivot_key} = :foreign_pivot_key
        ";
        
        $statement = $this->connection->prepare($query);
        $statement->bindValue(":foreign_pivot_key", $id, PDO::PARAM_INT);
        $statement->execute();
        
        $related->data = json_decode(json_encode($statement->fetchAll(PDO::FETCH_ASSOC)));
        
        return $related;     
    }

    public function hasMany(string $related, string $table, string $foreign_pivot_key)
    {
        $related = new $related();

        $id = empty($this->data) ? 0 : $this->data->id;

        $query = "SELECT * FROM {$table} WHERE {$foreign_pivot_key} = :foreign_pivot_key";
        $statement = $this->connection->prepare($query);
        $statement->bindParam(':foreign_pivot_key', $id, PDO::PARAM_INT);
        $statement->execute();
        
        $related->data = json_decode(json_encode($statement->fetchAll(PDO::FETCH_ASSOC)));

        return $related;
    }

    public function belongsTo(string $related, string $table, string $foreign_pivot_key)
    {
        $related = new $related();

        $id = empty($this->data) ? 0 : $this->data->id;

        $query = "SELECT * FROM {$table} WHERE id = (SELECT {$foreign_pivot_key} FROM {$this->table} WHERE id = :id)";
        $statement = $this->connection->prepare($query);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        
        $related->data = json_decode(json_encode($statement->fetchAll(PDO::FETCH_ASSOC)));

        return $related;
    }

    public function sync(array|int|null $attributes): bool
    {
        if(is_null($attributes)):
            $attributes = [];
        endif;

        if(!is_array($attributes)):
            $attributes = [$attributes];
        endif;

        $existing = [];
        $select_query = "SELECT {$this->related_pivot_key} FROM {$this->related_table} WHERE {$this->foreign_pivot_key} = :foreign_pivot_key";
        $select_statement = $this->connection->prepare($select_query);
        $select_statement->bindValue(':foreign_pivot_key', $this->related_id  , PDO::PARAM_INT);
        $select_statement->execute();

        while ($row = $select_statement->fetch(PDO::FETCH_ASSOC)):
            $existing[] = $row[$this->related_pivot_key];
        endwhile;

        $new_data = array_diff($attributes, $existing);
        
        try {
            $this->connection->beginTransaction(); // Start transaction

            $insert_query = "INSERT IGNORE INTO {$this->related_table} ({$this->related_pivot_key}, {$this->foreign_pivot_key}) VALUES (:related_pivot_key, :foreign_pivot_key)";
            $insert_statement = $this->connection->prepare($insert_query);
        
            foreach ($new_data as $new_data_id):
                $insert_statement->bindValue(':related_pivot_key', $new_data_id, PDO::PARAM_INT);
                $insert_statement->bindValue(':foreign_pivot_key', $this->related_id, PDO::PARAM_INT);
    
                if (!$insert_statement->execute()):
                    throw new Exception("There was an unexpected error during insertion!");
                endif;
            endforeach;

            // $not_contains = implode(',', array_fill(0, count($attributes), '?'));
            $not_contains = implode(',', $attributes);
            $delete_query = "DELETE FROM {$this->related_table} WHERE {$this->foreign_pivot_key} = :foreign_pivot_key AND {$this->related_pivot_key} NOT IN ({$not_contains})";
            $delete_statement = $this->connection->prepare($delete_query);
            $delete_statement->bindValue(':foreign_pivot_key', $this->related_id, PDO::PARAM_INT);

            // foreach ($attributes as $index => $value):
            //     $delete_statement->bindValue($index + 1, $value, PDO::PARAM_INT);
            // endforeach;

            $delete_statement->execute();
    
            $this->connection->commit(); // Confirm transaction
    
            return true;
    
        } catch (Exception $e) {
            $this->connection->rollBack(); // Rollback transaction
            return false;
        }
    }

    public function attach(array|int|null $attributes): bool
    {
        if(!is_array($attributes)):
            $attributes = [$attributes];
        endif;
        
        try {
            $this->connection->beginTransaction(); // Start transaction

            $insert_query = "INSERT IGNORE INTO {$this->related_table} ({$this->related_pivot_key}, {$this->foreign_pivot_key}) VALUES (:related_pivot_key, :foreign_pivot_key)";
            $insert_statement = $this->connection->prepare($insert_query);
        
            foreach ($$attributes as $attribute):
                $insert_statement->bindValue(':related_pivot_key', $attribute, PDO::PARAM_INT);
                $insert_statement->bindValue(':foreign_pivot_key', $this->related_id, PDO::PARAM_INT);
    
                if (!$insert_statement->execute()):
                    throw new Exception("There was an unexpected error during insertion!");
                endif;
            endforeach;
    
            $this->connection->commit(); // Confirm transaction
    
            return true;
    
        } catch (Exception $e) {
            $this->connection->rollBack(); // Rollback transaction
            return false;
        }
    }

    public function detach(array|int|null $attributes): bool
    {
        if(!is_array($attributes)):
            $attributes = [$attributes];
        endif;
        
        try {
            $this->connection->beginTransaction(); // Start transaction

            $delete_query = "DELETE FROM {$this->related_table} WHERE {$this->foreign_pivot_key} = :foreign_pivot_key";

            $delete_statement = $this->connection->prepare($delete_query);
            $delete_statement->bindValue(':foreign_pivot_key', $this->related_id, PDO::PARAM_INT);
            $delete_statement->execute();

            $this->connection->commit(); // Confirm transaction
    
            return true;
    
        } catch (Exception $e) {
            $this->connection->rollBack(); // Rollback transaction
            return false;
        }
    }

    private function whereClausure(): stdClass
    {
        $where_clause = '';
        $bindings = [];

        foreach ($this->wheres as $index => $where):
            $where_clause .= ($index === 0 ? ' WHERE ' : " {$where['exec']} ");
            $where_clause .= "`{$where['column']}` {$where['operator']} :{$where['column']}";
            $bindings[$where['column']] = $where['value'];
        endforeach;

        return json_decode(json_encode([
            'clausure' => $where_clause,
            'bindings' => $bindings
        ]));
    }

    private function existColumn(string $column): bool
    {
        $db_name = env('DB_DATABASE_NAME');
        $query = "SELECT COUNT(*) AS colExists
            FROM information_schema.columns
            WHERE table_schema = :dbname
                AND table_name = :table
                AND column_name = :column";

        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(":dbname", $db_name, PDO::PARAM_STR);
        $stmt->bindParam(":table", $this->table, PDO::PARAM_STR);
        $stmt->bindParam(":column", $column, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['colExists'] > 0 ? true : false;
    }

    private function setRelatioships(mixed $related, int $related_id, string $related_table, string $foreign_pivot_key, string $related_pivot_key): void
    {
        $related->foreign_pivot_key = $foreign_pivot_key;
        $related->related_pivot_key = $related_pivot_key;
        $related->related_id = $related_id;
        $related->related_table = $related_table;
    }
}
