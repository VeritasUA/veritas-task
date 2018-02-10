<?php

namespace app\models;

use app\components\DateUtils;
use app\components\DB;

class Customer
{
    /**
     * топ 500 пользователей с максимальным суммарным тоталом (status заказа должен быть success)
     *
     * @return array
     */
    public static function getTopByOrdersTotal($limit = 500)
    {
        $sql = "SELECT c.created, c.firstname, c.lastname, c.email, orders.orders_total
                FROM customer c
                INNER JOIN (
                  SELECT co.id_customer, SUM(co.total) AS orders_total
                  FROM customer_order co
                  FORCE INDEX (IDX_customer_order2) 
                  WHERE co.status = :status_success
                  GROUP BY co.id_customer
                  ORDER BY orders_total DESC
                  LIMIT :limit
                ) orders ON orders.id_customer = c.id";


        $conn = DB::getInstance();

        $stmt = $conn->prepare($sql);
        $stmt->bindValue("status_success", CustomerOrder::STATUS_SUCCESS, \PDO::PARAM_INT);
        $stmt->bindValue("limit", $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * топ 500 пользователей за последний год, у которых нет ни одного заказа со статусом success,
     * сортировка по дате регистрации
     *
     * @return array
     */
    public static function getWithoutSuccessOrders($years = 1, $limit = 500)
    {
        $sql = "SELECT * FROM (
				  SELECT DISTINCT(c.id) AS id, c.created, c.email, c.firstname, c.lastname
				  FROM customer c
				  WHERE
					c.created > DATE_SUB(:now, INTERVAL :years YEAR)
					AND NOT EXISTS (SELECT * from customer_order co1 WHERE co1.status = :status_success AND co1.id_customer = c.id)
				  LIMIT :limit
				) customers
				ORDER BY customers.created DESC
				";


        $now = date(DateUtils::DATE_FORMAT_MYSQL_DATETIME);
        $conn = DB::getInstance();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("status_success", CustomerOrder::STATUS_SUCCESS, \PDO::PARAM_INT);
        $stmt->bindValue("years", $years, \PDO::PARAM_INT);
        $stmt->bindValue("now", $now, \PDO::PARAM_STR);
        $stmt->bindValue("limit", $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}