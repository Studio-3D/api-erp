<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-mail Programmé</title>
    <style>
        /* Styles généraux */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: #2c3e50;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
        }
        .content h1 {
            font-size: 22px;
            color: #2c3e50;
        }
        .content p {
            margin: 10px 0;
            font-size: 16px;
        }
        .footer {
            background: #2c3e50;
            color: #ffffff;
            padding: 10px 20px;
            text-align: center;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #3498db;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            Notification Importante
        </div>

        <!-- Content -->
        <div class="content">
            <h1>Bonjour, {{ $name }} !</h1>
            <p>Nous vous rappelons votre rendez-vous prévu à la date suivante :</p>
            <p><strong>Date et heure :</strong> {{ $date }}</p>

            <p>Nous vous remercions pour votre confiance et restons à votre disposition pour toute question.</p>
            
<!--             <a href="#" class="btn">Confirmer le rendez-vous</a>
 -->        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} Immobilier. Tous droits réservés.<br>
            <small>Pour toute assistance, contactez-nous à <a href="mailto:support@immobilier.com" style="color: #ffffff; text-decoration: underline;">support@immobilier.com</a></small>
        </div>
    </div>
</body>
</html>
