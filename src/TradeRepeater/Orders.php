<?php

namespace Kobens\Monitor\TradeRepeater;

use Kobens\Core\Db;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

final class Orders
{
    const TABLE_NAME = 'trade_repeater';

    /**
     * @var Adapter
     */
    private $db;

    public function __construct()
    {
        $this->db = (new Db())->getAdapter();
    }

    public function getOrders() : \Generator
    {
        $data = $this->getTable()->select(function(Select $select)
        {
            $select->columns([
                'id',
                'symbol',
                'status',
                'buy_price',
                'buy_amount',
                'sell_price',
                'sell_amount'
            ]);
            $select->where->in('status', ['BUY_PLACED', 'SELL_PLACED']);
        });
        foreach ($data as $row) {
            yield [
                'id' => $row->id,
                'symbol' => $row->symbol,
                'side' => $row->status === 'BUY_PLACED' ? 'buy' : 'sell',
                'amount' => (float) ($row->status === 'BUY_PLACED' ? $row->buy_amount : $row->sell_amount),
                'price' => (float) ($row->status === 'BUY_PLACED' ? $row->buy_price : $row->sell_price),
                'buy_amount' => $row->buy_amount,
                'buy_price' => $row->buy_price,
                'sell_price' => $row->sell_price,
                'sell_amount' => $row->sell_amount,
            ];
        }
    }

    private function getTable(): TableGateway
    {
        return new TableGateway(self::TABLE_NAME, $this->db);
    }
}
