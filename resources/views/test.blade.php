<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = true;

        const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
            cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}"
        });

        const channel = pusher.subscribe('city-degress');
        channel.bind('test-event', function(data) {
            alert('📡 Received message: ' + data.message);
        });
    </script>
</head>

<body>
    <h2>📺 Listening to test-event on test-channel....</h2>
</body>

</html>
