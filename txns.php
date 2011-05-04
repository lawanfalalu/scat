<?
require 'scat.php';

head("transactions");

$criteria= array();

$type= $_REQUEST['type'];
if ($type) {
  $criteria[]= "(type = '".$db->real_escape_string($type)."')";
}

$q= $_REQUEST['q'];
if ($q) {
  $criteria[]= "(person.name LIKE '%$q%'
             OR person.company LIKE '%$q%')";
}

if (empty($criteria)) {
  $criteria= '1=1';
} else {
  $criteria= join(' AND ', $criteria);
}

?>
<form method="get" action="txns.php">
<select name="type">
 <option value="">Any
 <option value="customer">Invoice
 <option value="vendor">Purchase Order
 <option value="internal">Internal
</select>
<input id="focus" type="text" name="q" value="<?=ashtml($q)?>">
<input type="submit" value="Search">
</form>
<br>
<form method="get" action="txn.php">
<select name="type">
 <option value="customer">Invoice
 <option value="vendor">Purchase Order
 <option value="internal">Internal
</select>
<input type="text" name="number" value="">
<input type="submit" value="Look Up">
</form>
<br>
<?

$q= "SELECT
            txn.type AS meta,
            CONCAT(txn.id, '|', type, '|', txn.number) AS Number\$txn,
            txn.created AS Created\$date,
            CONCAT(txn.person, '|', IFNULL(person.company,''),
                   '|', IFNULL(person.name,''))
              AS Person\$person,
            SUM(ordered) AS Ordered,
            SUM(shipped) AS Shipped,
            SUM(allocated) AS Allocated,
            CAST(SUM(IF(type = 'customer', -1, 1) * allocated *
                     CASE discount_type
                       WHEN 'percentage' THEN ROUND(retail_price *
                                                    ((100 - discount) / 100), 2)
                       WHEN 'relative' THEN (retail_price - discount) 
                       WHEN 'fixed' THEN (discount)
                       ELSE retail_price
                     END * 1.0975)
                 AS DECIMAL(9,2)) Total\$dollar,
            CAST((SELECT SUM(amount) FROM payment WHERE txn.id = payment.txn)
                 AS DECIMAL(9,2)) AS Paid\$dollar
       FROM txn
       LEFT JOIN txn_line ON (txn.id = txn_line.txn)
       LEFT JOIN person ON (txn.person = person.id)
      WHERE $criteria
      GROUP BY txn.id
      ORDER BY created DESC
      LIMIT 200";

dump_table($db->query($q));
dump_query($q);

foot();
