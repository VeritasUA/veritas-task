<?php

require_once(__DIR__ . '/vendor/autoload.php');

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = 'home';
}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="web/css/normalize.css">
    <link rel="stylesheet" href="web/css/skeleton.css">
    <link rel="stylesheet" href="web/css/site.css">
</head>
<body>
<header>

</header>
<?php

$stack = \app\components\DbUtils::enableQueryStats();
$rows = [];
$title = 'Записей в таблице "customer": 1 000 000. Записей в таблице "customer_order": 1 500 000';
switch ($action) {
    case 'home':
        break;
    case 'top-by-orders-total':
        $rows = \app\models\Customer::getTopByOrdersTotal();
        $title = 'Топ 500 пользователей с максимальным суммарным тоталом (status заказа должен быть success)';
        break;
    case 'work-days-orders':
        $rows = \app\models\CustomerOrder::getWorkDaysOrders();
        $title = 'Топ 500 заказов, созданных в будний день за последние 3 месяца (вывести orderid, email, date),
            сортировка по дате заказа';
        break;
    case 'without-success-orders':
        $rows = \app\models\Customer::getWithoutSuccessOrders();
        $title = 'Топ 500 пользователей за последний год, у которых нет ни одного заказа со статусом success, 
            сортировка по дате регистрации';
        break;
    default:
        echo '<h1>Page not found</h1>';
}
$tableHeader = \app\components\RenderUtils::getTableHeader($rows);
$queryStats = \app\components\DbUtils::getQueryStats($stack);
?>

<div class="container">
    <div class="row">
        <div class="twelve columns logo">
            <img src="web/img/logo.jpg" alt="">
        </div>
    </div>
    <div class="row">
        <div class="twelve columns buttons text-center">
            <a class="button button-primary" href="?action=top-by-orders-total">Top customers by orders total</a>
            <a class="button button-primary" href="?action=work-days-orders">Work days orders</a>
            <a class="button button-primary" href="?action=without-success-orders">Customers without success orders</a>
        </div>
    </div>
    <div class="row text-center">
        <hr />
        <h6><b><?= $title ?></b></h6>
    </div>
    <div class="row">
        <div class="eight columns">
            <h4>Query results</h4>
            <?= \app\components\RenderUtils::renderTable($tableHeader, $rows) ?>
        </div>
        <div class="four columns">
            <h4>Doctrine query stats</h4>
            <?= \app\components\RenderUtils::renderQueryStats($queryStats) ?>
        </div>
    </div>
</div>
</body>
</html>
