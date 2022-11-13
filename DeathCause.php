<?php
class Row
{
    protected string $date;
    protected string $reason;
    protected array $causesOne = [];
    protected array $causesTwo = [];
    protected array $causesThree = [];

    public function __construct(string $date, string $reason, array $causesOne = [], array $causesTwo = [], array $causesThree = [])
    {
        $this->date = $date;
        $this->reason = $reason;
        $this->causesOne = $causesOne;
        $this->causesTwo = $causesTwo;
        $this->causesThree = $causesThree;
    }
}

class Filter extends Row
{
    public static function deathsInYear(string $year, array $dataDb): int
    {
        $counter = 0;
        foreach ($dataDb as &$value) {
            if (strpos($value->date, $year) !== false) {
                $counter++;
            }
        }
        return $counter;
    }

    public static function deathReasonCount(array $dataDb): array
    {
        $dataDbFilter = [];
        foreach ($dataDb as &$object) {
            if (isset($object->reason)) {
                $reason = $object->reason;
                if (!isset($dataDbFilter[$reason])) {
                    $dataDbFilter[$reason] = 0;
                }
                $dataDbFilter[$reason]++;
            }
        }
        return $dataDbFilter;
    }

    public static function deathReasonReasonCount(array $dataDb): array
    {
        $dataDbFilter = [];
        foreach ($dataDb as &$object) {
            $reason = $object->reason;
            if ($reason == "Nevardarbīga nāve") {
                $dataDbFilter[] = $object->causesOne;
            }
        }
        $filter = [];
        foreach ($dataDbFilter as &$item) {
            $filter[] = implode(" ", $item);
        }
        return array_count_values($filter);
    }
}

$row = 1;
$rows = [];

if(($handle = fopen("vtmec-causes-of-death.csv", "r")) !== false) {
    while (($data = fgetcsv($handle, 5000, ",")) !== false) {
        $num = count($data);
        $row++;
        $rows[] = new Row($data[1], $data[2], array_filter(explode(";", $data[3])),
            array_filter(explode(";", $data[4])), array_filter(explode(";", $data[5])));
        //if ($row > 30) break;
    }
    fclose($handle);
}
//var_dump($rows);
//Death count in 'specific' year.
/*echo Filter::deathsInYear("2022", $rows) . PHP_EOL;
echo Filter::deathsInYear("2021", $rows) . PHP_EOL;
echo Filter::deathsInYear("2020", $rows) . PHP_EOL;*/

//Deaths by deaths reason and count
//var_dump(Filter::deathReasonCount($rows));

//Deaths specific reason count
var_dump(Filter::deathReasonReasonCount($rows));