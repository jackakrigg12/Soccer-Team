<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 07/06/2018
 * Time: 12:04
 */

// Do we have both parameters that we need and are they valid
if($_REQUEST['competition_id']>0 && $_REQUEST['team_id']>0) {

    // Get the fixture
    $curl = curl_init("https://www.footballwebpages.co.uk/fixtures-results.json?comp=".$_REQUEST['competition_id']."&team=".$_REQUEST['team_id']."&results=1");

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

    $fixture_json = curl_exec($curl);

    // Where there any errors?
    if(curl_errno($curl)) {
        echo 'Error:' . curl_error($curl);
        exit;
    }

    curl_close($curl);

    // Decode the data into something we can use - php array
    $LatestFixture = json_decode($fixture_json, TRUE);

    // Lets do some validation to make sure we've got back what we expected...make sure we fail gracefully
    if(!is_array($LatestFixture)){
        echo 'Sorry, something went wrong whilst looking up the latest fixture for your selected team. We\'re looking into the issue now.';
        mail('jack_akrigg12@hotmail.com','Soccer Fixture API failed', $fixture_json);
        // Could also log this in the DB
        exit;
    }

    // Only need the fixture details multi-dimensional array
    $LatestFixture = $LatestFixture['matchesCompetition']['match'][0]; ?>

    <h2>Latest Result</h2>

    <p><strong>FT: </strong><?=$LatestFixture["homeTeamName"];?> <?=$LatestFixture["homeTeamScore"];?> - <?=$LatestFixture["awayTeamScore"];?> <?=$LatestFixture["awayTeamName"];?></p>
    <p><strong>Date: </strong><?=$LatestFixture["date"];?></p>
    <p><strong>HT: </strong><?=$LatestFixture["homeTeamHalfTimeScore"];?> - <?=$LatestFixture["awayTeamHalfTimeScore"];?></p>
    <p><strong>Attendance: </strong><?=$LatestFixture["attendance"];?></p>

    <?php
}
else {

    echo '<p>Sorry, something went wrong whilst looking up the latest fixture for your selected team. We\'re looking into the issue now.</p>';

}

