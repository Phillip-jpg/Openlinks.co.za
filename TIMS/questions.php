<!DOCTYPE html>
<html>
<head>
    <title>Question Weightings</title>
    <script>
        function calculateTotalWeight() {
            var weights = document.getElementsByName("weight[]");
            var totalWeight = 0;

            for (var i = 0; i < weights.length; i++) {
                var weight = parseInt(weights[i].value);
                if (!isNaN(weight)) {
                    totalWeight += weight;
                }
            }

            document.getElementById("totalWeight").textContent = totalWeight;
        }
    </script>
</head>
<body>
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
</body>
</html>
