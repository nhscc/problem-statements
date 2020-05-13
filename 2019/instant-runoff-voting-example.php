<?php
    # This took under two hours to theorize and then code from scratch with my
    # pretty rusty PHP knowledge (I do C and JavaScript these days).

    # The relevant PHP code is only 80 lines without the comments and
    # explanations. The IRV logic by itself is only 40 lines by the same metric.
    # The rest of this code is there to make the nice helpful buttons in the UI
    # work their magic.

    # What follows is also an example of what might score a team a 3 for code
    # organization and readability on the rubric. Note the tabbing, spacing, and
    # comments.

    # You can run this example locally without Apache or any server by running
    # the following in the Windows command line or Linux shell:
    #
    # php -S localhost:8080 instant-runoff-voting-example.php
    #
    # You can access the resulting page by navigating your browser to
    # http://localhost:8080 (or click the link now if you're using a modern IDE)

    const NUMBER_OF_CHOICES = 5;
    const WINNER_THRESHOLD_PERCENT = 0.5;
    const STATE_GENERATION_UPPER_BOUND = 1000;

    session_start();
    $winningChoices = [];
    $state = NULL;
    $explanation = [];

    # Note that we want the results of calculating the winner to be explained
    if(isset($_POST['explain']))
        $_POST['calculate-winner'] = TRUE;

    # Ensure $_SESSION['vote_cache'] array exists
    if(!isset($_SESSION['vote_cache']) || isset($_POST['reset-state']) || isset($_POST['generate-state']))
        $_SESSION['vote_cache'] = [];

    # Assumption: by this point, $_SESSION['vote_cache'] array definitely exists

    # Generate random "database" state
    if(isset($_POST['generate-state']))
    {
        # Select a random number X of votes between 10 and
        # STATE_GENERATION_UPPER_BOUND
        $numberOfVotesToGenerate = random_int(10, STATE_GENERATION_UPPER_BOUND);

        # Get a list of possible ranks from 1 to NUMBER_OF_CHOICES (inclusive)
        $arrayOfChoices = range(1, NUMBER_OF_CHOICES);

        # Use that list to add X *random* votes to our "database"
        while($numberOfVotesToGenerate--)
        {
            shuffle($arrayOfChoices);

            $localArrayOfChoices = array_flip($arrayOfChoices);

            # Make sure the keys are properly formatted with the "choice-"
            # prefix (i.e. "choice-X")
            foreach($localArrayOfChoices as $key => &$value)
                $value = 'choice-' . (((int) $value) + 1);

            $localArrayOfChoices = array_flip($localArrayOfChoices);
            $_SESSION['vote_cache'][] = $localArrayOfChoices;
        }

        # And we want to show the user our changes...
        $_POST['show-state'] = TRUE;
    }

    # Reset our "database" state to 0
    if(isset($_POST['reset-state']))
        echo '<p class="success">Session state reset successfully!</p>';

    # Show the raw state of our "database"
    if(isset($_POST['show-state']))
        $state = print_r($_SESSION['vote_cache'], TRUE);

    # A new vote is incoming. Let's record it!
    if(isset($_POST['add-votes']))
    {
        # Validate incoming ranking
        # i.e. 0 is bad, empty/!isset is bad, and non-unique ranks are bad
        if(empty($_POST['choice-1']) ||
           empty($_POST['choice-2']) ||
           empty($_POST['choice-3']) ||
           empty($_POST['choice-4']) ||
           empty($_POST['choice-5']) ||
           count(array_unique([$_POST['choice-1'], $_POST['choice-2'], $_POST['choice-3'], $_POST['choice-4'], $_POST['choice-5']])) != NUMBER_OF_CHOICES)
        {
            echo '<p class="error">Invalid voting attempt!</p>';
        }

        else
        {
            # Add the ranking to our "database"
            $_SESSION['vote_cache'][] = [
                'choice-1' => $_POST['choice-1'],
                'choice-2' => $_POST['choice-2'],
                'choice-3' => $_POST['choice-3'],
                'choice-4' => $_POST['choice-4'],
                'choice-5' => $_POST['choice-5'],
            ];

            # Confirm to user that their tally was received
            echo '<p class="success">Voting successful!</p>';
        }
    }

    # With the way I calculate the winner (below), the logic actually supports a
    # variable number of choices even though I hard-coded in five choices above
    # to keep the example simple and understandable. It would be almost trivial
    # to expand the above to a dynamic number of choices as was required in the
    # problem statement.

    # The user has requested we take the current votes and calculate a winner!
    else if(isset($_POST['calculate-winner']))
    {
        if(!empty($_SESSION['vote_cache']))
        {
            # Assumption: by this point, the "database" must exist AND cannot be
            # empty

            # Copy the "database" into another array so we don't accidentally
            # change it when we start doing fun stuff
            $votes = $_SESSION['vote_cache'];

            while(true)
            {
                # Prepare to begin tallying the number of rank-1 votes for each
                # choice
                $roundTally = array_fill_keys(array_keys($votes[0]), 0);

                # Tally the rank-1 votes
                foreach($votes as $vote)
                {
                    $ar = array_flip($vote);
                    ksort($ar);
                    $roundTally[reset($ar)]++;
                }

                $explanation[] = '> Runoff begins with choice counts: ' . print_r($roundTally, TRUE);

                $totalVotes = array_sum($roundTally);
                $votes50Percent = $totalVotes * WINNER_THRESHOLD_PERCENT;

                $maxTally = max($roundTally);
                $minTally = min($roundTally);

                $explanation[] = "> Total number of votes: $totalVotes";
                $explanation[] = "> 50% mark: >$votes50Percent";
                $explanation[] = "> Highest rank-1 tally: $maxTally";
                $explanation[] = "> Lowest rank-1 tally: $minTally";

                # Did somebody win (or did everyone tie)?
                if($maxTally > $votes50Percent || $maxTally == $minTally)
                {
                    $explanation[] = '=> Someone either won or tied...';

                    foreach($roundTally as $choice => $tally)
                    {
                        # If this is a winning choice, record it and how much
                        # it won by
                        if($tally == $maxTally)
                        {
                            $explanation[] = "==> Winning choice found: $choice (by $tally)";
                            $winningChoices[] = [$choice, $tally];
                        }
                    }

                    $explanation[] = '==> Chosen winners: ' . print_r($winningChoices, TRUE);

                    # Break out of the infinite while loop
                    $explanation[] = '-- DONE --';
                    break;
                }

                else
                {
                    $losingChoices = [];
                    $explanation[] =
                        "-> No winner this runoff. Commencing elimination of choices by lowest rank-1 tally";

                    # Which choice(s) have the least votes?
                    foreach($roundTally as $choice => $tally)
                    {
                        # If this is a losing choice, schedule it for
                        # elimination
                        if($tally == $minTally)
                        {
                            $losingChoices[] = $choice;
                            $explanation[] = "--> Target for elimination found: $choice (by $tally)";
                        }
                    }

                    $explanation[] = '--> Eliminating targets: ' . print_r($losingChoices, TRUE);

                    # Eliminate the choice(s) with the least votes from all vote
                    # arrays in our "database"
                    foreach($votes as &$vote_ref) # XXX: Be careful reusing variable names as references!
                    {
                        foreach($losingChoices as $losingChoice)
                           unset($vote_ref[$losingChoice]);
                    }

                    # Begin the loop again!
                    $explanation[] = '(looping again)';
                }
            }
        }
    }
?>
<h1>Instant-Runoff Voting Example (<?= count($_SESSION['vote_cache']) ?> total votes cast)</h1>
<h3>What choice is the best choice?</h3>
<form method="POST" action="#">
    <div>
        <label>Choice 1</label>
        <select name="choice-1">
            <option selected="selected" disabled="disabled" value="0">Rank this choice</option>
            <option value="1">Rank 1</option>
            <option value="2">Rank 2</option>
            <option value="3">Rank 3</option>
            <option value="4">Rank 4</option>
            <option value="5">Rank 5</option>
        </select>
    </div>
    <div>
        <label>Choice 2</label>
        <select name="choice-2">
            <option selected="selected" disabled="disabled" value="0">Rank this choice</option>
            <option value="1">Rank 1</option>
            <option value="2">Rank 2</option>
            <option value="3">Rank 3</option>
            <option value="4">Rank 4</option>
            <option value="5">Rank 5</option>
        </select>
    </div>
    <div>
        <label>Choice 3</label>
        <select name="choice-3">
            <option selected="selected" disabled="disabled" value="0">Rank this choice</option>
            <option value="1">Rank 1</option>
            <option value="2">Rank 2</option>
            <option value="3">Rank 3</option>
            <option value="4">Rank 4</option>
            <option value="5">Rank 5</option>
        </select>
    </div>
    </div>
    <div>
        <label>Choice 4</label>
        <select name="choice-4">
            <option selected="selected" disabled="disabled" value="0">Rank this choice</option>
            <option value="1">Rank 1</option>
            <option value="2">Rank 2</option>
            <option value="3">Rank 3</option>
            <option value="4">Rank 4</option>
            <option value="5">Rank 5</option>
        </select>
    </div>
    </div>
    <div>
        <label>Choice 5</label><!-- Try adding or removing some choices and modding the PHP above a little bit! -->
        <select name="choice-5">
            <option selected="selected" disabled="disabled" value="0">Rank this choice</option>
            <option value="1">Rank 1</option>
            <option value="2">Rank 2</option>
            <option value="3">Rank 3</option>
            <option value="4">Rank 4</option>
            <option value="5">Rank 5</option>
        </select>
    </div>
    <br />
    <div>
        <input type="submit" name="add-votes" value="Add Vote" />
        <input type="submit" name="calculate-winner" value="Calculate Winner" />
        <input type="submit" name="generate-state" value="Generate State" />
        <input type="submit" name="reset-state" value="Reset State" />
        <input type="submit" name="show-state" value="Show State" />
        <?= isset($_POST['calculate-winner']) ? '<input type="submit" name="explain" value="Explain Election Results" />' : '' ?>
    </div>
</form>

<?php
    # Did someone attempt to calculate winners?! Report the results!
    if(isset($_POST['calculate-winner']))
    {
?>
<hr />
<h2>ELECTION RESULTS!</h2>
<?php
        if(empty($winningChoices))
            echo '<p>There were no winners... (wtf?)</p>';

        else
        {
            # Let's build the results string with respect to English grammar...

            $grammar = count($winningChoices) == 1 ? ' is' : 's are';
            $winnerString = [];

            foreach ($winningChoices as $winningChoice)
                $winnerString[] = $winningChoice[0] . " (by {$winningChoice[1]} votes)";

            $winnerString = str_replace('###', 'and', trim(implode(' ### ', $winnerString), ' #'));

            echo "<p>The winner$grammar: <span class=\"bold\">{$winnerString}</span></p>";
        }
    }
?>

<?php
    # Display an explanation of the election results as an unordered list
    if(isset($_POST['explain'])):
?>
<h3>EXPLANATION OF ELECTION</h3>
<ul>
<?php foreach ($explanation as $line): ?>
    <li><?= $line ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php
    # Display the contents of the "database"
    if(isset($state)):
?>
<hr />
<h2>CONTENTS OF VOTER "DATABASE"</h2>
<pre><?= $state ?></pre>
<?php endif; ?>

<style>
    body {
        font-family: monospace;
    }

    .bold, .error {
        font-weight: bold;
    }

    .error {
        color: red;
    }

    .success {
        color: green;
    }

    ul {
        list-style-type: none;
    }
</style>
