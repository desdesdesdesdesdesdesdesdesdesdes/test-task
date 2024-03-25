<?php
//declare(strict_types=1);

//==============Не редактировать
final class DataBase
{
    private bool $isConnected = false;

    public function connect(): bool
    {
        sleep(1);
        $this->isConnected = true;
        return 'connected';
    }

    public function random()
    {
        $this->isConnected = rand(0, 3) ? $this->isConnected : false;
    }

    public function fetch($id): string
    {
        $this->random();
        if (!$this->isConnected) {
            throw new Exception('No connection');
        }
        usleep(100000);
        return 'fetched - ' . $id;
    }

    public function insert($data): string
    {
        $this->random();
        if (!$this->isConnected) {
            throw new Exception('No connection');
        }
        usleep(900000);
        return 'inserted - ' . $data;
    }


    public function batchInsert($data): string
    {
        $this->random();
        if (!$this->isConnected) {
            throw new Exception('No connection');
        }
        usleep(900000);
        return 'batch inserted';
    }
}
//==============

class DataBaseHelper
{
    private static $instance;
    private DataBase $dataBase;
    public function __construct()
    {
        $this->dataBase = new DataBase();
        $this->dataBase->connect();
    }
    public static function getInstance():DataBaseHelper
    {
        if (self::$instance === null) {
            self::$instance = new DataBaseHelper();
        }
        return self::$instance;
    }
    public function fetch(int $id):string
    {
        return $this->doDatabaseOperation('fetch', $id);
    }
    public function insert(int $data):string
    {
        return $this->doDatabaseOperation('insert', $data);
    }
    public function batchInsert(array $dataArray):string
    {
        foreach ($dataArray as $key=>$value) {
            settype($dataArray[$key], 'int');
        }
        return $this->doDatabaseOperation('batchInsert', $dataArray);
    }
    private function doDatabaseOperation(string $operation, $data):string
    {
        // reconnect and repeat operation on error
        do {
            try {
                $result = $this->dataBase->{$operation}($data);
                $connectionWorked = true;
            } catch (Exception $e) {
                if ($e->getMessage() == 'No connection') {
                    $connectionWorked = false;
                    $this->dataBase->connect();
                }
            }
        } while($connectionWorked==false);

        return $result;
    }
}

function step1(array $dataToFetch):void
{
    $dataBaseHelper = DataBaseHelper::getInstance();
    foreach ($dataToFetch as $dataRow) {
        print($dataBaseHelper->fetch($dataRow));
        print(PHP_EOL);
    }
}

function step2(array $dataToInsert):void
{
    $dataBaseHelper = DataBaseHelper::getInstance();
    print($dataBaseHelper->batchInsert($dataToInsert));
    print(PHP_EOL);
}

//==============Не редактировать
$dataToFetch = [1, 2, 3, 4, 5, 6];
$dataToInsert = [7, 8, 9, 10, 11, 12];

step1($dataToFetch);
step2($dataToInsert);
print("Success");
//==============