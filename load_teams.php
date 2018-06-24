<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 07/06/2018
 * Time: 12:04
 */

// Do we have both parameters that we need and are they valid
if($_REQUEST['competition_id']>0) {

    // Get team
    $curl = curl_init("https://www.footballwebpages.co.uk/teams.json?comp=".$_REQUEST['competition_id']);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

    $team_json = curl_exec($curl);

    // Where there any errors?
    if (curl_errno($curl)) {
        echo 'Error:' . curl_error($curl);
        exit;
    }

    curl_close($curl);


    // Decode the data into something we can use - php array
    $Team = json_decode($team_json, TRUE);


    // Lets do some validation to make sure we've got back what we expected...make sure we fail gracefully
    if (!is_array($Team)) {
        echo '<p>Sorry, something went wrong whilst looking up the teams in your selected Football League. We\'re looking into the issue now.</p>';
        mail('jack_akrigg12@hotmail.com', 'Soccer Team API failed', $team_json);
        // Could also log this in the DB
        exit;
    }


    // Only need the teams from the multi-dimensional array
    $Team = $Team['teams']['team']; ?>



    <label>Pick a team:</label>
    <select name="team_id" id="team_id" onchange="loadFixtures(); return false;">
        <option value=""> .. Pick a team ..</option>
        <?php foreach ($Team as $TeamData){ ?>
            <option value="<?=$TeamData['id'];?>"><?=$TeamData['name'];?></option>
        <?php } ?>
    </select>


    <?php

}
else {

    echo '<p>Sorry, something went wrong whilst looking up the teams in your selected Football League. We\'re looking into the issue now.</p>';

}

