<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>REÇU DE RENDEZ-VOUS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            padding: 30px;
            background-color: #fff;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* Header with table */
        .header-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .logo-cell {
            width: 80px;
        }
        .logo-container {
            width: 80px;
            height: 80px;
        }
        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .company-cell {
            text-align: right;
        }
        .company-info {
            text-align: right;
            font-size: 10px;
            line-height: 1.4;
        }
        .company-name {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 3px;
        }
        .line {
            border-top: 1px solid #000;
            margin: 20px 0;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }
        .text {
            font-size: 11px;
            line-height: 1.5;
            text-align: justify;
            margin-bottom: 15px;
        }
        .bold {
            font-weight: bold;
        }
        .property-details {
            margin: 20px 0;
            padding-left: 20px;
        }

        /* ========== SIGNATURE SECTION: SAME AS BON PRE RESERVATION ========== */
        .signature-container {
            width: 100%;
            margin-top: 80px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            vertical-align: bottom;
            padding: 0;
        }
        .signature-left {
            text-align: center;
            padding-right: 20px;
        }
        .signature-right {
            text-align: center;
            padding-left: 20px;
        }
        .signature-line {
            border-top: 1px solid #000000;
            padding-top: 10px;
            min-height: 60px;
        }
        /* Force table cells to have equal height */
        .signature-table td {
            height: 80px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 60px;
            color: #7F8C8D;
            padding-top: 8px;
            border-top: 1px solid #E4E4E4;
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <div class="logo-container">
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" alt="Logo">
                        @endif
                    </div>
                </td>
                <td class="company-cell">
                    <div class="company-info">
                        <div class="company-name">{{ $societe['raison_sociale'] ?? 'Société' }}</div>
                        @if(!empty($societe['adresse']))
                            <div>Adresse: {{ $societe['adresse'] }}</div>
                        @endif
                        @if(!empty($societe['tel']))
                            <div>Tél: {{ $societe['tel'] }}</div>
                        @endif
                        @if(!empty($societe['email']))
                            <div>Email: {{ $societe['email'] }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>


        <!-- TITLE -->
        <div class="title">REÇU DE RENDEZ-VOUS</div>

        <!-- CONTENT -->
        <div class="text">
            La société <span class="bold">{{ $societe['raison_sociale'] ?? 'Société' }}</span>,
            confirme le rendez-vous du bien décrit ci-dessous :
        </div>

        <div class="property-details">
            <div class="text">
                <span class="bold">• N° Dossier :</span> {{ $code_reservation }}
            </div>
            <div class="text">
                <span class="bold">• Référence du bien :</span> {{ $bien_propriete }}
            </div>
            <div class="text">
                <span class="bold">• Type de Rendez-vous :</span> {{ $type_rdv }}
            </div>
            <div class="text">
                <span class="bold">• Date du rendez-vous :</span> {{ $formatDate($date_rdv) }}
            </div>
            @if(!empty($num_recu))
            <div class="text">
                <span class="bold">• N° Reçu :</span> {{ $num_recu }}
            </div>
            @endif
        </div>

        <div class="text">
            Ce reçu confirme la prise de rendez-vous pour la visite du bien immobilier.
            Le client s'engage à se présenter à l'heure convenue.
        </div>

        <div class="text">
            Fait à {{ $societe['ville'] ?? '............' }}, le {{ $currentDate }}
        </div>

        <!-- ========== SIGNATURE SECTION: SAME AS BON PRE RESERVATION ========== -->
        <div class="signature-container">
            <table class="signature-table">
                <tr>
                    <td class="signature-left">
                        <div class="signature-line">
                            Signature du Client<br>
                            CIN / Passeport
                        </div>
                    </td>
                    <td class="signature-right">
                        <div class="signature-line">
                            Signature de la Société<br>
                            <strong>{{ $societe['raison_sociale'] ?? 'Société' }}</strong><br>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            Document généré le {{ $currentDate }} — {{ $societe['raison_sociale'] ?? 'Société' }} —
            Tous droits réservés {{ date('Y') }}
        </div>

    </div>
</body>
</html>
