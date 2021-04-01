<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class Status implements OptionSourceInterface
{
    const CREATED = 'created';
    const OFFERS_PREPARED = 'offers_prepared';
    const OFFER_SELECTED = 'offer_selected';
    const CONFIRMED = 'confirmed';
    const DISPATCH_BY_SENDER = 'dispatched_by_sender';
    const COLLECTED_FROM_SENDER = 'collected_from_sender';
    const TAKEN_BY_COURIER = 'taken_by_courier';
    const ADOPTED_AT_SOURCE_BRANCH = 'adopted_at_source_branch';
    const SENT_AT_SOURCE_BRANCH = 'sent_from_source_branch';
    const READY_TO_PICKUP_FROM_POK = 'ready_to_pickup_from_pok';
    const READY_TO_PICKUP_FROM_POK_REGISTRED = 'ready_to_pickup_from_pok_registered';
    const OVERSIZE = 'oversized';
    const ADOPTED_AT_SORTING_CENTER = 'adopted_at_sorting_center';
    const SENT_FROM_SORTING_CENTER = 'sent_from_sorting_center';
    const ADOPTED_AT_TARGET_BRANCH = 'adopted_at_target_branch';
    const OUT_OF_DELIVERY = 'out_for_delivery';
    const READY_TO_PICKUP = 'ready_to_pickup';
    const PICKUP_REMINDER_SENT = 'pickup_reminder_sent';
    const DELIVERED = 'delivered';
    const AVIZO = 'avizo';
    const CLAIMED = 'claimed';
    const RETURNED_TO_SENDER = 'returned_to_sender';
    const CANCEL = 'canceled';
    const OTHER = 'other';
    const DISPATCHED_BY_SENDER_TO_POK = 'dispatched_by_sender_to_pok';
    const OUT_FOR_DELIVERY_TO_ADDRESS = 'out_for_delivery_to_address';
    const PICKUP_REMINDER_SENT_ADDRESS = 'pickup_reminder_sent_address';
    const REJECT_BY_RECEIVER = 'rejected_by_receiver';
    const UNDELIVERED_WRONG_ADDRESS = 'undelivered_wrong_address';
    const UNDELIVERED_INCOMPLETE_ADDRESS = 'undelivered_incomplete_address';
    const UNDELIVERED_UNKNOWN_RECEIVER = 'undelivered_unknown_receiver';
    const UNDELIVERED_COD_CASH_RECEIVER = 'undelivered_cod_cash_receiver';
    const TAKEN_BY_COURIER_FROM_POK = 'taken_by_courier_from_pok';
    const UNDELIVERED = 'undelivered';
    const RETURN_PICKUP_CONFIRMATION_TO_SENDER = 'return_pickup_confirmation_to_sender';
    const READY_TO_PICKUP_FROM_BRANCH = 'ready_to_pickup_from_branch';
    const DELAY_IN_DELIVERY = 'delay_in_delivery';
    const REDIRECT_TO_BOX = 'redirect_to_box';
    const CANCELED_REDIRECT_TO_BOX = 'canceled_redirect_to_box';
    const READDRESSED = 'readdressed';
    const UNDELIVERED_NO_MAILBOX = 'undelivered_no_mailbox';
    const UNDELIVERED_NOT_LIVE_ADDRESS = 'undelivered_not_live_address';
    const UNDELIVERED_LACK_OF_ACCESS_LETTERBOX = 'undelivered_lack_of_access_letterbox';
    const MISSING = 'missing';
    const STACK_IN_CUSTOMER_SERVICE_POINT = 'stack_in_customer_service_point';
    const STACK_PARCEL_PICKUP_TIME_EXPIRED = 'stack_parcel_pickup_time_expired';
    const UNSTACK_FROM_CUSTOMER_SERVICE_POINT = 'unstack_from_customer_service_point';
    const COURIER_AVIZO_IN_CUSTOMER_SERVICE_POINT = 'courier_avizo_in_customer_service_point';
    const TAKEN_BY_COURIER_FROM_CUSTOMER_SERVICE_POINT = 'taken_by_courier_from_customer_service_point';
    const STACK_IN_BOX_MACHINE = 'stack_in_box_machine';
    const UNSTACK_FROM_BOX_MACHINE = 'unstack_from_box_machine';
    const STACK_PARCEL_IN_BOX_MACHINE_PICKUP_TIME_EXPIRED = 'stack_parcel_in_box_machine_pickup_time_expired';

    const STATUS_LABEL = [
        'created' => 'Przesyłka utworzona.',
        'offers_prepared' => 'Przygotowano oferty.',
        'offer_selected' => 'Oferta wybrana.',
        'confirmed' => 'Przygotowana przez Nadawcę.',
        'dispatched_by_sender' => 'Paczka nadana w paczkomacie.',
        'collected_from_sender' => 'Odebrana od klienta',
        'taken_by_courier' => 'Odebrana od Nadawcy.',
        'adopted_at_source_branch' => 'Przyjęta w oddziale InPost.',
        'sent_from_source_branch' => 'W trasie.',
        'ready_to_pickup_from_pok' => 'Czeka na odbiór w PaczkoPunkcie.',
        'ready_to_pickup_from_pok_registered' => 'Czeka na odbiór w PaczkoPunkcie.',
        'oversized' => 'Przesyłka ponadgabarytowa.',
        'adopted_at_sorting_center' => 'Przyjęta w Sortowni.',
        'sent_from_sorting_center' => 'Wysłana z Sortowni.',
        'adopted_at_target_branch' => 'Przyjęta w Oddziale Docelowym.',
        'out_for_delivery' => 'Przekazano do doręczenia.',
        'ready_to_pickup' => 'Umieszczona w Paczkomacie (odbiorczym).',
        'pickup_reminder_sent' => 'Przypomnienie o czekającej paczce.',
        'delivered' => 'Dostarczona.',
        'pickup_time_expired' => 'Upłynął termin odbioru.',
        'avizo' => 'Powrót do oddziału.',
        'claimed' => 'Zareklamowana w Paczkomacie.',
        'returned_to_sender' => 'Zwrot do nadawcy.',
        'canceled' => 'Anulowano etykietę.',
        'other' => 'Inny status.',
        'dispatched_by_sender_to_pok' => 'Nadana w PaczkoPunkcie.',
        'out_for_delivery_to_address' => 'W doręczeniu.',
        'pickup_reminder_sent_address' => 'W doręczeniu.',
        'rejected_by_receiver' => 'Odmowa przyjęcia.',
        'undelivered_wrong_address' => 'Brak możliwości doręczenia.',
        'undelivered_incomplete_address' => 'Brak możliwości doręczenia.',
        'undelivered_unknown_receiver' => 'Brak możliwości doręczenia.',
        'undelivered_cod_cash_receiver' => 'Brak możliwości doręczenia.',
        'taken_by_courier_from_pok' => 'W drodze do oddziału nadawczego InPost.',
        'undelivered' => 'Przekazanie do magazynu przesyłek niedoręczalnych.',
        'return_pickup_confirmation_to_sender' => 'Przygotowano dokumenty zwrotne.',
        'ready_to_pickup_from_branch' => 'Paczka nieodebrana – czeka w Oddziale',
        'delay_in_delivery' => 'Możliwe opóźnienie doręczenia.',
        'redirect_to_box' => 'Przekierowano do Paczkomatu.',
        'canceled_redirect_to_box' => 'Anulowano przekierowanie.',
        'readdressed' => 'Przekierowano na inny adres.',
        'undelivered_no_mailbox' => 'Brak możliwości doręczenia.',
        'undelivered_not_live_address' => 'Brak możliwości doręczenia.',
        'undelivered_lack_of_access_letterbox' => 'Brak możliwości doręczenia.',
        'missing' => 'Zaginiona.',
        'stack_in_customer_service_point' => 'Paczka magazynowana w PaczkoPunkcie.',
        'stack_parcel_pickup_time_expired' => 'Upłynął termin odbioru paczki magazynowanej.',
        'unstack_from_customer_service_point' => 'W drodze do wybranego Paczkomatu.',
        'courier_avizo_in_customer_service_point' => 'Oczekuje na odbiór.',
        'taken_by_courier_from_customer_service_point' => 'Zwrócona do nadawcy.',
        'stack_in_box_machine' => 'Paczka magazynowana w Paczkomacie tymczasowym',
        'unstack_from_box_machine' => 'Paczka w drodze do pierwotnie wybranego Paczkomatu',
        'stack_parcel_in_box_machine_pickup_time_expired' => 'Upłynął termin odbioru paczki magazynowanej',
    ];

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        $statuses = [
            ['value' => self::CREATED, 'label' => self::STATUS_LABEL[self::CREATED]],
            ['value' => self::OFFERS_PREPARED, 'label' => self::STATUS_LABEL[self::OFFERS_PREPARED]],
            ['value' => self::OFFER_SELECTED, 'label' => self::STATUS_LABEL[self::OFFER_SELECTED]],
            ['value' => self::CONFIRMED, 'label' => self::STATUS_LABEL[self::CONFIRMED]],
            ['value' => self::DISPATCH_BY_SENDER, 'label' => self::STATUS_LABEL[self::DISPATCH_BY_SENDER]],
            ['value' => self::COLLECTED_FROM_SENDER, 'label' => self::STATUS_LABEL[self::COLLECTED_FROM_SENDER]],
            ['value' => self::TAKEN_BY_COURIER, 'label' => self::STATUS_LABEL[self::TAKEN_BY_COURIER]],
            ['value' => self::ADOPTED_AT_SOURCE_BRANCH, 'label' => self::STATUS_LABEL[self::ADOPTED_AT_SOURCE_BRANCH]],
            ['value' => self::SENT_AT_SOURCE_BRANCH, 'label' => self::STATUS_LABEL[self::SENT_AT_SOURCE_BRANCH]],
            ['value' => self::READY_TO_PICKUP_FROM_POK, 'label' => self::STATUS_LABEL[self::READY_TO_PICKUP_FROM_POK]],
            ['value' => self::READY_TO_PICKUP_FROM_POK_REGISTRED, 'label' => self::STATUS_LABEL[self::READY_TO_PICKUP_FROM_POK_REGISTRED]],
            ['value' => self::OVERSIZE, 'label' => self::STATUS_LABEL[self::OVERSIZE]],
            ['value' => self::ADOPTED_AT_SORTING_CENTER, 'label' => self::STATUS_LABEL[self::ADOPTED_AT_SORTING_CENTER]],
            ['value' => self::SENT_FROM_SORTING_CENTER, 'label' => self::STATUS_LABEL[self::SENT_FROM_SORTING_CENTER]],
            ['value' => self::ADOPTED_AT_TARGET_BRANCH, 'label' => self::STATUS_LABEL[self::ADOPTED_AT_TARGET_BRANCH]],
            ['value' => self::OUT_OF_DELIVERY, 'label' => self::STATUS_LABEL[self::OUT_OF_DELIVERY]],
            ['value' => self::READY_TO_PICKUP, 'label' => self::STATUS_LABEL[self::READY_TO_PICKUP]],
            ['value' => self::PICKUP_REMINDER_SENT, 'label' => self::STATUS_LABEL[self::PICKUP_REMINDER_SENT]],
            ['value' => self::DELIVERED, 'label' => self::STATUS_LABEL[ self::DELIVERED]],
            ['value' => self::AVIZO, 'label' => self::STATUS_LABEL[self::AVIZO]],
            ['value' => self::CLAIMED, 'label' => self::STATUS_LABEL[self::CLAIMED]],
            ['value' => self::RETURNED_TO_SENDER, 'label' => self::STATUS_LABEL[self::RETURNED_TO_SENDER]],
            ['value' => self::CANCEL, 'label' => self::STATUS_LABEL[self::CANCEL]],
            ['value' => self::OTHER, 'label' => self::STATUS_LABEL[self::OTHER]],
            ['value' => self::DISPATCHED_BY_SENDER_TO_POK, 'label' => self::STATUS_LABEL[self::DISPATCHED_BY_SENDER_TO_POK]],
            ['value' => self::OUT_FOR_DELIVERY_TO_ADDRESS, 'label' => self::STATUS_LABEL[self::OUT_FOR_DELIVERY_TO_ADDRESS]],
            ['value' => self::PICKUP_REMINDER_SENT_ADDRESS, 'label' => self::STATUS_LABEL[self::PICKUP_REMINDER_SENT_ADDRESS]],
            ['value' => self:: REJECT_BY_RECEIVER, 'label' => self::STATUS_LABEL[self:: REJECT_BY_RECEIVER]],
            ['value' => self::UNDELIVERED_WRONG_ADDRESS, 'label' => self::STATUS_LABEL[self::UNDELIVERED_WRONG_ADDRESS]],
            ['value' => self::UNDELIVERED_INCOMPLETE_ADDRESS, 'label' => self::STATUS_LABEL[self::UNDELIVERED_INCOMPLETE_ADDRESS]],
            ['value' => self::UNDELIVERED_UNKNOWN_RECEIVER, 'label' => self::STATUS_LABEL[self::UNDELIVERED_UNKNOWN_RECEIVER]],
            ['value' => self::UNDELIVERED_COD_CASH_RECEIVER, 'label' => self::STATUS_LABEL[self::UNDELIVERED_COD_CASH_RECEIVER]],
            ['value' => self::TAKEN_BY_COURIER_FROM_POK, 'label' => self::STATUS_LABEL[self::TAKEN_BY_COURIER_FROM_POK]],
            ['value' => self::UNDELIVERED, 'label' => self::STATUS_LABEL[self::UNDELIVERED]],
            ['value' => self::RETURN_PICKUP_CONFIRMATION_TO_SENDER, 'label' => self::STATUS_LABEL[self::RETURN_PICKUP_CONFIRMATION_TO_SENDER]],
            ['value' => self::READY_TO_PICKUP_FROM_BRANCH, 'label' => self::STATUS_LABEL[self::READY_TO_PICKUP_FROM_BRANCH]],
            ['value' => self::DELAY_IN_DELIVERY, 'label' => self::STATUS_LABEL[self::DELAY_IN_DELIVERY]],
            ['value' => self::REDIRECT_TO_BOX, 'label' => self::STATUS_LABEL[self::REDIRECT_TO_BOX]],
            ['value' => self::CANCELED_REDIRECT_TO_BOX, 'label' => self::STATUS_LABEL[self::CANCELED_REDIRECT_TO_BOX]],
            ['value' => self::READDRESSED, 'label' => self::STATUS_LABEL[self::READDRESSED]],
            ['value' => self::UNDELIVERED_NO_MAILBOX, 'label' => self::STATUS_LABEL[self::UNDELIVERED_NO_MAILBOX]],
            ['value' => self::UNDELIVERED_NOT_LIVE_ADDRESS, 'label' => self::STATUS_LABEL[self::UNDELIVERED_NOT_LIVE_ADDRESS]],
            ['value' => self::UNDELIVERED_LACK_OF_ACCESS_LETTERBOX, 'label' => self::STATUS_LABEL[self::UNDELIVERED_LACK_OF_ACCESS_LETTERBOX]],
            ['value' => self::MISSING, 'label' => self::STATUS_LABEL[self::MISSING]],
            ['value' => self::STACK_IN_CUSTOMER_SERVICE_POINT, 'label' => self::STATUS_LABEL[self::STACK_IN_CUSTOMER_SERVICE_POINT]],
            ['value' => self::STACK_PARCEL_PICKUP_TIME_EXPIRED, 'label' => self::STATUS_LABEL[self::STACK_PARCEL_PICKUP_TIME_EXPIRED]],
            ['value' => self::UNSTACK_FROM_CUSTOMER_SERVICE_POINT, 'label' => self::STATUS_LABEL[self::UNSTACK_FROM_CUSTOMER_SERVICE_POINT]],
            ['value' => self::COURIER_AVIZO_IN_CUSTOMER_SERVICE_POINT, 'label' => self::STATUS_LABEL[ self::COURIER_AVIZO_IN_CUSTOMER_SERVICE_POINT]],
            ['value' => self::TAKEN_BY_COURIER_FROM_CUSTOMER_SERVICE_POINT, 'label' => self::STATUS_LABEL[self::TAKEN_BY_COURIER_FROM_CUSTOMER_SERVICE_POINT]],
            ['value' => self::STACK_IN_BOX_MACHINE, 'label' => self::STATUS_LABEL[self::STACK_IN_BOX_MACHINE]],
            ['value' => self::UNSTACK_FROM_BOX_MACHINE, 'label' => self::STATUS_LABEL[self::UNSTACK_FROM_BOX_MACHINE]],
            ['value' => self::STACK_PARCEL_IN_BOX_MACHINE_PICKUP_TIME_EXPIRED, 'label' => self::STATUS_LABEL[self::STACK_PARCEL_IN_BOX_MACHINE_PICKUP_TIME_EXPIRED]]
        ];

        return $statuses;
    }

    /**
     * @param $status
     * @return \Magento\Framework\Phrase
     */
    public function getStatusLabel($status)
    {
        return (isset(self::STATUS_LABEL[$status])) ? __(self::STATUS_LABEL[$status]) : $status;
    }
}
