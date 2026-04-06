<?php
namespace OCA\ClubSuiteSepa\Service;

use OCA\ClubSuiteSepa\Db\PaymentRunMapper;
use OCA\ClubSuiteSepa\Db\PaymentMapper;
use OCA\ClubSuiteSepa\Db\MandateMapper;
use OCA\ClubSuiteSepa\Db\PaymentRunEntity;
use OCA\ClubSuiteSepa\Db\PaymentEntity;
use OCA\ClubSuiteSepa\Db\MandateEntity;
use DateTimeImmutable;

class SepaXmlService {
    private PaymentRunMapper $runMapper;
    private PaymentMapper $paymentMapper;
    private MandateMapper $mandateMapper;

    public function __construct(PaymentRunMapper $runMapper, PaymentMapper $paymentMapper, MandateMapper $mandateMapper) {
        $this->runMapper = $runMapper;
        $this->paymentMapper = $paymentMapper;
        $this->mandateMapper = $mandateMapper;
    }

    /**
     * Generate pain.008.001.02 XML for SEPA Direct Debit
     * Follows ISO 20022 standard for SEPA Credit Transfer
     */
    public function generatePain008(int $runId): string {
        $run = $this->runMapper->findById($runId);
        if ($run === null) {
            throw new \InvalidArgumentException('Run not found');
        }
        $payments = $this->paymentMapper->findByRun($runId);

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.008.001.02"></Document>');
        
        $xml->addChild('GrpHdr');
        $xml->GrpHdr->addChild('MsgId', $this->generateMessageId($runId));
        $xml->GrpHdr->addChild('CreDtTm', (new DateTimeImmutable())->format('Y-m-d\TH:i:s\Z'));
        $xml->GrpHdr->addChild('NbOfTxs', (string)count($payments));
        $xml->GrpHdr->addChild('CtrlSum', $this->calculateControlSum($payments));
        
        $xml->GrpHdr->addChild('InitgPty');
        $xml->GrpHdr->InitgPty->addChild('Nm', $this->getCreditorName());

        $pmtInf = $xml->addChild('PmtInf');
        $pmtInf->addChild('PmtInfId', 'PAY-' . $runId);
        $pmtInf->addChild('PmtMtd', 'DD');
        $pmtInf->addChild('BtchBookg', 'true');
        $pmtInf->addChild('NbOfTxs', (string)count($payments));
        $pmtInf->addChild('CtrlSum', $this->calculateControlSum($payments));
        
        $pmtInf->addChild('PmtTpInf');
        $pmtInf->PmtTpInf->addChild('SvcLvl');
        $pmtInf->PmtTpInf->SvcLvl->addChild('Cd', 'SEPA');
        $pmtInf->PmtTpInf->addChild('LclInstrm');
        $pmtInf->PmtTpInf->LclInstrm->addChild('Cd', 'CORE');
        $pmtInf->PmtTpInf->addChild('SeqTp', $run->getSequenceType() ?? 'RCUR');

        $pmtInf->addChild('ReqdColltnDt', $run->getDate()->format('Y-m-d'));
        
        $pmtInf->addChild('Cdtr');
        $pmtInf->Cdtr->addChild('Nm', $this->getCreditorName());
        
        $pmtInf->addChild('CdtrAcct');
        $pmtInf->CdtrAcct->addChild('Id');
        $pmtInf->CdtrAcct->Id->addChild('IBAN', $this->getCreditorIBAN());
        
        $pmtInf->addChild('CdtrAgt');
        $pmtInf->CdtrAgt->addChild('FinInstnId');
        $pmtInf->CdtrAgt->FinInstnId->addChild('BIC', $this->getCreditorBIC() ?? '');
        
        $pmtInf->addChild('ChrgBr', 'SLEV');

        foreach ($payments as $p) {
            $mandate = $this->mandateMapper->findByUserId($p->getUserId());
            if ($mandate === null) {
                continue;
            }

            $ddTx = $pmtInf->addChild('DrctDbtTx');
            $ddTx->addChild('PmtId');
            $ddTx->PmtId->addChild('EndToEndId', 'E2E-' . $p->getId());
            
            $ddTx->addChild('InstdAmt', number_format($p->getAmount(), 2, '.', ''));
            $ddTx->InstdAtr->addChild('Amt');
            $ddTx->InstdAmt->addAttribute('Ccy', 'EUR');
            
            $ddTx->addChild('DrctDbtTx');
            $ddTx->DrctDbtTx->addChild('MndtRltdInf');
            $ddTx->DrctDbtTx->MndtRltdInf->addChild('MndtId', $mandate->getMandateId());
            $ddTx->DrctDbtTx->MndtRltdInf->addChild('DtOfSgntr', $mandate->getSignatureDate()?->format('Y-m-d'));
            $ddTx->DrctDbtTx->MndtRltdInf->addChild('AmdmntInd', 'false');
            
            $ddTx->addChild('RmtInf');
            $ddTx->RmtInf->addChild('Ustrd', $p->getPurpose() ?? 'Mitgliedsbeitrag');
        }

        return $xml->asXML();
    }

    private function generateMessageId(int $runId): string {
        return 'MSG-' . $runId . '-' . (new DateTimeImmutable())->format('YmdHis');
    }

    private function calculateControlSum(array $payments): string {
        $sum = 0.0;
        foreach ($payments as $p) {
            $sum += $p->getAmount();
        }
        return number_format($sum, 2, '.', '');
    }

    private function getCreditorName(): string {
        return $_SERVER['CLUBSUITE_CREDITOR_NAME'] ?? 'Verein';
    }

    private function getCreditorIBAN(): string {
        return $_SERVER['CLUBSUITE_CREDITOR_IBAN'] ?? '';
    }

    private function getCreditorBIC(): ?string {
        return $_SERVER['CLUBSUITE_CREDITOR_BIC'] ?? null;
    }
}
