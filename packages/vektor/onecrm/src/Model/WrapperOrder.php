<?php

namespace Vektor\OneCRM\Model;

use Vektor\OneCRM\OneCRM;
use Vektor\OneCRM\OneCRMModel;

class WrapperOrder
{
    public $crm;

    public $crm_model;

    public $_sales_order;

    public $_tax_code;

    public function __construct()
    {
        $this->crm = new OneCRM;
        $this->crm_model = new OneCRMModel;

        $this->_sales_order = new SalesOrder;
        $this->_tax_code = new TaxCode;

        return $this;
    }

    public function fill($data = [])
    {
        return $this->_sales_order->fill($data);
    }

    public function toArray()
    {
        return $this->_sales_order->toArray();
    }

    public function persist()
    {
        $response = null;
        $lines = $this->_sales_order->lines;

        if (!empty($lines)) {
            unset($this->_sales_order->lines);

            $sales_order_response = $this->_sales_order->persist();

            if ($sales_order_response) {
                $response = $sales_order_response;
                $response['lines'] = [];

                $_sales_order_line_group = new SalesOrderLineGroup;

                $_sales_order_line_group->fill([
                    'parent_id' => $sales_order_response['id'],
                    'cost' => $sales_order_response['pretax'],
                    'subtotal' => $sales_order_response['subtotal'],
                    'total' => $sales_order_response['amount'],
                ]);

                $sales_order_line_group_response = $_sales_order_line_group->persist();

                if ($sales_order_line_group_response) {
                    $line_position = 0;

                    foreach ($lines as $line) {
                        $_sales_order_line = new SalesOrderLine;

                        $sales_order_line_data = [
                            'sales_orders_id' => $sales_order_response['id'],
                            'line_group_id' => $sales_order_line_group_response['id'],
                            'name' => $line['name'],
                            'quantity' => $line['quantity'],
                            'unit_price' => $line['unit_price'],
                            'std_unit_price' => $line['std_unit_price'],
                            'ext_price' => $line['ext_price'],
                            'net_price' => $line['net_price'],
                            'tax_class_id' => isset($line['tax_class_id']) && !empty($line['tax_class_id']) ? $line['tax_class_id'] : $this->_tax_code->get(),
                            'position' => strval($line_position),
                        ];

                        if (isset($line['cost_price'])) {
                            $sales_order_line_data['cost_price'] = $line['cost_price'];
                        }

                        if (isset($line['list_price'])) {
                            $sales_order_line_data['list_price'] = $line['list_price'];
                        }

                        if (isset($line['related_type'])) {
                            $sales_order_line_data['related_type'] = $line['related_type'];
                        }

                        if (isset($line['related_id'])) {
                            $sales_order_line_data['related_id'] = $line['related_id'];
                        }

                        if (isset($line['mfr_part_no'])) {
                            $sales_order_line_data['mfr_part_no'] = $line['mfr_part_no'];
                        }

                        $_sales_order_line->fill($sales_order_line_data);

                        $sales_order_line_response = $_sales_order_line->persist();

                        if ($sales_order_line_response) {
                            $line_position++;

                            if (isset($line['adjustments']) && !empty($line['adjustments'])) {
                                foreach ($line['adjustments'] as $line_adjustment) {
                                    $_sales_order_adjustment = new SalesOrderAdjustment;

                                    $_sales_order_adjustment->fill([
                                        'sales_orders_id' => $sales_order_response['id'],
                                        'line_group_id' => $sales_order_line_group_response['id'],
                                        'line_id' => $sales_order_line_response['id'],
                                        'name' => $line_adjustment['name'],
                                        'type' => 'ProductAttributes',
                                        'related_id' => $line_adjustment['id'],
                                        'related_type' => 'ProductAttributes',
                                        'position' => strval($line_position),
                                    ]);

                                    $sales_order_adjustment_response = $_sales_order_adjustment->persist();

                                    if ($sales_order_adjustment_response) {
                                        $line_position++;

                                        if (!isset($sales_order_line_response['adjustments'])) {
                                            $sales_order_line_response['adjustments'] = [];
                                        }

                                        $sales_order_line_response['adjustments'][] = $sales_order_adjustment_response;
                                    }
                                }
                            }

                            if ($line['id'] == 'shipping' && isset($line['comment']) && !empty($line['comment'])) {
                                $_sales_order_comment = new SalesOrderComment;

                                $_sales_order_comment->fill([
                                    'sales_orders_id' => $sales_order_response['id'],
                                    'line_group_id' => $sales_order_line_group_response['id'],
                                    'name' => $line['name'],
                                    'body' => $line['comment'],
                                    'position' => strval($line_position),
                                ]);

                                $sales_order_comment_response = $_sales_order_comment->persist();

                                if ($sales_order_comment_response) {
                                    $line_position++;

                                    $sales_order_line_response['comment'] = $sales_order_comment_response;
                                }
                            }

                            $response['lines'][] = $sales_order_line_response;
                        }
                    }
                }
            }

            $this->_sales_order->lines = $lines;
        }

        return $response;
    }
}
