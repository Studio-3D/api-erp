<!DOCTYPE html>
<html>
<head>

</head>
<body>

<p>Votre Réclamation de : <br/>
    Objet:{{$objet_rec}}<br/>
    @if($etat==1) Est Résolu @elseif($etat==2) Non Résolu @else En cours de Traitement @endif <br/>
    Commentaire:{{$comment}}
</p>

</body>
</html>
