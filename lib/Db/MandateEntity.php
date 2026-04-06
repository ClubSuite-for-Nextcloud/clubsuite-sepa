<?php
namespace OCA\ClubSuiteSepa\Db;

use DateTimeImmutable;

class MandateEntity {
    private ?int $id = null;
    private string $userId;
    private string $iban;
    private ?string $bic = null;
    private string $mandateId;
    private ?DateTimeImmutable $signatureDate = null;

    public function __construct(string $userId, string $iban, string $mandateId) {
        $this->userId = $userId;
        $this->iban = $iban;
        $this->mandateId = $mandateId;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
    public function getUserId(): string { return $this->userId; }
    public function getIban(): string { return $this->iban; }
    public function setIban(string $iban): void { $this->iban = $iban; }
    public function getBic(): ?string { return $this->bic; }
    public function setBic(?string $bic): void { $this->bic = $bic; }
    public function getMandateId(): string { return $this->mandateId; }
    public function setMandateId(string $m): void { $this->mandateId = $m; }
    public function getSignatureDate(): ?DateTimeImmutable { return $this->signatureDate; }
    public function setSignatureDate(?DateTimeImmutable $d): void { $this->signatureDate = $d; }
}
