<!DOCTYPE html>
<html>
<head>
    <title>Manage Availability</title>
</head>
<body>

    <h1>Manage Availability</h1>

    <form method="post" action="">

        <label>Available Day:</label><br>
        <select name="day" required>
            <option value="">Select a day</option>
            <option>Saturday</option>
            <option>Sunday</option>
            <option>Monday</option>
            <option>Tuesday</option>
            <option>Wednesday</option>
            <option>Thursday</option>
            <option>Friday</option>
        </select>

        <br><br>

        <label>Start Time:</label><br>
        <input type="time" name="start_time" required>

        <br><br>

        <label>End Time:</label><br>
        <input type="time" name="end_time" required>

        <br><br>

        <input type="submit" value="Save Availability">

    </form>

    <br>

    <a href="index.php">Back to Dashboard</a>

</body>
</html>