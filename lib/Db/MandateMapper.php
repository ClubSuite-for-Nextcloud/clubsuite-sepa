<?php
namespace OCA\ClubSuiteSepa\Db;

class MandateMapper {
    private $connection;

    public function __construct($connection) { $this->connection = $connection; }

    public function findAll(): array {
        $sql = 'SELECT * FROM `*PREFIX*sepa_mandate` ORDER BY `user_id` ASC';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $res = [];
        while ($row = $stmt->fetch()) {
            $m = new MandateEntity($row['user_id'], $row['iban'], $row['mandate_id']);
            $m->setId((int)$row['id']);
            $m->setBic($row['bic'] ?? null);
            if (!empty($row['signature_date'])) $m->setSignatureDate(new \DateTimeImmutable($row['signature_date']));
            $res[] = $m;
        }
        return $res;
    }

    /**
     * Paginated listing for mandates.
     * @return array ['total'=>int,'rows'=>MandateEntity[]]
     */
    public function findAllPaginated(int $limit = 50, int $offset = 0, string $sort = 'user_id', string $order = 'ASC'): array {
        $allowed = ['user_id','mandate_id','signature_date'];
        if (!in_array($sort, $allowed, true)) { $sort = 'user_id'; }
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $countSql = 'SELECT COUNT(*) as c FROM `*PREFIX*sepa_mandate`';
        $total = (int)$this->connection->query($countSql)->fetch()['c'];

        $sql = 'SELECT `id`,`user_id`,`iban`,`bic`,`mandate_id`,`signature_date` FROM `*PREFIX*sepa_mandate` ORDER BY `' . $sort . '` ' . $order . ' LIMIT ? OFFSET ?';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([(int)$limit, (int)$offset]);
        $rows = [];
        while ($row = $stmt->fetch()) {
            $m = new MandateEntity($row['user_id'], $row['iban'], $row['mandate_id']);
            $m->setId((int)$row['id']);
            $m->setBic($row['bic'] ?? null);
            if (!empty($row['signature_date'])) $m->setSignatureDate(new \DateTimeImmutable($row['signature_date']));
            $rows[] = $m;
        }
        return ['total'=>$total,'rows'=>$rows];
    }

    public function findById(int $id): ?MandateEntity {
        $sql = 'SELECT * FROM `*PREFIX*sepa_mandate` WHERE `id` = ? LIMIT 1';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $m = new MandateEntity($row['user_id'], $row['iban'], $row['mandate_id']);
        $m->setId((int)$row['id']);
        $m->setBic($row['bic'] ?? null);
        if (!empty($row['signature_date'])) $m->setSignatureDate(new \DateTimeImmutable($row['signature_date']));
        return $m;
    }

    public function findByUserId(string $userId): ?MandateEntity {
        $sql = 'SELECT * FROM `*PREFIX*sepa_mandate` WHERE `user_id` = ? AND `status` = \'active\' LIMIT 1';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $m = new MandateEntity($row['user_id'], $row['iban'], $row['mandate_id']);
        $m->setId((int)$row['id']);
        $m->setBic($row['bic'] ?? null);
        if (!empty($row['signature_date'])) $m->setSignatureDate(new \DateTimeImmutable($row['signature_date']));
        return $m;
    }

    public function delete(int $id): void {
        $sql = 'DELETE FROM `*PREFIX*sepa_mandate` WHERE `id` = ?';
        $this->connection->prepare($sql)->execute([$id]);
    }
}
