<!DOCTYPE html>
<html>
<head>
    <title>Scorecard</title>
</head>
<body>
    <h1>Scorecard</h1>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <legend>Scorecard Details</legend>
            <div>
                <label>Name:</label>
                <input type="text" name="name" required>
            </div>
            <div>
                <label>Supporting Doc:</label>
                <input type="text" name="supportingDoc" required>
            </div>
            <div>
                <label>Weighting (%):</label>
                <input type="number" name="weighting" min="0" max="100" required>
            </div>
            <div>
                <label>Questions:</label>
                <a href=""></a>
            </div>
            <br>
            <input type="submit" name="submit" value="Submit">
        </fieldset>

        <h1>Question Weightings</h1>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <legend>Add Questions</legend>
            <div id="questions">
                <div>
                    <label>Question:</label>
                    <input type="text" name="question[]" required>
                    <label>Weighting %:</label>
                    <input type="number" name="weight[]" min="0" max="100" required oninput="calculateTotalWeight()">
                    <button type="button" onclick="addQuestion()">Add More</button>
                </div>
            </div>
            <br>
            <input type="submit" name="submit" value="Submit">
            <p>Your Weightings must add up to a total of 100%</p>
        </fieldset>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $questions = $_POST['question'];
        $weights = $_POST['weight'];
        $totalWeight = 0;

        if (count($questions) == count($weights)) {
            for ($i = 0; $i < count($questions); $i++) {
                $question = $questions[$i];
                $weight = $weights[$i];
                $totalWeight += $weight;

                echo "<p>Question: $question | Weighting: $weight</p>";
            }
        }

        echo "<h3>Total Weightings: $totalWeight</h3>";
    }
    ?>


    <script>
        function addQuestion() {
            var container = document.getElementById("questions");
            var div = document.createElement("div");

            div.innerHTML = '<label>Question:</label> <input type="text" name="question[]" required> <label>Weighting %:</label> <input type="number" name="weight[]" min="0" max="100" required oninput="calculateTotalWeight()"> <button type="button" onclick="removeQuestion(this)">Remove</button>';

            container.appendChild(div);
        }

        function removeQuestion(element) {
            var div = element.parentNode;
            div.parentNode.removeChild(div);
            calculateTotalWeight(); 
        }
    </script>
    </form>
<br>
<br>
<br>
    <div>
                <label>Questions:</label>
                <a href="questions.php">Click here to add questions</a>
    </div>
    <?php
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $supportingDoc = $_POST['supportingDoc'];
        $weighting = $_POST['weighting'];
        $questions = $_POST['questions'];

        // Save the data in a CSV file
        $data = array($name, $supportingDoc, $weighting, $questions);
        $file = fopen('scorecard.csv', 'a'); // Open the file in append mode
        fputcsv($file, $data); // Write the data to the file
        fclose($file); // Close the file

        echo "<h2>Scorecard Details:</h2>";
        echo "<p><strong>Name:</strong> $name</p>";
        echo "<p><strong>Supporting Doc:</strong> $supportingDoc</p>";
        echo "<p><strong>Weighting (%):</strong> $weighting</p>";
        echo "<p><strong>Questions:</strong> $questions</p>";
    }

    if (isset($_POST['clear'])) {
        // Clear the scorecard by deleting the CSV file
        if (file_exists('scorecard.csv')) {
            unlink('scorecard.csv');
            echo "<p>Scorecard cleared successfully.</p>";
        }
    }
    ?>

    <h2>Saved Scorecard Details:</h2>
    <?php
    // Display the saved scorecard details from the CSV file
    if (($file = fopen("scorecard.csv", "r")) !== FALSE) {
        echo "<table>";
        echo "<tr><th>Name</th><th>Supporting Doc</th><th>Weighting (%)</th><th>Questions</th></tr>";

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            echo "<tr>";
            echo "<td>".$data[0]."</td>";
            echo "<td>".$data[1]."</td>";
            echo "<td>".$data[2]."</td>";
            echo "<td>".$data[3]."</td>";
            echo "</tr>";
        }

        echo "</table>";
        fclose($file);
    }
    ?>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="submit" name="clear" value="Clear Scorecard">
    </form>

</body>
</html>
