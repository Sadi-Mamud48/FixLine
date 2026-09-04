<!DOCTYPE html>
<html>
<head>
    <title>Apply for Job</title>
</head>
<body>

    <h1>Apply for Repair Job</h1>

    <form method="post" action="">

        <label>Job ID:</label><br>
        <input type="text" name="job_id" required><br><br>

        <label>Proposed Service Date:</label><br>
        <input type="date" name="service_date" required><br><br>

        <label>Proposed Service Time:</label><br>
        <input type="time" name="service_time" required><br><br>

        <label>Estimated Cost:</label><br>
        <input type="number" name="estimated_cost" min="0" required><br><br>

        <label>Experience / Description:</label><br>
        <textarea name="experience" rows="5" cols="40" required></textarea><br><br>

        <label>Message to Customer:</label><br>
        <textarea name="message" rows="5" cols="40" required></textarea><br><br>

        <input type="submit" value="Submit Application">

    </form>

    <br>

    <a href="available-jobs.php">Back to Available Jobs</a>

</body>
</html>
