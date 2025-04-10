<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Job Monitor</title>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
</head>

<body>
    <h2>📡 Listening for Job Updates...</h2>

    <script>
        const jobId = prompt("Enter Job ID to listen to:");
        if (!jobId) {
            alert("❌ No Job ID provided.");
            throw new Error("No Job ID");
        }

        Pusher.logToConsole = true;

        const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
            cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
            forceTLS: true
        });

        const channel = pusher.subscribe(`job.${jobId}`);

        channel.bind('progress-updated', function(data) {
            console.log("📦 Progress Updated", data);
            // alert(`✅ Chunk #${data.chunkIndex} processed (${data.linesCount} lines)`);
        });

        channel.bind('job-completed', function(data) {
            console.log("🎉 Job Completed", data);
            // alert(`🎉 Job #${data.jobId} is complete!`);
        });
    </script>
</body>

</html>
