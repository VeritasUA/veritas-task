<?php

namespace app\models;

use app\components\DateUtils;
use app\components\DB;

class CustomerOrder
{
    const STATUS_NEW = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_DECLINE = 2;
    const STATUS_CANCEL = 3;

    /**
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_DECLINE => 'Declined',
            self::STATUS_CANCEL => 'Canceled',
        ];
    }

    /**
     * @param $status
     * @return string
     */
    public static function renderStatus($status)
    {
        $statusOptions = self::getStatusOptions();
        return isset($statusOptions[$status]) ? $statusOptions[$status] : 'Unknown';
    }

    /**
     * топ 500 заказов, созданных в будний день за последние 3 месяца (вывести orderid, email, date),
     * сортировка по дате заказа
     *
     * @param int $months month period to show orders
     * @return array
     */
    public static function getWorkDaysOrders($months = 3, $limit = 500)
    {
        $sql = "SELECT co.id AS orderid, c.email, co.created AS date
				  FROM customer_order co
				  LEFT JOIN customer c ON co.id_customer = c.id
				  WHERE co.created > DATE_SUB(:now, INTERVAL :months MONTH) AND WEEKDAY(co.created) IN (0,1,2,3,4)
				  ORDER BY co.created DESC
				  LIMIT :limit
				";


        $now = date(DateUtils::DATE_FORMAT_MYSQL_DATETIME);
        $conn = DB::getInstance();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("months", $months, \PDO::PARAM_INT);
        $stmt->bindValue("now", $now, \PDO::PARAM_STR);
        $stmt->bindValue("limit", $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}