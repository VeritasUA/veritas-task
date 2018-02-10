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
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'UTC',
  id_customer INT(11) NOT NULL,
  total DECIMAL(10, 2) DEFAULT 0.00,
  status TINYINT(1) UNSIGNED DEFAULT 0 COMMENT '0 - new; 1 - success; 2 - decline; 3 - cancel;',
  PRIMARY KEY (id),
  INDEX IDX_customer_order (id_customer),
  INDEX IDX_customer_order_created (created),
  INDEX IDX_customer_order_status (status),
  INDEX IDX_customer_order2 (id_customer, status, total),
  CONSTRAINT FK_customer_order_id_customer FOREIGN KEY (id_customer)
    REFERENCES customer(id) ON DELETE RESTRICT ON UPDATE CASCADE
)
ENGINE = INNODB;
```

топ 500 пользователей с максимальным суммарным тоталом (status заказа должен быть success)
```
SELECT c.*, orders.order_total
  FROM customer c
  INNER JOIN (
      SELECT co.id_customer, SUM(co.total) AS order_total
      FROM customer_order co
      FORCE INDEX (IDX_customer_order2) 
      WHERE co.status = 1
      GROUP BY co.id_customer
      ORDER BY order_total DESC
      LIMIT 500
    ) orders ON orders.id_customer = c.id
```

топ 500 пользователей за последний год, у которых нет ни одного заказа со статусом success, сортировка по дате регистрации
```
SELECT * FROM (
  SELECT DISTINCT(c.id), c.created, c.email
  FROM customer c
  WHERE c.created > DATE_SUB(NOW(), INTERVAL 1 YEAR) AND NOT EXISTS (SELECT * from customer_order co1 WHERE co1.status = 1 AND co1.id_customer = c.id)
  LIMIT 500
) customers
  ORDER BY customers.created DESC
```

топ 500 заказов, созданных в будний день за последние 3 месяца (вывести orderid, email, date), сортировка по дате заказа
```
SELECT co.id AS orderid, c.email, co.created AS date
  FROM customer_order co
  LEFT JOIN customer c ON co.id_customer = c.id
  WHERE co.created > DATE_SUB(NOW(), INTERVAL 3 MONTH) AND WEEKDAY(co.created) IN (0,1,2,3,4)
  ORDER BY co.created DESC
  LIMIT 500
```