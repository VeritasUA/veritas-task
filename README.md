```
CREATE TABLE customer (
  id INT(11) NOT NULL AUTO_INCREMENT,
  created DATETIME NOT NULL COMMENT 'UTC',
  email VARCHAR(50) NOT NULL,
  firstname VARCHAR(80) NOT NULL,
  lastname VARCHAR(80) NOT NULL,
  PRIMARY KEY (id),
  INDEX IDX_customer_created (created),
  UNIQUE INDEX UK_customer_email (email)
)
ENGINE = INNODB;

CREATE TABLE customer_order (
  id int(11) NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'UTC',
  id_customer int(11) NOT NULL,
  total decimal(10, 2) DEFAULT 0.00,
  status tinyint(1) UNSIGNED DEFAULT 0 COMMENT '0 - new; 1 - success; 2 - decline; 3 - cancel;',
  PRIMARY KEY (id),
  INDEX IDX_customer_order2 (status, id_customer, total),
  INDEX IDX_customer_order3 (id_customer, status),
  INDEX IDX_customer_order4 (created, id_customer),
  CONSTRAINT FK_customer_order_id_customer FOREIGN KEY (id_customer)
  REFERENCES customer (id) ON DELETE RESTRICT ON UPDATE CASCADE
)
ENGINE = INNODB;
```

топ 500 пользователей с максимальным суммарным тоталом (status заказа должен быть success)
```
SELECT
  c.created,
  c.firstname,
  c.lastname,
  c.email,
  orders.orders_total
FROM customer c
INNER JOIN (
  SELECT co.id_customer, SUM(co.total) AS orders_total
  FROM customer_order co
  WHERE co.status = :status_success
  GROUP BY co.id_customer
  ORDER BY orders_total DESC
  LIMIT :limit
) orders ON orders.id_customer = c.id
```

топ 500 пользователей за последний год, у которых нет ни одного заказа со статусом success, сортировка по дате регистрации
```
  SELECT
    DISTINCT(c.id) AS id,
    c.created,
    c.email,
    c.firstname,
    c.lastname
  FROM customer c
  WHERE
    c.created > DATE_SUB(:now, INTERVAL :years YEAR)
    AND NOT EXISTS (
      SELECT id
      FROM customer_order co
      WHERE
        co.status = :status_success
        AND co.id_customer = c.id
    )
  ORDER BY c.created DESC
  LIMIT :limit
```

топ 500 заказов, созданных в будний день за последние 3 месяца (вывести orderid, email, date), сортировка по дате заказа
```
SELECT
  co.id AS order_id,
  c.email,
  co.created AS date,
  co.status
FROM customer_order co
LEFT JOIN customer c ON co.id_customer = c.id
WHERE
  co.created > DATE_SUB(:now, INTERVAL :months MONTH)
  AND WEEKDAY(co.created) IN (0,1,2,3,4)
ORDER BY co.created DESC
LIMIT :limit
```
