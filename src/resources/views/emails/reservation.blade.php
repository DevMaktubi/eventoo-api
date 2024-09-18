<!DOCTYPE html>
<html>
<head>
    <title>Confirmação de Reserva</title>
</head>
<body>
    <h1>Detalhes da sua Reserva</h1>
    <p><strong>Evento:</strong> {{ $reservation->event->name }}</p>
    <p><strong>Descrição do Evento:</strong> {{ $reservation->event->description }}</p>
    <p><strong>Data:</strong> {{ $reservation->event->event_date }}</p>
    <p><strong>Quantidade de convites reservados:</strong> {{ $reservation->seats_reserved }}</p>

    <p>Obrigado por reservar com a gente!</p>
</body>
</html>
