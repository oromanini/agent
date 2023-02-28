<?php

namespace App\Services;

class InstallationService extends AfterSalesProcessService
{
    public const CHECKLIST_ITEM_WAITING_DELIVERY = 'Aguardando entrega';
    public const CHECKLIST_ITEM_WAITING_FOR_APPOINTMENT = 'Aguardando agendamento';
    public const CHECKLIST_ITEM_APPOINTMENT_MADE = 'Agendamento realizado';
    public const CHECKLIST_ITEM_CA_PURCHASED = 'C.A Comprado';
    public const CHECKLIST_ITEM_INSTALLATION_DONE = 'Instalação Concluída';

    public static function getChecklist(): string
    {
        return json_encode([
            self::CHECKLIST_ITEM_WAITING_DELIVERY => false,
            self::CHECKLIST_ITEM_WAITING_FOR_APPOINTMENT => false,
            self::CHECKLIST_ITEM_APPOINTMENT_MADE => false,
            self::CHECKLIST_ITEM_CA_PURCHASED => false,
            self::CHECKLIST_ITEM_INSTALLATION_DONE => false,
        ]);
    }
}
