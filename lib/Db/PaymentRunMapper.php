<?php
namespace OCA\ClubSuiteSepa\Db;

use DateTimeImmutable;

class PaymentRunMapper {
    private $connection;

    public function __construct($connection) { $this->connection = $connection; }

    public function findAll(): array {
        $sql = 'SELECT * FROM `*PREFIX*sepa_payment_run` ORDER BY `date` DESC';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $res = [];
        while ($row = $stmt->fetch()) {
            $r = new PaymentRunEntity(new DateTimeImmutable($row['date']));
            $r->setId((int)$row['id']);
            $r->setDescription($row['description'] ?? null);
            $res[] = $r;
        }
        return $res;
    }

    /**
     * Paginated listing for payment runs.
     */
    public function findAllPaginated(int $limit = 20, int $offset = 0, string $sort = 'date', string $order = 'DESC'): array {
        $allowed = ['date','id'];
        if (!in_array($sort, $allowed, true)) { $sort = 'date'; }
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $countSql = 'SELECT COUNT(*) as c FROM `*PREFIX*sepa_payment_run`';
        $total = (int)$this->connection->query($countSql)->fetch()['c'];

        $sql = 'SELECT `id`,`date`,`description` FROM `*PREFIX*sepa_payment_run` ORDER BY `' . $sort . '` ' . $order . ' LIMIT ? OFFSET ?';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([(int)$limit, (int)$offset]);
        $rows = [];
        while ($row = $stmt->fetch()) {
            $r = new PaymentRunEntity(new DateTimeImmutable($row['date']));
            $r->setId((int)$row['id']);
            $r->setDescription($row['description'] ?? null);
            $rows[] = $r;
        }
        return ['total'=>$total,'rows'=>$rows];
    }

    public function findById(int $id): ?PaymentRunEntity {
        $sql = 'SELECT * FROM `*PREFIX*sepa_payment_run` WHERE `id` = ? LIMIT 1';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $r = new PaymentRunEntity(new DateTimeImmutable($row['date']));
        $r->setId((int)$row['id']);
        $r->setDescription($row['description'] ?? null);
        return $r;
    }

    public function create(PaymentRunEntity $r): int {
        $sql = 'INSERT INTO `*PREFIX*sepa_payment_run` (`date`,`description`) VALUES (?,?)';
        $this->connection->prepare($sql)->execute([$r->getDate()->format('Y-m-d H:i:s'), $r->getDescription()]);
        return (int)$this->connection->lastInsertId();
    }
}
