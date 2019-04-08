<?php

namespace Kobens\Monitor\TradeRepeater;

use Kobens\Core\Db;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

final class Orders
{
    const TABLE_NAME = 'trader_simple_repeater';
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
            $select->columns(['exchange','symbol','status','buy_price','buy_amount','sell_price','sell_amount']);
            $select->where->in('status', ['buy_placed', 'sell_placed']);
        });
        foreach ($data as $row) {
            yield [
                'exchange' => $row->exchange,
                'symbol'   => $row->symbol,
                'side'     => $row->status === 'buy_placed' ? 'buy' : 'sell',
                'price'    => (float) ($row->status === 'buy_placed' ? $row->buy_price : $row->sell_price),
                'amount'   => (float) ($row->status === 'buy_placed' ? $row->buy_amount : $row->sell_amount),
            ];
        }
    }

    private function getTable() : TableGateway
    {
        return new TableGateway(self::TABLE_NAME, $this->db);
    }
}