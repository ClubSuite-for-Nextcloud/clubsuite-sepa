<?php
namespace OCA\ClubSuiteSepa\Db;

use DateTimeImmutable;

class PaymentRunEntity {
    private ?int $id = null;
    private DateTimeImmutable $date;
    private ?string $description = null;
    private ?string $sequenceType = 'RCUR';
    private ?string $creditorName = null;
    private ?string $creditorIBAN = null;
    private ?string $creditorBIC = null;

    public function __construct(DateTimeImmutable $date) {
        $this->date = $date;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
    public function getDate(): DateTimeImmutable { return $this->date; }
    public function setDate(DateTimeImmutable $d): void { $this->date = $d; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): void { $this->description = $d; }
    public function getSequenceType(): ?string { return $this->sequenceType; }
    public function setSequenceType(?string $s): void { $this->sequenceType = $s; }
    public function getCreditorName(): ?string { return $this->creditorName; }
    public function setCreditorName(?string $n): void { $this->creditorName = $n; }
    public function getCreditorIBAN(): ?string { return $this->creditorIBAN; }
    public function setCreditorIBAN(?string $i): void { $this->creditorIBAN = $i; }
    public function getCreditorBIC(): ?string { return $this->creditorBIC; }
    public function setCreditorBIC(?string $b): void { $this->creditorBIC = $b; }
}
