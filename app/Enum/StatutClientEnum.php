<?php

namespace App\Enum;

enum StatutClientEnum: int
{
    case Suivi_Dossier = 0;
    case Nouvelle_Avance = 1;
    case Question_sur_avance = 2;
    case Suivi_de_paiement = 3;
    case Question_sur_documents = 4;
    case Suivi_livraison = 5;
    case Modification_contrat = 6;
    case Signature_contrat = 7;
    case Autre_Question = 8;
    case Creation_Reservation = 9;
    case Ajouter_Rdv = 10;
    case Signer_Attestation_Vente = 11;
    case Signer_Contrat_Vente = 12;
    case Remise_Cle = 13;
    case Desistement_dd = 14;
    case Desistement_dp_profit = 15;
    case Desistement_dp_co = 16;
    case Desistement_dp_partiel = 17;
    case Desistement_change_bien = 18;
    case Payer_penalite = 19;
    case Rembourser = 20;

    public function label(): string
    {
        return match($this) {
            self::Suivi_Dossier => 'Suivi Dossier',
            self::Nouvelle_Avance => 'Nouvelle Avance',
            self::Question_sur_avance => 'Question sur avance des travaux',
            self::Suivi_de_paiement => 'Suivi de paiement',
            self::Question_sur_documents => 'Question sur documents',
            self::Suivi_livraison => 'Suivi livraison',
            self::Modification_contrat => 'Modification contrat',
            self::Signature_contrat => 'Signature contrat',
            self::Autre_Question => 'Autre question',
            self::Creation_Reservation => 'Création réservation',
            self::Ajouter_Rdv => 'Ajouter RDV',
            self::Signer_Attestation_Vente => 'Signer attestation vente',
            self::Signer_Contrat_Vente => 'Signer contrat vente',
            self::Remise_Cle => 'Remise clés',
            self::Desistement_dd => 'Désistement DD',
            self::Desistement_dp_profit => 'Désistement DP profit',
            self::Desistement_dp_co => 'Désistement DP co-acquéreur',
            self::Desistement_dp_partiel => 'Désistement DP partiel',
            self::Desistement_change_bien => 'Désistement changement bien',
            self::Payer_penalite => 'Payer pénalité',
            self::Rembourser => 'Rembourser',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::Suivi_Dossier => 'Suivi général du dossier client',
            self::Nouvelle_Avance => 'Le client souhaite effectuer un nouveau paiement',
            self::Question_sur_avance => 'Le client a des questions sur l\'avancement des travaux',
            self::Suivi_de_paiement => 'Suivi des paiements en cours ou retardés',
            self::Question_sur_documents => 'Le client a des questions concernant les documents',
            self::Suivi_livraison => 'Question sur la date de livraison ou remise des clés',
            self::Modification_contrat => 'Demande de modification du contrat ou des termes',
            self::Signature_contrat => 'Suivi pour signature de contrat',
            self::Autre_Question => 'Autre type de question non spécifiée',
            self::Creation_Reservation => 'Processus de création de réservation',
            self::Ajouter_Rdv => 'Ajout d\'un rendez-vous avec le client',
            self::Signer_Attestation_Vente => 'Signature de l\'attestation de vente',
            self::Signer_Contrat_Vente => 'Signature du contrat de vente définitif',
            self::Remise_Cle => 'Remise des clés du bien au client',
            self::Desistement_dd => 'Désistement avec dépôt de garantie non restitué',
            self::Desistement_dp_profit => 'Désistement avec dépôt de garantie à partager selon profit',
            self::Desistement_dp_co => 'Désistement avec dépôt de garantie pour co-acquéreur',
            self::Desistement_dp_partiel => 'Désistement avec dépôt de garantie partiellement restitué',
            self::Desistement_change_bien => 'Désistement pour changement de bien',
            self::Payer_penalite => 'Paiement de pénalités pour retard',
            self::Rembourser => 'Processus de remboursement en cours',
        };
    }

    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = [
                'code' => $case->value,
                'label' => $case->label(),
                'description' => $case->description(),
            ];
        }
        return $array;
    }

    public static function getLabel(int $value): string
    {
        return self::tryFrom($value)?->label() ?? 'Statut inconnu';
    }

    public static function getDescription(int $value): string
    {
        return self::tryFrom($value)?->description() ?? 'Description non disponible';
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    public static function getByCategory(string $category): array
    {
        $categories = [
            'suivi' => [
                self::Suivi_Dossier,
                self::Suivi_de_paiement,
                self::Suivi_livraison,
            ],
            'paiement' => [
                self::Nouvelle_Avance,
                self::Suivi_de_paiement,
                self::Payer_penalite,
            ],
            'documentation' => [
                self::Question_sur_documents,
                self::Signature_contrat,
                self::Modification_contrat,
                self::Signer_Attestation_Vente,
                self::Signer_Contrat_Vente,
            ],
            'reservation' => [
                self::Creation_Reservation,
                self::Ajouter_Rdv,
            ],
            'desistement' => [
                self::Desistement_dd,
                self::Desistement_dp_profit,
                self::Desistement_dp_co,
                self::Desistement_dp_partiel,
                self::Desistement_change_bien,
            ],
            'finalisation' => [
                self::Remise_Cle,
                self::Rembourser,
            ],
            'autre' => [
                self::Question_sur_avance,
                self::Autre_Question,
            ],
        ];

        if (!isset($categories[$category])) {
            return [];
        }

        $result = [];
        foreach ($categories[$category] as $case) {
            $result[] = [
                'code' => $case->value,
                'label' => $case->label(),
                'description' => $case->description(),
            ];
        }
        return $result;
    }

   
}
