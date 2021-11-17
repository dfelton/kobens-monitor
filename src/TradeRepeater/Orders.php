<?php

namespace Kobens\Monitor\TradeRepeater;

use Kobens\Core\Db;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Kobens\Math\BasicCalculator\Add;
use Kobens\Math\BasicCalculator\Divide;

final class Orders
{
    const TABLE_NAME = 'trade_repeater';

    /**
     * @var Adapter
     */
    private Adapter $db;

    private array $colors = [
        'BUY_PLACED' => '#0f9100',
        'SELL_PLACED' => '#bf0000',
        'BUY_READY' => '#ad8500',
        'BUY_SENT' => '#ad8500',
        'SELL_SENT' => '#ad8500',
        'BUY_FILLED' => '#ad8500',
        'SELL_FILLED' => '#ad8500',
        'DISABLED' => '#787878',
    ];

    public function __construct()
    {
        $this->db = (new Db())->getAdapter();
    }

    public function getOrders(string $symbol) : \Generator
    {
        $data = $this->getTable()->select(function(Select $select) use ($symbol)
        {
            $select->columns([
                'id',
                'symbol',
                'status',
                'buy_price',
                'buy_amount',
                'sell_price',
                'sell_amount',
                'meta',
            ]);
            $select->where->equalTo('symbol', $symbol);
        });
        foreach ($data as $row) {
            $meta = json_decode($row->meta ?? '{}', true);
            yield [
                'id' => $row->id,
                'symbol' => $row->symbol,
                'status' => $row->status,
                'color' => $this->colors[$row->status] ?? '#000000',
                'amount' => (float) ($row->status === 'SELL_PLACED' ? $row->sell_amount : $row->buy_amount),
                'price' => (float) ($row->status === 'SELL_PLACED' ? $row->sell_price : $row->buy_price),
                'buy_amount' => $row->buy_amount,
                'buy_price' => $row->buy_price,
                'average_buy_price' => $this->getAverageBuyPrice($meta['buy_price'] ?? ['0']),
                'sell_price' => $row->sell_price,
                'sell_amount' => $row->sell_amount,
            ];
        }
    }

    /**
     *
     * @param string|array $prices
     * @return string
     */
    private function getAverageBuyPrice($prices): string
    {
        if (!is_array($prices) && !is_string($prices)) {
            throw new \InvalidArgumentException('"$prices" argument must be a string or array of strings.');
        }
        $prices = (array) $prices;
        $sum = '0';
        foreach ($prices as $price) {
            $sum = Add::getResult($sum, $price);
        }
        return Divide::getResult($sum, (string) count($prices), 8);
    }

    private function getTable(): TableGateway
    {
        return new TableGateway(self::TABLE_NAME, $this->db);
    }
}
