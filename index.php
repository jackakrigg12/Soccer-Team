<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 07/06/2018
 * Time: 12:04
 */


// Get competition
$curl = curl_init("https://www.footballwebpages.co.uk/competitions.json");

curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

$comp_json = curl_exec($curl);

// Where there any errors?
if(curl_errno($curl)) {
    echo 'Error:' . curl_error($curl);
    exit;
}

curl_close($curl);


// Decode the data into something we can use - php array
$Competition = json_decode($comp_json, TRUE);

// Lets do some validation to make sure we've got back what we expected...make sure we fail gracefully
if(!is_array($Competition)){
    echo '<p>Sorry, something went wrong whilst looking up the Football Leagues. We\'re looking into the issue now.</p>';
    mail('jack_akrigg12@hotmail.com','Soccer Competition API failed', $comp_json);
    // Could also log this in the DB
    exit;
}


// Only need the competitions from the multi-dimensional array
$Competition = $Competition['competitions']['competition'];


// reorder them by the competition id
usort($Competition, function($a, $b) {
    return $a['id'] <=> $b['id'];
});


// Only need the top 7 leagues
$Competition = array_slice($Competition, 0, 7);


// DEBUG - SHOW ME WHAT YOU GOT!
//echo "<pre>".print_r($Competition,1)."</pre>";
//exit;
//$Competition['id'];
//$Competition['name']; ?>


<html>
<head>

    <script>

        // AJAX in the teams
        function loadTeams() {

            var competition_id = document.getElementById("competition_id").value;

            if (competition_id == "") {
                document.getElementById('team_wrapper').innerHTML = '<p>Please select a league.</p>';
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("team_wrapper").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "load_teams.php?competition_id=" + competition_id, true);
                xmlhttp.send();
            }
        }

        // AJAX in the fixtures
        function loadFixtures() {

            var competition_id = document.getElementById("competition_id").value;
            var team_id = document.getElementById("team_id").value;

            if ( competition_id == "" || team_id == "") {
                document.getElementById('fixture_wrapper').innerHTML = '<h2>Latest Result</h2><p>Please select a league and team.</p>';
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("fixture_wrapper").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "load_fixtures.php?competition_id=" + competition_id + "&team_id=" + team_id, true);
                xmlhttp.send();
            }
        }

        function clearFixture(){
            document.getElementById('fixture_wrapper').innerHTML = '<h2>Latest Result</h2><p>Please select a league and team.</p>';
        }

    </script>

    <style>
        body {
            background-color: #333;
            color: white;
            text-align: center;
            font-family: Calibri;
        }

        #filter_wrapper, #fixture_wrapper  {
            background-color: #262626;
            width: 40%;
            height: auto;
            padding: 20px;
            margin: 0 auto;
            margin-bottom: 50px;
        }

        h1 {
            margin: 35px 0;
        }

    </style>

</head>
<body>

    <h1>Find out how your team got on in their latest fixture...</h1>


    <div id="filter_wrapper">

        <div id="league_wrapper">
            <h2>Start by selecting a league from below:</h2>
            <form>
                <label>Pick a League:</label>
                <select name="competition_id" id="competition_id" onchange="loadTeams(); clearFixture(); return false;">
                    <option value=""> .. Pick a league .. </option>
                    <?php foreach ($Competition as $CompetitionData){ ?>
                        <option value="<?=$CompetitionData['id'];?>"><?=$CompetitionData['name'];?></option>
                    <?php } ?>
                </select>
            </form>
        </div>

        <!-- load in the team filter depending on competition selected -->
        <div id="team_wrapper"></div>

    </div>


    <!-- load in the latest fixture information depending on team and competition selected -->
    <div id="fixture_wrapper">
        <h2>Latest Result</h2>
        <p>Please select a league and team.</p>
    </div>


</body>
</html>
