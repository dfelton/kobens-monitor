<?php

namespace Kobens\Monitor\TradeRepeater;

use Kobens\Core\Db;
use Zend\Db\Adapter\Adapter;
use Kobens\Math\BasicCalculator\Add;

final class DailyProfits
{
    /**
     * @var Adapter
     */
    private $db;

    public function __construct()
    {
        $this->db = (new Db())->getAdapter();
    }

    public function getData(): array
    {
        $data = ['total_notional' => []];
        foreach ($this->getRaw() as $date => $profits) {
            foreach ($profits as $symbol => $profit) {
                if (($data[$symbol] ?? null) === null) {
                    $data[$symbol] = [];
                }
                $data[$symbol][] = ['date' => $date, 'amount' => $profit];
            }
            foreach (array_diff(array_keys($data), array_keys($profits)) as $symbol) {
                $data[$symbol][] = ['date' => $date, 'amount' => '0'];
            }
        }
        return $data;
    }

    private function getRaw(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM `repeater_stats_daily_profit` ORDER BY `date`, `symbol`'
        );
        $rows = $stmt->execute();
        $data = [];
        if ($rows->count() !== 0) {
            foreach ($rows as $row) {
                if (($data[$row['date']] ?? null) === null) {
                    $data[$row['date']] = [];
                }
                $data[$row['date']][$row['symbol']] = $row['amount'];
                $data[$row['date']]['total_notional'] = Add::getResult(
                    $data[$row['date']]['total_notional'] ?? '0',
                    $row['amount_notional']
                );
            }
        }
        return $data;
    }
}
